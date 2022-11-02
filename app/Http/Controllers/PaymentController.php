<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceCollection;
use App\Lib\Services\Flutterwave;
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
        $invoice = $this->checkIfInvoiceExist($id);

        $data['title'] = "Payment Page";
        $data['invoice'] = $invoice;

        //only show payment page when invoice is pending
        if ($invoice->status !== "pending") {
            //redirect to payment page;
            return redirect()->route('receipt', ['id' => $id])->with('status', 'Invoice Paid!');
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
        $invoice = Invoice::with('transaction')->where('invoice_no', $id)->first();

        if (!$invoice) {
            abort(404);
        }
        return $invoice;
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
            if (isset($transaction->details["redirect_url"])) {

                $data['merchant_site'] = $transaction->merchantRedirectUrl();

            }
        }


        return view('payment_receipt', $data);


    }

    public function createPaymentRequest(Request $request)
    {
        $request->validate([
            "name" => "required",
            "amount" => ["required", "numeric", "min:100"],
            "email" => "required",
            "quantity" => ["required", "numeric", "min:1"],
            'request_id' => ["required", "min:8"],
            "redirect_url" => ["sometimes", "url"]

        ], $request->all());


        /** @var User $user */
        $user = $request->user();

        //request passed create Invoice and return link;
        $data = "";
        $request_id = $request->request_id;

        //check if invoice Exists;
        $transaction = Transaction::select('merchant_transaction_ref')->firstWhere('merchant_transaction_ref',$request_id);
        if ($transaction){
            $error = [
                "request_id" => ["Payment Request already Exists, Please Use a Unique Request ID!"],
            ];
            return response()->json(errorResponseJson('Payment Request Failed',$error),404);
        }

        DB::transaction(function () use ($request,$request_id, $user, &$data) {
            $redirect_url = $request->redirect_url;
            $trn_details = [];
            $amount = $request->amount;
            /** @var Invoice $invoiceAdded */
            $invoiceAdded = $user->invoice()->create([
                'invoice_no' => 'INV' . $request_id,
                'quantity' => 1,
                'customer_email' => $request->email,
                'due_date' => Carbon::now()->addDays(7),
                'amount' => $amount,
                'name' => $request->name,
            ]);
            //Add Transaction;
            $uuid = Str::orderedUuid();
            //check if merchantRedirectURL is set and add it ;
            if (isset($redirect_url)) {
                $trn_details['redirect_url'] = $redirect_url;
            }
            $invoiceAdded->transaction()->create([
                "transaction_ref" => $uuid,
                "user_id" => $invoiceAdded->user_id,
                "merchant_transaction_ref" => $request_id ?? $uuid,
                "status" => "pending",
                "amount" => $amount,
                "total" => $amount,
                'details' => $trn_details,
                "flag" => "debit"
            ]);
            $data = new InvoiceCollection($invoiceAdded);
        });
        //set flag to indicate it's a paymentRequest;
        $request->attributes->set('paymentRequest', true);

        return response()->json(['status' => true, "message" => "Payment Request Successful", "data" => $data,]);

    }

    /**
     * @param $id //Invoice ID/ Transaction Id;
     * @param Request $request
     */
    public function validateCardPayment($id, Request $request)
    {
        info("Paylod sent to Redirect URL for INVOICE $id is : ", $request->all());
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
                $flwave = new Flutterwave(config('flutterwave.secret_key'));
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
                $gateway = Gateway::select('id', 'name')->where('name', "Card")->first();


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
            'request_id' => ["required", "min:8"],
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


}
