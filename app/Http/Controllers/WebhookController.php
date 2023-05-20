<?php

namespace App\Http\Controllers;

use App\Lib\Services\Flutterwave;
use App\Lib\Services\NinePSB;
use App\Lib\Services\Providus;
use App\Models\DynamicAccount;
use App\Models\Gateway;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Webhooks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{
    public function index()
    {
        $perPage = \request('perpage');
        $perPage = ($perPage > 7000 ? 100: $perPage) ?? 20;
        $criteria = array_filter(\request()->query());

        $result = Webhooks::latest()->criteria($criteria);
        $webhooks = $result->paginate($perPage);
        $transactionCount = $webhooks->total();


        return view('admin.webhooks', compact("webhooks",'perPage','transactionCount'));
    }


    public function flutterwave(Request $request, Webhooks $webhooks)
    {
        $requestSuccessful = false;
        $settlementId = false;
        $responseMessage = false;
        $userRef = "";

        info("request from {$request->ip()} is ", $request->all());
        $data = $request->input('data');
        try {

            $statusCode = 406;

            if (isset($data['id'])) {
                $flutterwaveId = $data['id'];
                $settlementId = $flutterwaveId;
                if (isset($data['fullname']) && !isset($data['full_name'])) {
                    $data['full_name'] = $data['fullname'];
                }

                // check if id exist on flutter and reference belonngs to saana;
                $flwave = new Flutterwave(config('flutterwave.secret_key'));
                $fromFlutterwave = $flwave->verifyTransaction($flutterwaveId);
                info("Transaction Verified :",$fromFlutterwave);
                $responseMessage = "Transaction Not Found on Flutterwave!";

                /** @var array $flwavePayload */
                $flwavePayload = $fromFlutterwave['data'];
                if (isset($flwavePayload['id'])) {

                    $responseMessage = "Processing";
                    $payment_provider_message = $flwavePayload['flw_ref'] . " " . $flwavePayload['processor_response'];

                    $settlementId = $flutterwaveId;
                    $gateway = Gateway::whereIn('name', ['Card', 'Googlepay', 'Applepay'])->get()->pluck("id", "name");

                    //check if transaction Exists on Saanapay
                    /** @var Transaction $transactionExists */
                    $transactionExists = Transaction::where("flutterwave_ref", $flwavePayload['id'])->first();

                    if (is_null($transactionExists)){

                        $responseMessage = "Transaction Not Found On Saana.";
                        $statusCode = 404;
                    }


                    if ($transactionExists) {

                        /**
                         * @var User $user
                         * @var User $company
                         * @var Wallet $wallet
                         **/
                        $user = $transactionExists->user;
                        $userRef = $user->id;
                        $company = company();
                        $wallet = $user->wallet;
                        $statusCode = 200;


                        //check if transaction is successful
                        if ($transactionExists->status === "successful") {
                            $responseMessage = "Duplicate Transaction";
                        }

                        //check if transaction is pending
                        if ($transactionExists->status === "pending") {

                            if ($flwavePayload['amount'] < $transactionExists->total ){

                                $responseMessage = "Amount Paid less than Transaction Amount";
                            }

                            //make sure amount equalt to or greater than transaction amount;
                            if ($flwavePayload['amount'] >= $transactionExists->total) {
                                $gateway_id = $gateway[ucfirst($flwavePayload['payment_type'])];
                                $details = array_merge($flwavePayload['customer'], [
                                    "narration" => $flwavePayload['narration'],
                                    "tx_ref" => $flwavePayload['tx_ref'],
                                    "ip" => $flwavePayload['ip'],
                                    "payment_type" => $flwavePayload['payment_type']
                                ]);
                                //update transaction
                                if ($flwavePayload['status'] === "successful") {

                                    $responseMessage = "successful";
                                    $requestSuccessful = true;

                                    DB::transaction(function () use ($details, $gateway_id, $payment_provider_message, $transactionExists, $wallet, $user, $company) {

                                        $transactionExists->handleSuccessfulPayment($transactionExists, $gateway_id, $payment_provider_message, $details, $wallet, $user, $company);

                                    });
                                }

                                //check if flutterewave ever sends failed;
                                if ($flwavePayload['status'] === "failed") {
                                    $responseMessage = "failed";

                                    $transactionExists->handleFailedPayment($transactionExists,$gateway_id,$payment_provider_message,$details);

                                }
                            }
                            //update transaction;

                        }

                        $webhookResponse = [
                            "status_code" => $statusCode,
                            "message" => $responseMessage
                        ];


                        $webhooks->logWebhook($settlementId,$userRef,$data,$webhookResponse);

                    }


                }
            }

            return response()->json([
                "requestSuccessful" => $requestSuccessful,
                "settlementId" => $settlementId,
                "responseMessage" => $responseMessage,
                "responseCode" => $statusCode
            ], $statusCode);

        } catch (\Exception $e) {
            $statusCode = 500;
            $responseMessage = "error occurred in webhook";

            logger()->alert('error occurred in webhook', array_merge((array)$data, ['cause'=>$e->getMessage(), 'trace'=>$e->getTraceAsString()]));

            $webhookResponse = [
                "status_code" => $statusCode,
                "message" => $responseMessage
            ];
            $webhooks->logWebhook($settlementId,$userRef,$data,$webhookResponse);


            return response()->json([
                "requestSuccessful" => $requestSuccessful,
                "settlementId" => $settlementId,
                "responseMessage" => $responseMessage,
                "responseCode" => $statusCode
            ], $statusCode);
        }


    }
    //


    public function providusSettlement(Request $request, Providus $providus, Webhooks $webhooks){


        $requestSuccessful = false;
        $responseCode = "02";
        $responseMessage = "";
        $settlementId = $request->settlementId;
        $data = $request->all();
        $userRef = "";
        $statusCode = 400;


        info("request from {$request->ip()} is ", $data);

        try {
            if (isset($settlementId)) {
                // query for the status of the transaction to really be sure that the payment was made
                $transaction_status = $providus->verifyTransaction($settlementId);
                info("call PROVIDUS to get transaction $settlementId status.", (array)$transaction_status);
                $processTransaction = false;
                if (isset($transaction_status)){
                    $responseMessage = $transaction_status->tranRemarks;
                    if (empty($transaction_status->initiationTranRef)){
                        $processTransaction = false;
                        $responseMessage = "Transaction with settlementId : $transaction_status->settlementId cannot be processed, initiationTranRef is Missing ";
                    }
                    if ($transaction_status->settlementId === $request->settlementId){
                        $processTransaction = true;

                    }
                }
                if ($processTransaction){
                    $gateway = Gateway::where('name', "Bank Transfer")->get()->pluck("id", "name");

                    //check if transaction Exists on Saanapay
                    /** @var Transaction $transactionExists */
                    $transactionExists = DynamicAccount::with(['invoice','transaction'])->where("initiationTranRef", $request->initiationTranRef)->where('status',1)->first();


                    if (is_null($transactionExists)){
                        $statusCode = 200;
                        $responseMessage = "Transaction Not Found On Saana.";
                    }
                    if ($transactionExists) {

                        /**
                         * @var User $user
                         * @var User $company
                         * @var Wallet $wallet
                         **/
                        /** @var Transaction $spTransaction */
                        $spTransaction = $transactionExists->transaction;
                        $user = $spTransaction->user;
                        $gateway_id = $gateway["Bank Transfer"];
                        $transactionTotal = $spTransaction->computeChargeAndTotal($gateway_id);
                        $spTransaction->total = $transactionTotal['total'];
                        $spTransaction->fee = $transactionTotal['charge'];
                        $userRef = $user->id;
                        $company = company();
                        $wallet = $user->wallet;
                        $statusCode = 200;


                        //check if transaction is successful
                        if ($spTransaction->status === "successful") {
                            $responseMessage = "Duplicate Transaction";
                            $responseCode = "01";
                        }

                        //check if transaction is pending
                        if ($spTransaction->status === "pending") {

                            if ($transaction_status->transactionAmount < $spTransaction->total ){

                                $responseMessage = "Amount Paid less than Transaction Amount";
                            }

                            //make sure amount equal to or greater than transaction amount;
                            if ($transaction_status->transactionAmount >= $spTransaction->total) {
                                $gateway_id = $gateway["Bank Transfer"];
                                $details = [
                                    "initiationTranRef" => $transaction_status->initiationTranRef,
                                    "sourceAccountNumber" => $transaction_status->sourceAccountNumber,
                                    "sourceBankName" => $transaction_status->sourceBankName,
                                    "settlementId" => $transaction_status->settlementId,
                                    "sessionId" => $transaction_status->sessionId,
                                ];
                                //update transaction
                                $responseMessage = "successful";
                                $requestSuccessful = true;
                                $transactionExists->update([
                                    'session_id' => $transaction_status->settlementId,
                                    'settlement_id' =>  $transaction_status->sessionId,
                                ]);

                                DB::transaction(function () use ($details, $gateway_id, $transaction_status, $spTransaction, $wallet, $user, $company) {
                                    //update transaction fee and total;
                                    $spTransaction->update([
                                        'total' => $transaction_status->transactionAmount,
                                        'fee' => $transaction_status->transactionAmount - $spTransaction->amount,
                                        'bank_transfer_ref' => $transaction_status->settlementId
                                    ]);
                                    $spTransaction->handleSuccessfulPayment($spTransaction, $gateway_id, $transaction_status->tranRemarks, $details, $wallet, $user, $company);

                                });

                            }
                            //update transaction;

                        }


                    }
                }

                $webhookResponse = [
                    "status_code" => $statusCode,
                    "message" => $responseMessage
                ];

                $webhooks->logWebhook($settlementId,$userRef,$data,$webhookResponse);

            }
            logger("Webhook Response for $request->settlementId",[
                "requestSuccessful"=> $requestSuccessful,
                "settlementId"=> $request->settlementId,
                "responseMessage"=> $responseMessage,
                "responseCode"=> $responseCode
            ]);

            return response()->json([
                "requestSuccessful"=> $requestSuccessful,
                "settlementId"=> $request->settlementId,
                "responseMessage"=> $responseMessage,
                "responseCode"=> $responseCode
            ]);
        } catch (\Exception $e) {
            $statusCode = 500;
            $responseMessage = "error occurred in webhook";

            logger()->alert('error occurred in webhook', array_merge($data, ['cause'=>$e->getMessage(), 'trace'=>$e->getTraceAsString()]));

            $webhookResponse = [
                "status_code" => $statusCode,
                "message" => $responseMessage
            ];
            $webhooks->logWebhook($settlementId,$userRef,$data,$webhookResponse);


            return response()->json([
                "requestSuccessful" => $requestSuccessful,
                "settlementId" => $settlementId,
                "responseMessage" => $responseMessage,
                "responseCode"=> $responseCode
            ], $statusCode);
        }


    }

    public function ninePsbSettlement(Request $request, NinePSB $ninePsb, Webhooks $webhooks){


        $requestSuccessful = false;
        $responseCode = "02";
        $responseMessage = "";
        $data = $request->all();
        $settlementId = $request['transaction']['linkingreference'] ?? null;
        $sessionId = $request['transaction']['externalreference'] ?? null;
        $userRef = "";


        info("request from {$request->ip()} is ", $data);

        try {
            if (isset($settlementId)) {
                // query for the status of the transaction to really be sure that the payment was made
                $transaction_status = (object)$ninePsb->transactionStatusDynamicAccount($settlementId);
                info("call 9PSB to get transaction $settlementId status.", (array)$transaction_status);
                $processTransaction = false;

                if ($transaction_status->status){
                    $gateway = Gateway::where('name', "Bank Transfer")->get()->pluck("id", "name");

                    //check if transaction Exists on Saanapay
                    /** @var Transaction $transactionExists */
                    $transactionExists = DynamicAccount::with(['invoice','transaction'])->where("initiationTranRef", $settlementId)->first();

                    if (is_null($transactionExists)){

                        $responseMessage = "Transaction Not Found On Saana.";
                    }

                    if ($transactionExists) {

                        /**
                         * @var User $user
                         * @var User $company
                         * @var Wallet $wallet
                         **/
                        /** @var Transaction $spTransaction */
                        $spTransaction = $transactionExists->transaction;
                        $user = $spTransaction->user;
                        $gateway_id = $gateway["Bank Transfer"];
                        $transactionTotal = $spTransaction->computeChargeAndTotal($gateway_id);
                        $spTransaction->total = $transactionTotal['total'];
                        $spTransaction->fee = $transactionTotal['charge'];
                        $userRef = $user->id;
                        $company = company();
                        $wallet = $user->wallet;
                        $statusCode = 200;
                        $transactionExists->update([
                            'session_id' => $sessionId,
                            'settlement_id' =>  $settlementId,
                        ]);


                        //check if transaction is successful
                        if ($spTransaction->status === "successful") {
                            $responseMessage = "Duplicate Transaction";
                            $responseCode = "01";
                        }

                        //check if transaction is pending
                        if ($spTransaction->status === "pending") {

                            if ($transaction_status->data['order']['amount'] < $spTransaction->total ){

                                $responseMessage = "Amount Paid less than Transaction Amount";
                            }


                            if ($transaction_status->payment === "successful") { //make sure amount equal to or greater than transaction amount;
                                if ($transaction_status->data['order']['amount'] >= $spTransaction->total) {

                                    $details = [
                                        "initiationTranRef" => $transaction_status->data["transaction"]['linkingreference'],
                                        //    "sourceAccountNumber" => $transaction_status->sourceAccountNumber,
                                        //    "sourceBankName" => $transaction_status->sourceBankName,
                                        "settlementId" => $settlementId,
                                        "sessionId" => $sessionId,
                                    ];

                                    //update transaction
                                    $responseMessage = "successful";
                                    $requestSuccessful = true;

                                    DB::transaction(function () use ($details, $gateway_id, $transaction_status, $spTransaction, $wallet, $user, $company) {
                                        //update transaction fee and total;
                                        $spTransaction->update([
                                            'total' => $transaction_status->data['order']['amount'],
                                            'fee' => $transaction_status->data['order']['amount'] - $spTransaction->amount,
                                            'bank_transfer_ref' => $transaction_status->data['transaction']['linkingreference']
                                        ]);
                                        $spTransaction->handleSuccessfulPayment($spTransaction, $gateway_id, $transaction_status->tranRemarks, $details, $wallet, $user, $company);

                                    });

                                }
                            }

                            //update transaction;

                        }

                        $webhookResponse = [
                            "status_code" => $statusCode,
                            "message" => $responseMessage
                        ];


                        $webhooks->logWebhook($settlementId,$userRef,$data,$webhookResponse);

                    }
                }


            }
            logger("Webhook Response for $request->settlementId",[
                "requestSuccessful"=> $requestSuccessful,
                "settlementId"=> $request->settlementId,
                "responseMessage"=> $responseMessage,
                "responseCode"=> $responseCode
            ]);

            return response()->json([
                "requestSuccessful"=> $requestSuccessful,
                "settlementId"=> $request->settlementId,
                "responseMessage"=> $responseMessage,
                "responseCode"=> $responseCode
            ]);
        } catch (\Exception $e) {
            $statusCode = 500;
            $responseMessage = "error occurred in webhook";

            logger()->alert('error occurred in webhook', array_merge($data, ['cause'=>$e->getMessage(), 'trace'=>$e->getTraceAsString()]));

            $webhookResponse = [
                "status_code" => $statusCode,
                "message" => $responseMessage
            ];
            $webhooks->logWebhook($settlementId,$userRef,$data,$webhookResponse);


            return response()->json([
                "requestSuccessful" => $requestSuccessful,
                "settlementId" => $settlementId,
                "responseMessage" => $responseMessage,
                "responseCode"=> $responseCode
            ], $statusCode);
        }


    }
}
