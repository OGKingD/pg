<?php

namespace App\Http\Livewire;

use App\Lib\Services\Flutterwave;
use App\Lib\Services\NinePSB;
use App\Lib\Services\Providus;
use App\Lib\Services\Remita;
use App\Models\RRR;
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
    public $byPassInitiationRef;
    protected $listeners = ['byPassProvidusRef'];

    public function render()
    {
        $this->dispatchBrowserEvent('resetPage');
        return view('livewire.requery-tool',['messageType'=> $this->messageType])->extends('layouts.admin.admin_dashboardapp', ['title' => 'Requery Tool']);;
    }


    public function byPassProvidusRef()
    {
        $this->byPassInitiationRef = true;

    }
    public function getTransactionDetails(): void
    {
        $this->messageType = false;
        $processTransaction = true;


        $provider = strtoupper($this->provider);
        $trnx = trim($this->transaction_ref);

        if ( in_array($provider,['PROVIDUSOLD', 'PROVIDUS'])) {

            //call providus to get transaction details;
            /** @var object $providus */
            if ($provider === "PROVIDUSOLD"){
                $providus = (new Providus)->verifyTransactionOldPtpp($trnx);
            }
            if ($provider === "PROVIDUS"){
                //check to see if transaction is not already successful;
                $transactionExists = Transaction::firstWhere('bank_transfer_ref', $trnx);
                if ($transactionExists && $transactionExists->status === "successful") {
                    $processTransaction = false;
                    $this->message = "Transaction with settlementId : $this->transaction_ref Already Processed.";
                    $this->messageType = "info";
                }
                $providus = (new Providus)->verifyTransaction($trnx);
            }

            if (isset($providus)) {

                //check if transaction exists;
                if (!empty($providus->settlementId)) {

                    //check if initiationTranRef is missing;
                    if ($this->byPassInitiationRef){
                        $this->transactionDetails = [
                            "transaction_ref" => $providus->settlementId,
                            "amount" => $providus->transactionAmount,
                            "date" => $providus->settlementDateTime,
                            "remarks" => $providus->tranRemarks,
                            "invoice_no" => 'N/A',
                        ];
                    }
                    if (!$this->byPassInitiationRef){
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
                                "invoice_no" => $transactionExists->invoice_no,
                            ];
                        }
                    }

                }
                if (empty($providus->settlementId)) {
                    $this->message = "Transaction  : $trnx cannot be processed, $providus->tranRemarks";
                    $this->messageType = "danger";
                }

            }

        }

        if ($provider === "9PSB") {
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
                $ninePsb = (new NinePSB())->transactionStatusDynamicAccount($trnx);

                if ($ninePsb['status']) {
                    //check for PENDING;
                    $paymentStatus = strtoupper($ninePsb['payment']);

                    if ($paymentStatus === "PENDING"){
                        $this->message = "Transaction  : $trnx cannot be processed, {$ninePsb['message']}";
                        $this->messageType = "warning";
                    }

                   if ($paymentStatus !==  "PENDING"){
                       $this->transactionDetails = [
                           "transaction_ref" => $ninePsb['data']['transaction']['linkingreference'] ?? $trnx,
                           "amount" => $ninePsb['data']['order']['amount'],
                           "date" => $ninePsb['data']['transaction']['date'],
                           "remarks" => $ninePsb['message'],
                           "invoice_no" => $transactionExists->invoice_no,
                       ];
                       $this->transactionDetails = array_merge($this->transactionDetails,$ninePsb['data']);

                   }

                }
                if (!$ninePsb['status']) {
                    $this->message = "Transaction  : $trnx cannot be processed, {$ninePsb['message']}";
                    $this->messageType = "danger";
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

            if (is_null($transactionExists)){
                $processTransaction = false;
                $this->message = "Transaction  : $trnx cannot be processed, transaction Not Found";
                $this->messageType = "danger";
            }

            if ($transactionExists){
                if ($transactionExists->status === "successful"){
                    $processTransaction = false;
                    $this->message = "Transaction with settlementId : $this->transaction_ref Already Processed.";
                    $this->messageType = "info";
                }
                $flwaveInstance = new Flutterwave(config('flutterwave.secret_key'));

                if ($transactionExists->provider === "FLWAVEPERCENT"){
                    $flwaveInstance = getFlwave(true);
                }
                if ($byTranxRef){
                    $flutterwave = ($flwaveInstance)->verifyTansactionByRef($trnx);
                }
                if (!$byTranxRef){
                    $flutterwave = ($flwaveInstance)->verifyTransaction($trnx);
                }
            }


            if ($processTransaction && isset($flutterwave['data'])) {
                $payload = $flutterwave['data'];
                $status = strtoupper($payload['status']);

                if ($status=== "FAILED") {
                    $this->message = "Transaction  : $trnx cannot be processed, \n" .$payload['narration']. $payload['status'] . ' reason---> '. $payload['processor_response'];
                    $this->messageType = "danger";
                }

                if ( $status === "SUCCESSFUL"){

                    $this->transactionDetails = $flutterwave;
                    $this->transactionDetails["transaction_ref"] = $payload['tx_ref'];
                    $this->transactionDetails["amount"] = $payload['charged_amount'];
                    $this->transactionDetails["date"] = $payload['created_at'];
                    $this->transactionDetails["remarks"] = $payload['status'];
                    $this->transactionDetails["invoice_no"] = $transactionExists->invoice_no;

                }
            }
        }
        if ($provider === "REMITA") {

            $transactionExists = false;
            //check to see if transaction is not already successful;
            if (str_contains($trnx,"INV")){
                $transactionExists = RRR::with('invoice')->firstWhere('invoice_no',$trnx);
            }
            if (!str_contains($trnx,"INV")){
                if (strlen( $trnx) === 12){
                    $transactionExists = RRR::with('invoice')->firstWhere('rrr',$trnx);
                }
                if (strlen( $trnx) !== 12){
                    $transactionExists = Transaction::firstWhere('merchant_transaction_ref',$trnx)->invoice->rrr;
                }
            }


            if ($transactionExists && $transactionExists->status === "successful") {
                $processTransaction = false;
                $this->message = "Transaction with settlementId : $this->transaction_ref Already Processed.";
                $this->messageType = "info";
            }
            if (!$transactionExists){
                $processTransaction = false;
                $this->message = "No Transaction found for $trnx. Make sure the transaction exists on This Platform or try using the Invoice Number.";
                $this->messageType = "info";
            }

            if ($processTransaction) {
                //call remita to get status of transaction
                $remita = (new Remita())->rrrStatus($transactionExists->rrr);

                if ($remita['status']) {
                    //check for PENDING;
                    $paymentStatus = strtoupper($remita['data']['status']);

                    if ($paymentStatus !== "00"){
                        $this->message = "Transaction  : $trnx cannot be processed, {$remita['message']}";
                        $this->messageType = "warning";
                    }

                    if ($paymentStatus ===  "00"){
                        $this->transactionDetails = [
                            "transaction_ref" =>  $remita['data']['RRR'],
                            "invoice_no" => $transactionExists->invoice_no,
                            "orderRef" =>  $remita['data']['orderId'],
                            "orderId" =>  $remita['data']['orderId'],
                            "rrr" =>  $remita['data']['RRR'],
                            "amount" => $remita['data']['amount'],
                            "date" => $remita['data']['paymentDate'],
                            "remarks" => $remita['message'],
                        ];
                        $this->transactionDetails = array_merge($this->transactionDetails,$remita['data']);

                    }

                }

                if (!$remita['status']) {
                    $this->message = "Transaction  : $trnx cannot be processed, {$remita['message']}";
                    $this->messageType = "danger";
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
        if ( str_contains($provider,"PROVIDUS")){
            //call repush API;
            /** @var object $result */
            if ($provider === "PROVIDUS"){
                $result = (new Providus())->repushNotification($this->transaction_ref);
            }
            if ($provider === "PROVIDUSOLD"){
                $result = (new Providus())->repushNotificationOldPtpp($this->transaction_ref);
            }
            $this->formatProvidusResponse($result);
        }

        if ( $provider === "9PSB"){
            //call repush API;
            Http::withoutVerifying()->post(route('webhook.nine-psb-settlement'), $this->transactionDetails)->json();

            $this->message = "Transaction  $this->transaction_ref Pushed for requery!";
            $this->messageType = "success";
            $this->dispatchBrowserEvent('alertBox', ['type' => 'success', 'message' => $this->message]);

        }
        if ($provider === "REMITA"){
            //Push to Remita
            Http::withoutVerifying()->post(route('webhook.remita-settlement'),[$this->transactionDetails])->body();
            $this->message = "Transaction  $this->transaction_ref Pushed for requery!";
            $this->messageType = "success";
            $this->dispatchBrowserEvent('alertBox', ['type' => 'success', 'message' => $this->message]);

        }

        if ($provider === "FLUTTERWAVE"){

            //push to webhook;
            Http::withoutVerifying()->post(route('webhook.flutterwave'), $this->transactionDetails)->json();


            $this->message = "Transaction  $this->transaction_ref Pushed for requery!";
            $this->messageType = "success";
            $this->dispatchBrowserEvent('alertBox', ['type' => 'success', 'message' => $this->message]);



        }



    }

    public function refund()
    {
        $provider = strtoupper($this->provider);;
        $this->dispatchBrowserEvent('closeAlert');
        //before implementing spatie check userid as crude way
        if (in_array(request()->user()->id,[1,4,12])){
            if (str_contains($provider, "PROVIDUS")) {
                //call repush API;
                /** @var object $result */
                if ($provider === "PROVIDUS") {
                    $result = (new Providus())->refundTransaction($this->transaction_ref);
                }
                if ($provider === "PROVIDUSOLD") {
                    $result = (new Providus())->refundTransactionOldPtpp($this->transaction_ref);
                }
                $this->formatProvidusResponse($result);
            }
        }

    }

    /**
     * @param $result
     */
    protected function formatProvidusResponse($result): void
    {
        if ($result->requestSuccessful) {
            $this->message = "Transaction  $this->transaction_ref $result->responseMessage!";
            $this->messageType = "success";
            $this->dispatchBrowserEvent('alertBox', ['type' => 'success', 'message' => $this->message]);

        }
        if (!$result->requestSuccessful) {
            $this->message = "Transaction  $this->transaction_ref $result->responseMessage!";
            $this->messageType = "danger";
            $this->dispatchBrowserEvent('alertBox', ['type' => 'danger', 'message' => $this->message]);

        }
    }
}
