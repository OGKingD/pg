<?php

namespace App\Http\Controllers;

use App\Lib\Services\Flutterwave;
use App\Lib\Services\Providus;
use App\Models\Gateway;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Webhooks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{
    public function flutterwave(Request $request, Webhooks $webhooks)
    {
        $requestSuccessful = false;
        $settlementId = false;
        $responseMessage = false;
        $userRef = "";

        info("request from {$request->ip()} is ", $request->all());
        $event = $request->input('event');
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


    public function providusSettlement(Request $request, Providus $providus, Webhooks $webhook){


        $requestSuccessful = false;
        $settlementId = false;
        $responseMessage = false;
        $statusCode = 200;
        $userRef = "";

        info("request from {$request->ip()} is ", $request->all());


        if(isset($request->settlementId)){
            // payment made by account transfer
            // query for the status of the transaction to really be sure that the payment was made
            $transaction_status = $providus->verifyTransaction($request->settlementId);
            logger()->info( "request from {$request->ip()} is ", $request->all());
            logger()->info('call PROVIDUS to get transaction status.', (array)$transaction_status);

            //IF PROIVIDUS WEBHOOK IS DOWN AND REQUEST IS COMING FROM PROVIDUS BYPASS THE REQUERY TO PROVIDUS AND USE THE PARAMETER'S PUSHED
            //NOTE I WOULD NOT ADVISE THIS BUT STILL

            if(!isset($transaction_status) and $request->ip() === "154.113.165.53"){
                $transactionExists = Transaction::firstWhere("payment_provider_id",$request->settlementId);
                if(!$transactionExists){
                    // transaction confirmed as paid

                    $amount = $request->transactionAmount;
                    $currency = $request->currency;
                    $customer_accountNumber = $request->accountNumber;
                    if(property_exists($request, 'settledAmount')){
//                    $credit_amount = $request->settledAmount;
                        $credit_amount = $request->transactionAmount;
                    }else{
                        $credit_amount = $amount;
                    }
                    // credit the money to the wallet that belongs to $customer_email
                    $customer = ($customer= Wallet::where('account_number', $customer_accountNumber)->first()) ? $customer->user : null ;
                    if($customer){
                        /** @var Wallet $wallet */
                        $wallet = Wallet::where('account_number', $customer_accountNumber)->first();
                        if(!$wallet){
                            $wallet = $customer->wallets()->save(new Wallet(['balance' => 0, 'currency' => $currency]));
                        }

                        try{
                            DB::connection($this->dbConnection)->transaction(function () use ($currency, $request, $credit_amount, $wallet, $customer) {
                                if($wallet->credit($credit_amount, null, $before, $after)){
                                    //  keep track of this in transactions table
                                    $userAssoc = $customer->businessBelongsTo();
                                    $userAssocId = isset($userAssoc) ? $userAssoc->id : $userAssoc;
                                    $transaction = $customer->addTransaction($request->sessionId, $request->settlementId, "Account Transfer", "Fund Wallet", $credit_amount, $request->feeAmount, $request->tranRemarks, "successful", ["bankName" => $request->sourceBankName,"sourceAccountName" => $request->sourceAccountName,"sourceAccountNumber" => $request->sourceAccountNumber], "credit", $currency,$userAssocId,$customer->first_name. " ". $customer->last_name,$customer->email,$payment_provider_message = null,$request->tranDateTime);
                                    // fire event to notify user
                                    WalletCreditedJob::dispatch($wallet,$transaction,$this->royalFive);

                                }
                            });
                            logger()->alert('customer funded his wallet by transferring amount to a reserved account linked to his wallet.', (array)$request);
                            return response()->json([
                                "requestSuccessful"=> true,
                                "settlementId"=> "$request->settlementId",
                                "responseMessage"=> "success",
                                "responseCode"=> "00"
                            ],200);
                        }catch (\Exception $exception){
                            logger()->alert('error occurred in webhook', array_merge((array)$request, ['cause'=>$exception->getMessage(), 'trace'=>$exception->getTraceAsString()]));

                            $recipients = env("ADMIN_USER");
                            $alert = "Attention is Needed Urgently! A Transaction Failed to update on ".($this->royalFive ? "RoyalFive \n \n \n" :"SaanaPay \n \n \n"). print_r(array_merge((array)$request, ['cause'=>$exception->getMessage(), 'trace'=>$exception->getTraceAsString()]),true);
                            Notification::send(User::firstWhere("email",env("SUPERADMIN_USER")), new AlertNotification("Withdrawal  | Failed", $alert,$recipients));

                            return response()->json(['message' => 'error occurred in webhook. Check system logs'], 500);
                        }
                    }else{
                        logger()->alert("Providus Settlement ID of {$request->settlementId } ,Customer  {$request->accountNumber } not found by Account Number on SaanaPay ");
                        return response()->json(['message' => 'Customer not found by accountNumber on SaanaPay'], 404);
                    }
                }else{
                    if ($transactionExists){
                        return response()->json([
                            "requestSuccessful"=> true,
                            "settlementId"=> "$request->settlementId",
                            "responseMessage"=> "duplicate transaction",
                            "responseCode"=> "01"
                        ],200);
                    }

                    if ($request->tranRemarks === "Transaction not found!!!"){
                        return response()->json([
                            "requestSuccessful"=> true,
                            "settlementId"=> "$request->settlementId",
                            "responseMessage"=> "rejected transaction",
                            "responseCode"=> "02"
                        ],409);
                    }

                }
            }



            // Original Code


            $transactionExists = Transaction::firstWhere("payment_provider_id",$request->settlementId);

            if ($transactionExists){

                $requestSuccessful = true;
                $responseMessage = "duplicate transaction";

                $webhookResponse = [
                    "requestSuccessful" => $requestSuccessful,
                    "settlementId" => $request->settlementId,
                    "responseMessage" => $responseMessage,
                    "responseCode" => "01"
                ];
            }

            if(!$transactionExists){
                // transaction confirmed as paid

                $amount = $request->transactionAmount;
                $currency = $request->currency;
                $customer_accountNumber = $request->accountNumber;
                if(property_exists($request, 'settledAmount')){
//                    $credit_amount = $request->settledAmount;
                    $credit_amount = $request->transactionAmount;
                }else{
                    $credit_amount = $amount;
                }
                // credit the money to the wallet that belongs to $customer_email
                $customer = ($customer= Wallet::where('account_number', $customer_accountNumber)->first()) ? $customer->user : null ;
                if($customer){
                    /** @var Wallet $wallet */
                    $wallet = Wallet::where('account_number', $customer_accountNumber)->first();
                    if(!$wallet){
                        $wallet = $customer->wallets()->save(new Wallet(['balance' => 0, 'currency' => $currency]));
                    }

                    try{
                        DB::connection($this->dbConnection)->transaction(function () use ($currency, $request, $credit_amount, $wallet, $customer) {
                            if($wallet->credit($credit_amount, null, $before, $after)){
                                //  keep track of this in transactions table
                                $userAssoc = $customer->businessBelongsTo();
                                $userAssocId = isset($userAssoc) ? $userAssoc->id : $userAssoc;
                                $transaction = $customer->addTransaction($request->sessionId, $request->settlementId, "Account Transfer", "Fund Wallet", $credit_amount, $request->feeAmount, $request->tranRemarks, "successful", ["bankName" => $request->sourceBankName,"sourceAccountName" => $request->sourceAccountName,"sourceAccountNumber" => $request->sourceAccountNumber], "credit", $currency,$userAssocId,$customer->first_name. " ". $customer->last_name,$customer->email,$payment_provider_message = null,$request->tranDateTime);
                                // fire event to notify user
                                WalletCreditedJob::dispatch($wallet,$transaction,$this->royalFive);

                            }
                        });
                        logger()->alert('customer funded his wallet by transferring amount to a reserved account linked to his wallet.', (array)$request);
                        return response()->json([
                            "requestSuccessful"=> true,
                            "settlementId"=> "$request->settlementId",
                            "responseMessage"=> "success",
                            "responseCode"=> "00"
                        ],200);
                    }catch (\Exception $exception){
                        logger()->alert('error occurred in webhook', array_merge((array)$request, ['cause'=>$exception->getMessage(), 'trace'=>$exception->getTraceAsString()]));

                        $recipients = env("ADMIN_USER");
                        $alert = "Attention is Needed Urgently! A Transaction Failed to update on ".($this->royalFive ? "RoyalFive \n \n \n" :"SaanaPay \n \n \n"). print_r(array_merge((array)$request, ['cause'=>$exception->getMessage(), 'trace'=>$exception->getTraceAsString()]),true);
                        Notification::send(User::firstWhere("email",env("SUPERADMIN_USER")), new AlertNotification("Withdrawal  | Failed", $alert,$recipients));

                        return response()->json(['message' => 'error occurred in webhook. Check system logs'], 500);
                    }
                }else{
                    logger()->alert("Providus Settlement ID of {$request->settlementId } ,Customer  {$request->accountNumber } not found by Account Number on SaanaPay ");
                    return response()->json(['message' => 'Customer not found by accountNumber on SaanaPay'], 404);
                }
            }

            if($transaction_status && isset($transaction_status->settlementId)   && !$transactionExists){
                // transaction confirmed as paid

                $amount = $transaction_status->transactionAmount;
                $currency = $transaction_status->currency;
                $customer_accountNumber = $transaction_status->accountNumber;
                if(property_exists($transaction_status, 'settledAmount')){
//                    $credit_amount = $transaction_status->settledAmount;
                    $credit_amount = $transaction_status->transactionAmount;
                }else{
                    $credit_amount = $amount;
                }
                // credit the money to the wallet that belongs to $customer_email
                $customer = ($customer= Wallet::where('account_number', $customer_accountNumber)->first()) ? $customer->user : null ;
                if($customer){
                    /** @var Wallet $wallet */
                    $wallet = Wallet::where('account_number', $customer_accountNumber)->first();
                    if(!$wallet){
                        $wallet = $customer->wallets()->save(new Wallet(['balance' => 0, 'currency' => $currency]));
                    }

                    try{
                        DB::connection($this->dbConnection)->transaction(function () use ($currency, $transaction_status, $credit_amount, $wallet, $customer) {
                            if($wallet->credit($credit_amount, null, $before, $after)){
                                //  keep track of this in transactions table
                                $userAssoc = $customer->businessBelongsTo();
                                $userAssocId = isset($userAssoc) ? $userAssoc->id : $userAssoc;
                                $transaction = $customer->addTransaction($transaction_status->sessionId, $transaction_status->settlementId, "Account Transfer", "Fund Wallet", $credit_amount, $transaction_status->feeAmount, $transaction_status->tranRemarks, "successful", ["bankName" => $transaction_status->sourceBankName,"sourceAccountName" => $transaction_status->sourceAccountName,"sourceAccountNumber" => $transaction_status->sourceAccountNumber], "credit", $currency,$userAssocId,$customer->first_name. " ". $customer->last_name,$customer->email,$payment_provider_message = null,$transaction_status->tranDateTime);
                                // fire event to notify user
                                WalletCreditedJob::dispatch($wallet,$transaction,$this->royalFive);

                            }
                        });
                        logger()->alert('customer funded his wallet by transferring amount to a reserved account linked to his wallet.', (array)$transaction_status);
                        return response()->json([
                            "requestSuccessful"=> true,
                            "settlementId"=> "$request->settlementId",
                            "responseMessage"=> "success",
                            "responseCode"=> "00"
                        ],200);
                    }catch (\Exception $exception){
                        logger()->alert('error occurred in webhook', array_merge((array)$request, ['cause'=>$exception->getMessage(), 'trace'=>$exception->getTraceAsString()]));


                        $recipients = env("ADMIN_USER");
                        $alert = "Attention is Needed Urgently! A Transaction Failed to update on ".($this->royalFive ? "RoyalFive \n \n \n" :"SaanaPay \n \n \n"). print_r(array_merge((array)$request, ['cause'=>$exception->getMessage(), 'trace'=>$exception->getTraceAsString()]),true);
                        Notification::send(User::firstWhere("email",env("SUPERADMIN_USER")), new AlertNotification("Providus Bank | Settlement Pending - Webhook Error ", $alert,$recipients));

                        return response()->json(['message' => 'error occurred in webhook. Check system logs'], 500);
                    }
                }else{
                    logger()->alert("Providus Settlement ID of {$request->settlementId } ,Customer  {$request->accountNumber } not found by Account Number on SaanaPay ");
                    return response()->json(['message' => 'Customer not found by accountNumber on SaanaPay'], 404);
                }
            }else{

                if ($transaction_status->tranRemarks == "Transaction not found!!!"){
                    return response()->json([
                        "requestSuccessful"=> true,
                        "settlementId"=> "$request->settlementId",
                        "responseMessage"=> "rejected transaction",
                        "responseCode"=> "02"
                    ],409);
                }

                if (!isset($transaction_status) or !isset($transaction_status->settlementId)){
                    return response()->json([
                        "requestSuccessful"=> true,
                        "settlementId"=> "$request->settlementId",
                        "responseMessage"=> "Transaction Not found from Provider",
                        "responseCode"=> "25"
                    ],404);
                }

            }

        }else{
            return response('', 204);
        }
    }

}
