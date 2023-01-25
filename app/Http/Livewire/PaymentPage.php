<?php

namespace App\Http\Livewire;

use Exception;
use App\Lib\Services\{Flutterwave, Providus, Remita};
use App\Models\DynamicAccount;
use App\Models\Gateway;
use App\Models\PaymentRequest;
use App\Models\RRR;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Component;

class PaymentPage extends Component
{
    public $invoiceNo;
    public $invoiceTotal;
    public $invoiceCharge;
    public $merchantGateways;
    public $invoice;
    public $activeTab = "card";
    public $remitaDetails;
    public $virtualAccDetails;
    public $cardDetails;
    public $gatewayId;
    public $merchantRedirectUrl;

    public $cc_Number;
    public $cc_Expiration;
    public $cc_Pin;
    public $cc_Otp;
    public $isPinRequired;
    public $isOtpRequired;
    public $hideCardFields;
    public $user;
    public $transaction;

    protected $listeners = ['generateRRR', 'processCardTransaction',
        'cardAuthorizationWithPin', 'cardAuthorizationWithOtp','cardAuthorizationWithAvs',
        'generateVirtualAccountNumber', 'payWith',];

    public function render()
    {
        $this->setActiveTab($this->activeTab);

        return view('livewire.payment-page');
    }

    /**
     * @throws \JsonException
     * @var float|int|mixed
     */

    public function generateRRR(Remita $remitaService)
    {

        //get the INvoice NO;
        $rrr = $this->invoice->rrr;

        $this->logPaymentRequest("remita");

        $status = false;
        //check if transaction has RRR already;
        if ($rrr) {
            $remitaUrl = config('remita.redirect_url') . "/remita/onepage/biller/$rrr->rrr/payment.spa";
            $status = true;
            $this->remitaDetails = ["status" => $status, 'RRR' => $rrr->rrr, 'url' => $remitaUrl];

        } else {
            //Get Merchant Charge;
            $amount = $this->merchantGateways[$this->activeTab]['invoiceTotal'];


            $response = $remitaService->remitaGenerateRRR($amount, $this->invoice->invoice_no, $this->invoice->customer_email);
            $parsedResult = $response;
            if (str_contains($parsedResult, "jsonp")) {
                $status = true;
                $parsedResult = json_decode(trim($response, 'jsonp ( )'), false, 512, JSON_THROW_ON_ERROR);
            }
            //insert RRR into rrr table;
            RRR::create([
                'rrr' => $parsedResult->RRR,
                'invoice_no' => $this->invoice->invoice_no,
            ]);
            $remitaUrl = config('remita.redirect_url') . "/remita/onepage/biller/$parsedResult->RRR/payment.spa";

            $this->remitaDetails = ["status" => $status, 'RRR' => $parsedResult->RRR, 'url' => $remitaUrl];

        }


        $this->dispatchBrowserEvent('rrrGenerated');

    }

    public function setActiveTab($tab): void
    {
        $this->activeTab = $tab;

    }
    /**
     * @param $channel
     * @throws \JsonException
     */
    public function logPaymentRequest($channel): void
    {
        $paymentRequest = (new PaymentRequest)->firstOrCreate(
            [
                "invoice_no" => $this->invoice->invoice_no,
            ]
        );
        $response = [
            "channel" => $channel,
            "date" => Carbon::now()->toDayDateTimeString(),
        ];
        //Get the payload;
        $payload = $paymentRequest->details;
        $payload[] = $response;


        $paymentRequest->update([
            "details" => $payload,
        ]);
    }

    /**
     * @throws \JsonException
     */
    public function generateVirtualAccountNumber(Providus $providus)
    {
        $status = false;
        $this->setActiveTab('banktransfer');
        $accountName = "PROVIDUS BANK";

        //check table to see if virtual Account Exists;
        $virtualAcc = DynamicAccount::where('invoice_no', $this->invoice->invoice_no)->first();

        //Add Payment Request;
        $this->logPaymentRequest("bank transfer");


        if ($virtualAcc) {
            $status = true;
            $this->virtualAccDetails = ["status" => $status, "accountNumber" => $virtualAcc->account_number, "accountName" => $virtualAcc->account_name, "bankName" => $accountName];

        } else {
            //call Providus or Db to generate Account Number;
            //$result = (new Providus())->reserveAccount("SAANAPAY LIMITED","", "","","","");
            $result = $providus->generateDynamicAccountNumber('SAANAPAY LIMITED', '');


            if ($result->requestSuccessful) {
                $status = true;
                //store details into table;
                DynamicAccount::create(
                    [
                        'invoice_no' => $this->invoice->invoice_no,
                        'account_number' => $result->account_number,
                        'account_name' => $result->account_name,
                        'initiationTranRef' => $result->initiationTranRef,
                        'status' => 1
                    ]
                );

            }
            //failed call to providus return false
            $this->virtualAccDetails = ["status" => $status, "accountNumber" => $result->account_number, "accountName" => $result->account_name, "bankName" => $accountName];

        }

        $this->dispatchBrowserEvent('virtualAccountGenerated', $this->virtualAccDetails);

    }

    /**
     * @throws \JsonException
     */
    public function processCardTransaction(): void
    {
        //do validation;
        $this->cardDetails = json_decode($this->cardDetails, true, 512, JSON_THROW_ON_ERROR);


        $expiry = explode("/", $this->cardDetails['cc_expiration']);
        [$expiry_month, $expiry_year] = $expiry;
        $this->cardDetails = array_merge($this->cardDetails, [
            "card_number" => str_replace(' ', '', $this->cardDetails['card_number']),
            "cvv" => $this->cardDetails['cvv'],
            "expiry_month" => trim($expiry_month),
            "expiry_year" => trim($expiry_year),
            "currency" => "NGN",
            "amount" => $this->merchantGateways[$this->activeTab]['invoiceTotal'],
            "email" => $this->invoice->customer_email,
            "redirect_url" => config('app.url') . "/payment/card/validate/{$this->invoice->invoice_no}",
            "tx_ref" => ""
        ]);

        $validator = Validator::make($this->cardDetails, [
            "email" => ['required'],
            "card_number" => ['required', 'between:16,19', 'string'],
            "cvv" => ['required', 'size:3',],
            "cc_expiration" => ['required'],
            "amount" => ['required'],
        ]);


        if ($validator->fails()) {

            $details = ['status' => false, 'errors' => $validator->messages()];
            $this->dispatchBrowserEvent('cardPaymentProcessed', $details);
            return;

        }

        //call Flutterwave to charge Card;
        try {
            [$flwave, $response] = $this->flwChargeCard();

            $trnxId = $flwave->getTxRef();

            if (!isset($this->transaction)) {
                //create transaction;
                $orderedUuid = Str::orderedUuid();
                $this->user->transaction()->create(
                    [
                        'transaction_ref' => $orderedUuid,
                        'invoice_no' => $this->invoice->invoice_no,
                        'merchant_transaction_ref' => $orderedUuid,
                        'flutterwave_ref' => $trnxId,
                        'gateway_id' => $this->merchantGateways[$this->activeTab]['gateway_id'],
                        'amount' => $this->merchantGateways[$this->activeTab]['invoiceTotal'] - $this->merchantGateways[$this->activeTab]['invoiceCharge'],
                        'fee' => $this->merchantGateways[$this->activeTab]['invoiceCharge'],
                        'total' => $this->merchantGateways[$this->activeTab]['invoiceTotal'],
                        'status' => 'pending',
                        'flag' => 'debit',

                    ]
                );
            }

            //Update Transaction;
            if (isset($this->transaction)) {
                $this->transaction->update(
                    [
                        "flutterwave_ref" => $trnxId,
                        'gateway_id' => $this->merchantGateways[$this->activeTab]['gateway_id'],
                        'amount' => $this->merchantGateways[$this->activeTab]['invoiceTotal'] - $this->merchantGateways[$this->activeTab]['invoiceCharge'],
                        'fee' => $this->merchantGateways[$this->activeTab]['invoiceCharge'],
                        'total' => $this->merchantGateways[$this->activeTab]['invoiceTotal'],
                    ]
                );
            }
            //check for the authorization
            $details = ['status' => false, 'errors' => "Cannot Authorize Card!"];

            if (isset($response['meta']['authorization'])) {
                $details = ['status' => true,];
                $this->logPaymentRequest("card");

                $this->cardDetails['authorization']['mode'] = $response['meta']['authorization']['mode'];

                if ($response['meta']['authorization']['mode'] === 'pin') {
                    //pin required;
                    $this->isPinRequired = true;
                    $this->hideCardFields = true;
                    $details['flag'] = "pin_required";
                    $this->cardDetails['authorization']['pin'] = "";
                }
                if ($response['meta']['authorization']['mode'] === 'avs_noauth') {
                    $this->cardDetails["authorization"] = array("mode" => "avs_noauth", "city" => "Sampleville", "address" => "", "state" => "Simplicity", "country" => "Nigeria", "zipcode" => "000000",);
                    $details['flag'] = "charge_card";

                }
                if ($response['meta']['authorization']['mode'] === 'redirect') {
                    $details['flag'] = "redirect_required";
                    $details['url'] = $response['meta']['authorization']['redirect'];
                }
            }
        } catch (Exception $e) {
            $details = ['status' => false, 'errors' => $e->getMessage()];
        }

        $this->dispatchBrowserEvent('cardPaymentProcessed', $details);


    }

    /**
     * @return array
     */
    public function flwChargeCard(): array
    {
        $this->user = $this->invoice->user;
        $this->transaction = $this->invoice->transaction;

        $flwave = new Flutterwave(config('flutterwave.secret_key'));
        $response = $flwave->cardCharge($this->cardDetails);
        return array($flwave, $response);
    }

    public function cardAuthorizationWithPin()
    {

        try {//add pin to cardDetails
            $this->cardDetails['authorization']['pin'] = $this->cc_Pin;//charge card finally
            $response = $this->flwChargeCard()[1];
            $data = $response['data'];
            $this->transaction->update([
                'flutterwave_ref' => $data['id'],
            ]);
            $this->cardDetails['flw_ref'] = $data['flw_ref'];
            $details = [];//just in case check what type of Authorization is needed;
            if (isset($response['meta'])) {
                if ($response['meta']['authorization']['mode'] === 'redirect') {
                    $details['flag'] = "redirect_required";
                    $details = ["status" => true, "flag" => "redirect_required", "url" => $response['meta']['authorization']['redirect']];
                }
            }//when OTP is required;
            if (isset($data['auth_mode']) && $data['auth_mode'] === "otp") {
                $this->isOtpRequired = true;
                $this->isPinRequired = false;
                $this->hideCardFields = true;
                $details = ['status' => true, 'flag' => "otp_required"];
            }
        } catch (Exception $e) {
            logger("An Error Occurred while trying to Authorize with PIN: \n {$e->getMessage()} \n {$e->getTraceAsString()} ");
            $details = ['status' => false, 'errors' => "Connection Lost."];
        }

        $this->dispatchBrowserEvent('cardPaymentProcessed', $details);

    }

    public function cardAuthorizationWithOtp()
    {

        try {
            $flwave = new Flutterwave(config('flutterwave.secret_key'));
            $response = $flwave->validateTransaction($this->cc_Otp, $this->cardDetails['flw_ref'], 'card');
            $this->verifyFlwaveResponse($response);
        } catch (Exception $e) {
            logger("An Error Occurred while trying to Authorize with OTP: \n {$e->getMessage()} \n {$e->getTraceAsString()} ");

            $details = ['status' => false, 'errors' => $e->getMessage()];
            $this->dispatchBrowserEvent('cardPaymentProcessed', $details);

        }

    }
    public function cardAuthorizationWithAvs()
    {
        try {
            $flwave = new Flutterwave(config('flutterwave.secret_key'));
            $response = $flwave->cardCharge($this->cardDetails);
            $this->verifyFlwaveResponse($response);
        } catch (Exception $e) {
            logger("An Error Occurred while trying to Authorize with AVS: \n {$e->getMessage()} \n {$e->getTraceAsString()} ");

            $details = ['status' => false, 'errors' => $e->getMessage()];
            $this->dispatchBrowserEvent('cardPaymentProcessed', $details);
        }
    }

    public function payWith($processor)
    {


        try {
            $this->setActiveTab(strtolower($processor));
            $flwave = new Flutterwave(config('flutterwave.secret_key'));
            $tx_ref = $flwave->getTxRef();
            $gateway = Gateway::where('name', $processor)->first();
            $amount = $this->setPaymentGatewayCharges($gateway);
            $payload = [
                "amount" => $amount,
                "currency" => "NGN",
                "email" => $this->invoice->customer_email,
                "fullname" => $this->invoice->name,
                "tx_ref" => $tx_ref
            ];
            $response = $flwave->{"charge" . $processor}($payload);
            $details = [];
            if ($response) {
                $details['message'] = response(['message']);
                $details['flag'] = "payment_failed";
                $details['status'] = isset($response['data']['meta']['authorization']['redirect']);
                if ($details['status']) {
                    $details['flag'] = "redirect_required";
                    $details['url'] = $response['data']['meta']['authorization']['redirect'];
                }
            }
            logger("Payment for charge$processor Response is : ", $response);
        } catch (Exception $e) {
            $details = ['status' => false, 'errors' => $e->getMessage()];
        }


        $this->dispatchBrowserEvent('cardPaymentProcessed', $details);


    }


    /**
     * @param $response
     * @param Transaction $transaction
     * @param $wallet
     * @param User $user
     * @param User $company
     */
    public function verifyFlwaveResponse($response): void
    {
        /**
         * @var User $user
         * @var User $company
         * @var Wallet $wallet
         * @var Transaction $transaction
         **/
        $company = User::firstWhere('email', config('app.company_email'));
        $user = $this->user;
        $wallet = $user->wallet;
        $transaction = $this->transaction;
        info("Response for " . $this->cardDetails['flw_ref'], $response);

        $details = ['status' => true, 'flag' => 'processing'];
        if (isset($response['data'])) {
            if (strtoupper($response['data']['status']) === "SUCCESSFUL") {

                $details = ['status' => true, 'flag' => "payment_completed"];

                //Payment Successful;
                $payment_provider_message = $response['data']['flw_ref'] . " " . $response['data']['processor_response'];
                $params = array_merge($response['data']['customer'], [
                    "narration" => $response['data']['narration'],
                    "tx_ref" => $response['data']['tx_ref'],
                    "ip" => $response['data']['ip'],
                    "payment_type" => $response['data']['payment_type']
                ]);

                $transaction->handleSuccessfulPayment($transaction, $this->merchantGateways[$this->activeTab]['gateway_id'], $payment_provider_message, $params, $wallet, $user, $company);


            }
            if (strtoupper($response['data']['status']) === "FAILED") {
                //Payment UNSuccessful;
                $details = ['status' => false, 'flag' => "payment_failed"];
            }

        }


        $this->dispatchBrowserEvent('paymentCompleted', $details);
    }
}
