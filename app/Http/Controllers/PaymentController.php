<?php

namespace App\Http\Controllers;

use App\Http\Livewire\PaymentPage;
use App\Http\Requests\authorizeCardWithAvsRequest;
use App\Http\Requests\authorizeCardWithOtpRequest;
use App\Http\Requests\authorizeCardWithPinRequest;
use App\Http\Requests\BankTransferRequest;
use App\Http\Requests\CardTransferRequest;
use App\Http\Requests\ChargeAndTotalRequest;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\InvoiceCollection;
use App\Lib\Services\Blusalt;
use App\Models\Gateway;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserSettings;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public const successful = "SUCCESSFUL";
    public const failed = "FAILED";
    public const pending = "PENDING";

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        //
        $data['title'] = "Payment Gateways";

        return view('admin.gateways.index', $data);
    }

    public function paymentPage($id)
    {
        /** @var Invoice $invoice */
        $invoice = $this->checkIfInvoiceExist($id);

        $data['title'] = "Payment Page";
        $data['invoice'] = $invoice;
        $merchRef = $invoice->transaction->merchant_transaction_ref;
        //declare other variables for the payment page;
        $merchantGatewayDetails = $this->getMerchantGatewayDetails($invoice);
        $tranx_details = $invoice->transaction->details;
        //check for UIGatewayRules;
        [$merchantGatewayDetails, $details] = $this->uiGatewayRules($invoice, $merchRef, $merchantGatewayDetails);
        if (!$details['status']) {
            //redirect to information page showing student should make payment
            return view('invoice.notavailable', [
                'message' => $details['message']
            ]);
        }

        //only show payment page when invoice is pending
        if ($invoice->status !== "pending") {
            //redirect to payment page;
            return redirect()->route('receipt', ['id' => $id])->with('status', 'Invoice Paid!');
        }

        if (isset($tranx_details['channel'])) {
            if (array_key_exists($tranx_details['channel'], $merchantGatewayDetails)) {
                $temp[$tranx_details['channel']] = $merchantGatewayDetails[$tranx_details['channel']];
                $merchantGatewayDetails = $temp;
            }
        }
        $data['merchantGateways'] = $merchantGatewayDetails;
        $data['activeTab'] = array_key_first($merchantGatewayDetails);
        $merchantSettings = UserSettings::firstWhere('user_id', $invoice->user->id);
        $data['merchantSettings'] = $merchantSettings;
        $data['merchantAvatar'] = false;
        if ($merchantSettings) {
            $data['merchantAvatar'] = $merchantSettings->values['avatar'] ?? null;
        }

        return view('payment_page', $data);

    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function checkIfInvoiceExist($id)
    {
        //check if Invoice exists;
        $invoice = Invoice::where('invoice_no', $id)->with(['transaction', 'gateway'])->first();

        if (!$invoice) {
            abort(404);
        }
        return $invoice;
    }


    /**
     * @param $invoice
     * @return array
     */
    public function getMerchantGatewayDetails($invoice): array
    {
        $mGateways = $invoice->user->usergateway;
        $merchantGateways = $mGateways->config_details ?? null;

        $freshArr = [];

        if ($merchantGateways) {
            array_walk($merchantGateways, function ($item, $key) use (&$freshArr, $invoice) {

                if ($item['status']) {

                    if (strtolower($item['name']) === "card") {
                        //check if percentage is set use flwavePercent Channel;
                        if ((int)$item['customer_service']['charge_factor'] === 1) {
                            $item['flwave_percent'] = true;
                        }
                        $item = $this->setCardMerchantCharge($invoice, $item);
                    }
                    $item['gateway_id'] = $key;
                    $item["invoiceCharge"] = $item['customer_service']['charge_factor'] ? ($item['customer_service']['charge'] / 100) * $invoice->amount : $item['customer_service']['charge'];
                    $item["invoiceTotal"] = $invoice->amount + $item['invoiceCharge'];
                    //check if it's intlPayment
                    if (strtoupper($invoice->transaction->currency) !== "NGN") {
                        if (str_replace(' ', '', strtolower($item['name'])) === "intcard") {
                            $item['name'] = $freshArr['card']['name'];
                            $item['gateway_id'] = $freshArr['card']['gateway_id'];
                        }
                    }
                    $freshArr[str_replace(' ', '', strtolower($item['name']))] = $item;

                }
            });

        }

        return $freshArr;
    }

    public function setCardMerchantCharge(Invoice $invoice, $item)
    {
        //above 22500 => flatrate
        if ($invoice->amount <= 22500) {
            $item['flwave_percent'] = true;
        }
        return $item;

    }

    public function receipt($id)
    {
        /** @var Invoice $invoice */
        $invoice = $this->checkIfInvoiceExist($id);

        $data['title'] = "Payment Receipt";

        if (isset($invoice)) {
            $data['invoice'] = $invoice;
            /** @var Transaction $transaction */
            $transaction = $invoice->transaction;

            $data['transaction'] = $transaction;
            $data['redirect'] = false;

            if ($transaction->status === "successful") {
                if (isset($transaction->redirect_url)) {
                    if ($transaction->user_id != 3) {
                        $data['redirect'] = true;
                        $urlQuery = $transaction->transactionToPayload();
                        $url = $transaction->redirect_url;
                        $data['redirect_url'] = $url . "?" . http_build_query($urlQuery);
                    }
                }
            }

        }


        return view('payment_receipt', $data);


    }

    public function createPaymentRequest(PaymentRequest $request)
    {

        $currency = $request->currency;
        $trn_details = [];
        $trn_channelId = $request->channel;

        $channel = $request->input('channel');
        if (!empty($channel)) {
            $trn_details['channel'] = $channel;
        }


        /** @var User $user */
        $user = $request->user();

        //request passed create Invoice and return link;
        $invoice = null;
        $request_id = $request->request_id;

        //check if invoice Exists;
        $transaction = Transaction::firstWhere('merchant_transaction_ref', $request_id);
        if ($transaction) {
            //check for UI merchant and apply custom rule;
            if (in_array($user->id, explode(',', config('app.skip_duplicate_create_payment_request_merchants')))) {
                return $this->UIpaymentRule($transaction, $request);
            }
            $error = [
                "request_id" => ["Payment Request already Exists, Please Use a Unique Request ID!"],
            ];
            return response()->json(errorResponseJson('Payment Request Failed', $error), 404);

        }

        DB::transaction(function () use ($trn_channelId, $request, $request_id, $user, $trn_details, $currency, &$invoice) {
            $redirect_url = $request->redirect_url;
            $amount = $request->amount;
            /** @var Invoice $invoice */
            $invoice = $user->invoice()->create([
                'invoice_no' => 'INV' . $request_id,
                'quantity' => 1,
                'customer_email' => $request->email,
                'customer_name' => $request->full_name,
                'due_date' => Carbon::now()->addDays(7),
                'amount' => $amount,
                'name' => $request->name,
            ]);

            $uuid = Str::orderedUuid();
            //check if merchantRedirectURL is set and add it ;
            if (isset($redirect_url)) {
                $trn_details['redirect_url'] = $redirect_url;
                $trn_details['full_name'] = $request->full_name;
            }

            $invoice->transaction()->create([
                "transaction_ref" => $uuid,
                "user_id" => $invoice->user_id,
                "merchant_transaction_ref" => $request_id ?? $uuid,
                "status" => "pending",
                "type" => $request->service_type ?? "N/A",
                "amount" => $amount,
                "total" => $amount,
                'details' => $trn_details,
                "flag" => "debit",
                "currency" => $currency,
                "gateway_id" => $trn_channelId,
                "redirect_url" => $redirect_url
            ]);
        });

        $data = new InvoiceCollection($invoice);
        //set flag to indicate it's a paymentRequest;
        $request->merge(['paymentRequest' => true]);

        return response()->json(['status' => true, "message" => "Payment Request Successful", "data" => $data,]);

    }

    public function updatePaymentRequest(Request $request)
    {
        $request->validate([
            "name" => "required",
            "amount" => ["required", "numeric", "min:100"],
            "email" => "required",
            'request_id' => ["required", "min:5"],
            "redirect_url" => ["sometimes", "url"]

        ], $request->all());



        //request passed create Invoice and return link;
        $data = "";
        $request_id = $request->request_id;

        //check if invoice Exists;
        /** @var Transaction $transaction */
        $transaction = Transaction::firstWhere('merchant_transaction_ref', $request_id);
        $message = ['status' => false, "message" => "Payment Request Not Found", "data" => $data,];

        if ($transaction) {
            $tStatus = strtoupper($transaction->status);
            $message = ['status' => false, "message" => "Payment Request already $tStatus", "data" => $data,];

            //only allow update on pending transactions
            if ($tStatus === "PENDING") {
                DB::transaction(function () use ($request, $transaction, &$data) {
                    //update invoice;

                    /** @var Invoice $invoice */
                    $invoice = $transaction->invoice;
                    $invoice->update([
                        'customer_email' => $request->email,
                        'customer_name' => $request->full_name,
                        'due_date' => Carbon::now()->addDays(7),
                        'amount' => $request->amount,
                        'name' => $request->name,
                    ]);

                    //update transaction;
                    $transaction->update([
                        "amount" => $request->amount,
                        "redirect_url" => $request->redirect_url,
                        "total" => $request->amount
                    ]);
                    //set flag to indicate it's a paymentRequest;
                    $request->attributes->set('paymentRequest', true);
                    $data = new InvoiceCollection($invoice);
                });
                $message = ['status' => true, "message" => "Payment Request Updated", "data" => $data,];


            }

        }


        return response()->json($message);

    }

    /**
     * @param $id //Invoice ID/ Transaction Id;
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \JsonException
     */
    public function validateCardPayment($id, Request $request)
    {
        /** @var Invoice $invoice */
        $invoice = $this->checkIfInvoiceExist($id);
        if ($invoice) {
            /** @var Transaction $transaction */
            $transaction = $invoice->transaction;
            $details = ['flag' => self::pending];
            $payment_provider_message = null;
            $trnx_details = [];

            //make sure invoice status is not successful;
            if (strtoupper($invoice->status) !== self::successful) {

                $provider = strtoupper($transaction->provider);

                if ($provider === "BLUSALT") {
                    list($details, $payment_provider_message, $trnx_details) = $this->validateBlusaltRedirect($transaction, $details);
                }

                if (in_array($provider, ['FLUTTERWAVE', 'FLWAVEPERCENT', 'FLWAVEFLAT'])) {
                    list($details, $payment_provider_message, $trnx_details) = $this->validateFlutterwaveRedirect($request, $provider, $details);
                }

                $gateway = Gateway::select(['id', 'name'])->where('name', "Card")->first();

                //Transaction Successful;
                if (strtoupper($details['flag']) === self::successful) {
                    /** @var User $user */
                    $user = $transaction->user;

                    /** @var Wallet $wallet */
                    $wallet = $user->wallet;

                    $company = company();

                    $transaction->handleSuccessfulPayment($transaction, $gateway->id, $payment_provider_message, $trnx_details, $wallet, $user, $company);
                }

                if (strtoupper($details['flag']) === self::failed) {
                    $transaction->handleFailedPayment($transaction, $gateway->id, $payment_provider_message, $trnx_details);
                }

            }

        }
        //send to receipt page;
        return redirect()->to(route('receipt', ['id' => $id]));

    }

    public function details(Request $request)
    {
        $userId = $request->user()->id;
        $request->validate([
            'request_id' => ["required", "min:5"],
        ], $request->all());
        /** @var Transaction $transaction */
        $transaction = Transaction::firstWhere([
            ['user_id', '=', $userId],
            ['merchant_transaction_ref', '=', $request->request_id]
        ]);
        if (is_null($transaction)) {

            $error = [
                "request_id" => ["The request id does not exist"],
            ];
            return response()->json(errorResponseJson('Invalid Request ID', $error), 404);
        }

        return response()->json(['status' => true, "message" => "Detail Retrieved Successfully", "data" => new InvoiceCollection($transaction),]);

    }

    public function UIpaymentRule(Transaction $transaction, $request)
    {
        $status = strtoupper($transaction->status);

        $isSuccessful = false;
        $message = "Transaction Already Processed";

        if ($status === "FAILED") {
            //change transaction to pending
            logger("Changing transaction {$transaction->merchant_transaction_ref} from $status to PENDING ");
            $transaction->update([
                "status" => "pending"
            ]);
            //change invoice to pending
            $transaction->invoice->update(['status' => 'pending']);
        }
        //check if amount differs and update
        //only allow update on pending transactions
        if ($status === "PENDING") {
            DB::transaction(function () use ($request, $transaction) {
                //update invoice;

                /** @var Invoice $invoice */
                $invoice = $transaction->invoice;
                $invoice->update([
                    'amount' => $request->amount,
                    'customer_email' => $request->email,
                    'customer_name' => $request->full_name,
                    'due_date' => Carbon::now()->addDays(7),
                    'name' => $request->name,
                ]);

                //update transaction;
                $transaction->update([
                    "amount" => $request->amount,
                    "type" => $request->service_type,
                    "total" => $request->amount + $transaction->fee,
                    "redirect_url" => $request->redirect_url
                ]);

            });
            $isSuccessful = true;
            $message = "Payment Request Updated";
        }
        return response()->json(['status' => $isSuccessful, "message" => $message, "data" => [
            "url" => route('payment-page', ['id' => $transaction->invoice_no])
        ]
        ]);

    }

    /**
     * @param Invoice $invoice
     * @param $merchRef
     * @param array $merchantGatewayDetails
     * @return array[]
     */
    protected function uiGatewayRules(Invoice $invoice, $merchRef, array $merchantGatewayDetails): array
    {
        $temp = [];
        $status = true;
        $uiDetails['status'] = $status;
        if ($invoice->user->id === 3) {
            $response = $invoice->statusOnUI();
            $message = "This invoice <b> $merchRef </b> is not available for payment, Kindly generate another record to solve this issue.";
            if ($response) {
                if (!$response['status']) {
                    $status = false;
                }

            }
            if (!$response) {
                $status = false;
                $message = "Oops! Sorry we cannot confirm the status of your invoice $merchRef ! Please try again later.";
            }

            //ui handle only remita;
            if (!str_contains(strtolower(str_replace(" ", "", $invoice->transaction->type)), "undergraduatetranscript")) {
                unset($merchantGatewayDetails['remita']);
            }
            if (str_contains(strtolower(str_replace(" ", "", $invoice->transaction->type)), "undergraduatetranscript")) {
                //use only remita channel;
                if (array_key_exists("remita", $merchantGatewayDetails)) {
                    $temp['remita'] = $merchantGatewayDetails['remita'];
                    $merchantGatewayDetails = $temp;
                }
            }
            $uiDetails['status'] = $status;
            $uiDetails['message'] = $message;

        }
        return array($merchantGatewayDetails, $uiDetails);
    }

    /**
     * @param Transaction $transaction
     * @param array $details
     * @return array
     */
    public function validateBlusaltRedirect(Transaction $transaction, array $details): array
    {
//verify transaction;
        $blusalt = new Blusalt();
        $blusaltPayload = $blusalt->verifyTransaction($transaction->spay_ref);
        if (strtoupper($blusaltPayload['status']) === self::failed) {
            $details['flag'] = strtolower(self::failed);
        }

        if (strtoupper($blusaltPayload['status']) === self::successful) {
            $details['flag'] = strtolower(self::successful);
        }
        $payment_provider_message = $blusaltPayload['metadata']['response']['message'] ?? $blusaltPayload['status'];
        $trnx_details = array_merge($blusaltPayload['metadata']['card'], [
            "narration" => $blusaltPayload['narration'],
            "id" => $blusaltPayload['reference'],
            "tx_ref" => $blusaltPayload['client_reference'],
            "payment_type" => $blusaltPayload['type']
        ]);
        return array($details, $payment_provider_message, $trnx_details);
    }

    /**
     * @param Request $request
     * @param $provider
     * @param $details
     * @return array
     * @throws \JsonException
     */
    public function validateFlutterwaveRedirect(Request $request, $provider, $details): array
    {
        $payload = $request->get('response');
        /** @var object $data */
        $data = json_decode($payload, false, 512, JSON_THROW_ON_ERROR);
        //validate Payment;
        $flutterwaveId = $data->id;
        //call flutterwave to validate transaction;
        $isFlwavePercent = false;
        if ($provider === "FLWAVEPERCENT"){
            $isFlwavePercent = true;
        }
        $flwave = getFlwave($isFlwavePercent);


        $fromFlutterwave = $flwave->verifyTransaction($flutterwaveId);
        info("Transaction Verified :", $fromFlutterwave);

        //check transaction status;
        $flwavePayload = $fromFlutterwave['data'];
        if (strtoupper($flwavePayload["status"]) === self::successful) {
            $details['flag'] = strtolower(self::successful);
        }
        if (strtoupper($flwavePayload["status"]) === self::failed) {
            $details['flag'] = strtolower(self::failed);
        }
        $payment_provider_message = $flwavePayload['flw_ref'] . " " . $flwavePayload['processor_response'];

        $trnx_details = array_merge($flwavePayload['customer'], [
            "narration" => $flwavePayload['narration'],
            "id" => $flutterwaveId,
            "tx_ref" => $flwavePayload['tx_ref'],
            "ip" => $flwavePayload['ip'],
            "payment_type" => $flwavePayload['payment_type']
        ]);
        return array($details, $payment_provider_message, $trnx_details);
    }


    public function getPaymentChannels(Request $request)
    {
        return Gateway::whereIn('name', ['Bank Transfer', 'Card'])->select(['name'])->get()->each(function (Gateway $gateway) {
            $gateway->name = strtolower(str_replace(' ', '_', $gateway->name));
        });

    }

    public function computeChargeAndTotal(ChargeAndTotalRequest $request)
    {
        /** @var Transaction $transaction */
        $transaction = $request->input('transaction');
        $channel = ucwords(str_replace('_', ' ', $request->input('channel')));
        $gateway = Gateway::where('name', $channel)->select(['id', 'name'])->first();
        $transactionTotal = $transaction->computeChargeAndTotal($gateway->id);
        return ["status" => true, "amount" => $transactionTotal['total'], "breakdown" => ["charge" => $transactionTotal['charge'], 'face_value' => $transactionTotal['total'] - $transactionTotal['charge']]];
    }

    public function processBankTransfer(BankTransferRequest $request)
    {
        $result = ["status" => false, "account_name" => null, "account_number" => null, "bank_name" => null, "expires_at" => null];
        $pp = new PaymentPage();
        /** @var Transaction $transaction */
        $transaction = $request->transaction;
        $pp->invoice = $transaction->invoice;
        $hours = $request->input('expires_at');
        $prefix = $request->input('account_name_prefix');
        $expires_at = max(min($hours, 180), 60);
        $pp->generateVirtualAccountNumber($prefix, $expires_at);
        $virtualAccDetails = $pp->virtualAccDetails;
        if ($virtualAccDetails['status']) {
            $result['status'] = true;
            $result['account_number'] = $virtualAccDetails['accountNumber'];
            $result['account_name'] = $virtualAccDetails['accountName'];
            $result['bank_name'] = $virtualAccDetails['bankName'];
            $result['expires_at'] = $virtualAccDetails['endtime'];

        }
        return $result;

    }


    /**
     * @throws \JsonException
     */
    public function processCardTransaction(CardTransferRequest $request)
    {
        $pp = $this->bootstrapCardPayment($request);
        $pp->processCardTransaction();
        return $pp->details;

    }

    /**
     * @throws \JsonException
     */
    public function authorizeCardWithPin(authorizeCardWithPinRequest $request)
    {
        $pp = $this->bootstrapCardPayment($request);
        $pp->cc_Pin = $request->pin;
        $pp->cardDetails = json_decode($pp->cardDetails,true);
        $pp->cardDetails['authorization']['pin']  = $pp->cc_Pin;
        $pp->cardDetails['authorization']['mode']  = "pin";
        $pp->cardAuthorizationWithPin();
        return $this->checkPaymentCompletedFlag($pp);

    }

    public function authorizeCardWithOtp(authorizeCardWithOtpRequest $request)
    {
        /** @var Transaction $transaction */
        $transaction = $request->transaction;
        $result = ['status' => false, 'authorization' => null, 'data' => $transaction->refresh()->transactionToPayload()];

        $pp = $this->bootstrapCardPayment($request);
        $pp->user = $request->user();
        $pp->cc_Otp = $request->otp;

        $pp->transaction = $transaction;
        $pp->cardDetails = json_decode($pp->cardDetails,true);
        $pp->cardAuthorizationWithOtp();

        if (isset($pp->details['status'])){
            $result['status'] = true;
            $result['authorization'] = null;
            $result['data'] = $pp->transaction->refresh()->transactionToPayload();
        }
        return $result;

    }

    /**
     * @throws \JsonException
     */
    public function authorizeCardWithAvs(authorizeCardWithAvsRequest $request)
    {
        /** @var Transaction $transaction */
        $transaction = $request->transaction;

        $pp = $this->bootstrapCardPayment($request);
        $pp->user = $request->user();
        $pp->transaction = $transaction;
        $pp->cardDetails = json_decode($pp->cardDetails,true);
        $pp->cardDetails['authorization']['mode']  = "avs_noauth";
        $pp->cardDetails['authorization']['city']  = $request->input('city');
        $pp->cardDetails['authorization']['address']  = $request->input('address');
        $pp->cardDetails['authorization']['state']  = $request->input('state');
        $pp->cardDetails['authorization']['country']  = $request->input('country');
        $pp->cardDetails['authorization']['zipcode']  = $request->input('zipcode');
        $pp->cardAuthorizationWithAvs();
        return $this->checkPaymentCompletedFlag($pp);
    }

    public function consumatePayment(BankTransferRequest $request)
    {
        /** @var Transaction $transaction */
        $transaction = $request->transaction;
        $gateway = Gateway::where('name', "Bank Transfer")->select(['id', 'name'])->first(); /** @var User $user */
        $user = $transaction->user;
        /** @var Wallet $wallet */
        $wallet = $user->wallet;
        $company = company();
        $details = ['session_id' => Str::random(32), 'settlement_id' => Str::random(32), 'bank_transfer_ref' => $request->input('request_id'), 'provider' => 'DUMMY'];
        $transaction->handleSuccessfulPayment($transaction, $gateway->id, '', $details, $wallet, $user, $company);
        $data = $transaction->refresh()->transactionToPayload();

        return response()->json([
            "status" => true,
            "data"  => $data
        ]);

    }

    /**
     * @param mixed $request
     * @return PaymentPage
     */
    public function bootstrapCardPayment(FormRequest $request): PaymentPage
    {
        $pp = app(PaymentPage::class);
        /** @var Transaction $transaction */
        $transaction = $request->transaction;
        $pp->invoice = $transaction->invoice;
        $pp->activeTab = "card";
        $pp->merchantGateways = $this->getMerchantGatewayDetails($transaction->invoice);

        $cardDetails = [
            "card_number" => $request->card_number,
            "cvv" => $request->cvv,
            "cc_expiration" => $request->card_expiration,
            "email" => $pp->invoice->customer_email,
            "currency" => $pp->invoice->transaction->currency,
            "amount" => $pp->merchantGateways[$pp->activeTab]['invoiceTotal'],
            "tx_ref" => '',
            "redirect_url" => config('app.url') . "/payment/card/validate/{$pp->invoice->invoice_no}",
        ];

        if ($request->has('card_expiration')) {
            $pp->cardDetails['cc_expiration'] = $request->card_expiration;
            $expiry = explode("/", $pp->cardDetails['cc_expiration']);
            [$expiry_month, $expiry_year] = $expiry;
            $cardDetails['expiry_month'] = $expiry_month;
            $cardDetails['expiry_year'] = $expiry_year;
        }


        $pp->cardDetails = json_encode($cardDetails);

        return $pp;
    }

    /**
     * @param PaymentPage $pp
     * @return array|mixed
     * @throws \JsonException
     */
    public function checkPaymentCompletedFlag(PaymentPage $pp)
    {
        $result = $pp->details;

        if (isset($result['flag'])) {
            if (strtoupper($result['flag']) === "PAYMENT_COMPLETED") {
                $flWave = getFlwave(isset($pp->merchantGateways['card']['flwave_percent']));
                $pp->verifyFlwaveResponse($flWave->verifyTransactionByRef($pp->transaction->spay_ref));
                $result = ['status' => true, 'authorization' => null, 'data' => $pp->transaction->refresh()->transactionToPayload()];
            }
        }
        return $result;
    }

}
