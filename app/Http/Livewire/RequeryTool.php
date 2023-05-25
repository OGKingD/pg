<?php

namespace App\Http\Livewire;

use App\Lib\Services\Flutterwave;
use App\Lib\Services\Providus;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class RequeryTool extends Component
{
    public $provider;
    public $transaction_ref;
    public $transactionDetails = [];
    public $message = false;
    private $messageType;

    public function render()
    {
        return view('livewire.requery-tool',['messageType'=> $this->messageType])->extends('layouts.admin.admin_dashboardapp', ['title' => 'Requery Tool']);;
    }


    public function getTransactionDetails(): void
    {
        $this->messageType = false;
        $processTransaction = true;


        $provider = strtoupper($this->provider);
        $trnx = trim($this->transaction_ref);

        if ($provider === "PROVIDUS") {
            //check to see if transaction is not already successful;
            $transactionExists = Transaction::firstWhere('bank_transfer_ref', $trnx);
            if ($transactionExists && $transactionExists->status === "successful") {
                $processTransaction = false;
                $this->message = "Transaction with settlementId : $this->transaction_ref Already Processed.";
                $this->messageType = "info";
            }

            if ($processTransaction) {
                //call providus to get transaction details;
                /** @var object $providus */
                $providus = (new Providus)->verifyTransaction($trnx);
                if (isset($providus)) {

                    //check if transaction exists;
                    if (!empty($providus->settlementId)) {

                        //check if initiationTranRef is missing;
                        if (empty($providus->initiationTranRef)) {
                            $this->message = "Transaction with settlementId : $providus->settlementId cannot be processed, initiationTranRef is Missing. Dynamic Account Expired when Payment was made.";
                            $this->messageType = "danger";
                        }
                        if (!empty($providus->initiationTranRef)) {
                            $this->transactionDetails = [
                                "transaction_ref" => $providus->settlementId,
                                "amount" => $providus->transactionAmount,
                                "date" => $providus->settlementDateTime,
                                "remarks" => $providus->tranRemarks,
                            ];
                        }
                    }
                    if (empty($providus->settlementId)) {
                        $this->message = "Transaction  : $trnx cannot be processed, $providus->tranRemarks";
                        $this->messageType = "danger";
                    }

                }
            }

        }
        if ($provider === "FLUTTERWAVE") {

            //check to see if transaction is not already successful;
            ////check to see if transaction is prefixed with RV_ or SPAY
            $byTranxRef = false;
            if (str_contains($trnx,"RV_") || str_contains($trnx,"SPAY")){
                $transactionExists = Transaction::firstWhere('spay_ref',$trnx);
                $byTranxRef = true;
            }else{
                $transactionExists = Transaction::firstWhere('flutterwave_ref',$trnx);
            }
            if ($transactionExists && $transactionExists->status === "successful") {
                $processTransaction = false;
                $this->message = "Transaction with settlementId : $this->transaction_ref Already Processed.";
                $this->messageType = "info";
            }
            if (is_null($transactionExists)){
                $processTransaction = false;
                $this->message = "Transaction  : $trnx cannot be processed, transaction Not Found";
                $this->messageType = "danger";
            }
            if ($byTranxRef){
                $flutterwave = (new Flutterwave(config('flutterwave.secret_key')))->verifyTansactionByRef($trnx);
            }
            if (!$byTranxRef){
                $flutterwave = (new Flutterwave(config('flutterwave.secret_key')))->verifyTransaction($trnx);
            }
            if ($processTransaction) {
                if (isset($flutterwave)) {

                    if ($flutterwave['status'] === "error") {
                        $this->message = "Transaction  : $trnx cannot be processed, " . $flutterwave['message'];
                        $this->messageType = "danger";
                    }
                    $payload = $flutterwave['data'];
                    if (isset($payload['status'])){
                        if ( strtoupper($payload['status']) === "SUCCESSFUL"){
                            $this->transactionDetails = $flutterwave;
                            $this->transactionDetails["transaction_ref"] = $payload['tx_ref'];
                            $this->transactionDetails["amount"] = $payload['charged_amount'];
                            $this->transactionDetails["date"] = $payload['created_at'];
                            $this->transactionDetails["remarks"] = $payload['status'];

                        }
                    }


                }

            }
        }

        $this->dispatchBrowserEvent('closeAlert');

        if ($this->transactionDetails) {
            $this->dispatchBrowserEvent('alertBox', ['type' => 'success']);
        }


    }

    public function requery()
    {
        $provider = strtoupper($this->provider);;
        $this->dispatchBrowserEvent('closeAlert');
        if ( $provider === "PROVIDUS"){
            //call repush API;
            /** @var object $result */
            $result = (new Providus())->repushNotification($this->transaction_ref);
            if ($result->requestSuccessful){
                $this->message = "Transaction  $this->transaction_ref $result->responseMessage!";
                $this->messageType = "success";
                $this->dispatchBrowserEvent('alertBox', ['type' => 'success', 'message' => $this->message]);

            }
            if (!$result->requestSuccessful){
                $this->message = "Transaction  $this->transaction_ref $result->responseMessage!";
                $this->messageType = "danger";
                $this->dispatchBrowserEvent('alertBox', ['type' => 'danger', 'message' => $this->message]);

            }
        }

        if ($provider === "FLUTTERWAVE"){

            //push to webhook;
            $response = Http::withoutVerifying()->post(route('webhook.flutterwave'), $this->transactionDetails)->json();


            $this->message = "Transaction  $this->transaction_ref Pushed for requery!";
            $this->messageType = "success";
            $this->dispatchBrowserEvent('alertBox', ['type' => 'success', 'message' => $this->message]);



        }



    }
}
