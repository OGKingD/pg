<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceCollection;
use App\Models\Gateway;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
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
        if ($invoice->user->id === 3){
            $response = $invoice->statusOnUI();
            if (!$response['status']){
                //redirect to information page showing student should make payment
                return  view('invoice.notavailable',['merchant_ref' => $invoice->transaction->merchant_transaction_ref]);
            }
        }

        //only show payment page when invoice is pending
        if ($invoice->status !== "pending") {
            //redirect to payment page;
            return redirect()->route('receipt', ['id' => $id])->with('status', 'Invoice Paid!');
        }

        //declare other variables for the payment page;
        $merchantGatewayDetails= $this->getMerchantGatewayDetails($invoice);
        $tranx_details = $invoice->transaction->details;
        if (isset($tranx_details['channel'])){
            if (array_key_exists($tranx_details['channel'],$merchantGatewayDetails)){
                $temp[$tranx_details['channel']] = $merchantGatewayDetails[$tranx_details['channel']];
                $merchantGatewayDetails = $temp;
            }
        }
        $data['merchantGateways'] = $merchantGatewayDetails;
        $data['activeTab'] = array_key_first($merchantGatewayDetails);

        return view('payment_page', $data);

    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function checkIfInvoiceExist($id)
    {
        //check if Invoice exists;
        $invoice = Invoice::where('invoice_no', $id)->with(['transaction','gateway'])->first();

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
        $merchantGateways = $mGateways ? $mGateways->config_details : null;

        $freshArr = [];

        if ($merchantGateways) {
            array_walk($merchantGateways, function ($item, $key) use (&$freshArr, $invoice) {
                if ($item['status']){
                    if (strtolower($item['name']) === "card"){
                        if ($invoice->user_id === 3){
                            $item = $this->setCardMerchantCharge($invoice,$item);
                        }
                    }
                    $item['gateway_id'] = $key;
                    $item["invoiceCharge"] = $item['charge_factor'] ?  ($item['charge'] / 100) * $invoice->amount : $item['charge'];
                    $item["invoiceTotal"] = $invoice->amount + $item['invoiceCharge'];
                    $freshArr[str_replace(' ', '', strtolower($item['name']))] = $item;

                }
            });

        }

        return $freshArr;
    }

    public function setCardMerchantCharge(Invoice $invoice,$item)
    {
        $transaction = $invoice->transaction;
        //Tuition fees - 50 - 500,000 - flat rate 350
        //Application fees - 18000 - 25000 - % 1.5
        //Transcript fees - 700 - 25000 %1.5
        //Acceptance fees - 22000 % 1.5
        //Check if it's UI merchant;
        $type = strtolower(str_replace(" ", "", $transaction->type));

        if ( str_contains($type,"tuition") || str_contains($type,"school") ){
            $item['charge_factor'] = 0;
            $item['charge'] = 350;
        }

        if ( str_contains($type,"application") ){
            $item['charge_factor'] = 0;
            $item['charge'] = 350;
//            $item['flwave_percent'] = true;
        }

        if ( str_contains($type,"transcript") ){
            $item['charge_factor'] = 0;
            $item['charge'] = 350;
//            $item['flwave_percent'] = true;
        }

        if ( str_contains($type,"acceptance") ){
            $item['charge_factor'] = 0;
            $item['charge'] = 350;
//            $item['flwave_percent'] = true;
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
            $data['redirect']  = false;

            if (isset($transaction->redirect_url)){
                $data['redirect'] = true;
                $urlQuery = $transaction->transactionToPayload();
                $url = $transaction->redirect_url;
                $data['redirect_url']  = $url ."?" . http_build_query($urlQuery);
            }

        }


        return view('payment_receipt', $data);


    }

    public function createPaymentRequest(Request $request)
    {
        $gateways = [];
        $trn_details = [];

        if ($request->has('channel')){
            $gateways = Gateway::all()->pluck('id','name')->toArray();
            $trn_details['channel'] = strtolower(str_replace(" ","",array_search($request->channel, $gateways, false)));
        }
        $request->validate([
            "name" => "required",
            "amount" => ["required", "numeric", "min:100"],
            "email" => ["required",'email:rfc'],
            "quantity" => ["required", "numeric", "min:1"],
            'request_id' => ["required", "min:5","max:32"],
            "channel" => ['sometimes',Rule::in($gateways)],
            "redirect_url" => ["sometimes", "url"]

        ], $request->all());


        /** @var User $user */
        $user = $request->user();

        //request passed create Invoice and return link;
        $data = "";
        $request_id = $request->request_id;

        //check if invoice Exists;
        $transaction = Transaction::firstWhere('merchant_transaction_ref',$request_id);
        if ($transaction){
            //check for UI merchant and apply custom rule;
            if ($user->id === 3){
               return  $this->UIpaymentRule($transaction,$request);
            }
            $error = [
                "request_id" => ["Payment Request already Exists, Please Use a Unique Request ID!"],
            ];
            return response()->json(errorResponseJson('Payment Request Failed',$error),404);

        }

        DB::transaction(function () use ($request,$request_id, $user, &$data, &$trn_details) {
            $redirect_url = $request->redirect_url;
            $amount = $request->amount;
            /** @var Invoice $invoiceAdded */
            $invoiceAdded = $user->invoice()->create([
                'invoice_no' => 'INV' . $request_id,
                'quantity' => 1,
                'customer_email' => $request->email,
                'customer_name' => $request->full_name,
                'due_date' => Carbon::now()->addDays(7),
                'amount' => $amount,
                'name' => $request->name,
            ]);
            //Add Transaction;
            $uuid = Str::orderedUuid();
            //check if merchantRedirectURL is set and add it ;
            if (isset($redirect_url)) {
                $trn_details['redirect_url'] = $redirect_url;
                $trn_details['full_name'] = $request->full_name;
            }
            $invoiceAdded->transaction()->create([
                "transaction_ref" => $uuid,
                "user_id" => $invoiceAdded->user_id,
                "merchant_transaction_ref" => $request_id ?? $uuid,
                "status" => "pending",
                "type" => $request->service_type,
                "amount" => $amount,
                "total" => $amount,
                'details' => $trn_details,
                "flag" => "debit",
                "redirect_url" => $redirect_url
            ]);
            $data = new InvoiceCollection($invoiceAdded);
        });
        //set flag to indicate it's a paymentRequest;
        $request->attributes->set('paymentRequest', true);

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
        $transaction = Transaction::firstWhere('merchant_transaction_ref',$request_id);
        $message = ['status' => false, "message" => "Payment Request Not Found", "data" => $data,];

        if ($transaction){
            $tStatus = strtoupper($transaction->status);
            $message = ['status' => false, "message" => "Payment Request already $tStatus", "data" => $data,];

            //only allow update on pending transactions
            if ($tStatus === "PENDING"){
                DB::transaction(function () use ($request,$transaction, &$data) {
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
     */
    public function validateCardPayment($id, Request $request)
    {
        $payload = $request->get('response');
        /** @var object $data */
        $data = json_decode($payload, false, 512, JSON_THROW_ON_ERROR);
        $details = ['status' => true, 'flag' => self::pending];
        //validate Payment;
        $flutterwaveId = $data->id;
        $invoice = $this->checkIfInvoiceExist($id);
        if ($invoice) {
            /** @var \App\Models\Transaction $transaction */
            $transaction = $invoice->transaction;

            //make sure invoice status is not successful;
            if (strtoupper($invoice->status) !== self::successful) {
                //call flutterwave to validate transaction;
                $provider = strtoupper($transaction->provider);
                $flwave = getFlwave(false);
                if ($provider === "FLWAVEPERCENT"){
                    $flwave = getFlwave(true);
                }

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

                /** @var Gateway $gateway */
                $gateway = Gateway::select(['id', 'name'])->where('name', "Card")->first();


                $payment_provider_message = $flwavePayload['flw_ref'] . " " . $flwavePayload['processor_response'];

                $trnx_details = array_merge($flwavePayload['customer'], [
                    "narration" => $flwavePayload['narration'],
                    "id" => $flutterwaveId,
                    "tx_ref" => $flwavePayload['tx_ref'],
                    "ip" => $flwavePayload['ip'],
                    "payment_type" => $flwavePayload['payment_type']
                ]);

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
            ['user_id','=',$userId],
            ['merchant_transaction_ref','=', $request->request_id]
        ]);
        if (is_null($transaction)){

            $error = [
                "request_id" => ["The request id does not exist"],
            ];
            return response()->json(errorResponseJson('Invalid Request ID',$error),404);
        }

        return response()->json(['status' => true, "message" => "Detail Retrieved Successfully", "data" => new InvoiceCollection($transaction),]);

    }

    public function UIpaymentRule(Transaction $transaction,$request)
    {
        $status = strtoupper($transaction->status);

        $isSuccessful = false;
        $message = "Transaction Already Processed";

        if ($status === "FAILED"){
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
        if ($status === "PENDING"){
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

}
