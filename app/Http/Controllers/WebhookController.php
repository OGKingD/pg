<?php

namespace App\Http\Controllers;

use App\Lib\Services\Flutterwave;
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

            $statusCode = 406;//check to see if payload has data['id']

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
                        $company = User::firstWhere('email', 'business@saanapay.ng');
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

                                    DB::transaction(function () use ($details, $gateway_id, $flwavePayload, $transactionExists, $wallet, $user, $company) {

                                        $payment_provider_message = $flwavePayload['flw_ref'] . " " . $flwavePayload['processor_response'];

                                        $transactionExists->handleSuccessfulPayment($transactionExists, $gateway_id, $payment_provider_message, $details, $wallet, $user, $company);

                                    });
                                }

                                //check if flutterewave ever sends failed;
                                if ($flwavePayload['status'] === "failed") {
                                    $responseMessage = "failed";

                                    $transactionExists->update([
                                        "status" => "failed",
                                        "gateway_id" => $gateway_id,
                                        "payment_provider_message" => $flwavePayload['flw_ref'] . " " . $flwavePayload['processor_response'],
                                        "details" => $details
                                    ]);

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
}
