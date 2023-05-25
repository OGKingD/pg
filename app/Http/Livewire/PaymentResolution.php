<?php

namespace App\Http\Livewire;

use App\Lib\Services\Flutterwave;
use App\Lib\Services\Providus;
use App\Models\DynamicAccount;
use App\Models\Transaction;
use Livewire\Component;

class PaymentResolution extends Component
{
    public $channel;
    public $transaction_ref;
    public $transactionExists;
    private $paymentProviderDetails;
    public $messageType;
    public $message;
    public function render()
    {
        return view('livewire.payment_resolution')->extends('layouts.admin.admin_dashboardapp', ['title' => 'Resolution']);
    }

    public function getTransactionDetails()
    {
        if ((int)$this->channel === 1){
            //card
            if (str_contains($this->transaction_ref,"RV_") || str_contains($this->transaction_ref,"SPAY")){
                $this->transactionExists = Transaction::firstWhere('spay_ref',$this->transaction_ref);
                $this->paymentProviderDetails = (new Flutterwave(config('flutterwave.secret_key')))->verifyTansactionByRef($this->transactionExists->spay_ref);

            }else{
                $this->transactionExists = Transaction::firstWhere('flutterwave_ref',$this->transaction_ref);
                $this->paymentProviderDetails = (new Flutterwave(config('flutterwave.secret_key')))->verifyTransaction($this->transaction_ref);

            }
            $this->message .= "Provider Details :  ". json_encode($this->paymentProviderDetails['data'], JSON_THROW_ON_ERROR);
            $this->messageType = "info";

        }

        if ((int)$this->channel === 2){
            //bank_transfer
            //check from providus

            $this->paymentProviderDetails = (new Providus)->verifyTransaction($this->transaction_ref);

            if (isset($this->paymentProviderDetails->initiationTranRef)){
                //check if it exist in dynamic Accounts;
                $dynAccResult = DynamicAccount::with(['invoice'])->where('initiationTranRef',$this->paymentProviderDetails->initiationTranRef)->get();
                if (!$dynAccResult->count() ){
                    $this->message = "No Record for Transaction with settlementId : $this->transaction_ref !
                     Please send to Tech to try to pull up invoice associated with transaction and Process.\n". json_encode($this->paymentProviderDetails, JSON_THROW_ON_ERROR);
                    $this->messageType = "info";

                }
                if ($dynAccResult->count()){

                    $this->messageType = "warning";
                    $this->message = "Possible Payment After Account Expired! <br>";

                    $this->message .= '
                    <div class="table-responsive">
                    <table id="entries" class="table mb-0">
                        <thead>
                        <tr>
                            <th>InvoiceNO</th>
                            <th>InitiationRef</th>
                            <th>AccountNumber</th>
                            <th>Status</th>
                            <th>Payment Channel</th>
                            <th>Customer Email</th>
                            <th>Customer Name</th>
                        </tr>
                        </thead>
                        <tbody>
        ';

                    foreach ($dynAccResult as $item) {

                        $this->message .= ' <tr>
            <td>
                '.$item->invoice_no.'
            </td>
            <td>
                '.$item->initiationTranRef.'
            </td>
            <td>
                '.$item->account_number.'
            </td>
            <td>
                '.$item->invoice->status.'
            </td>
            <td>
                '.$item->invoice->gateway->name.'
            </td>
             <td>
                '.$item->invoice->customer_email.'
            </td>
             <td>
                '.$item->invoice->customer_name.'
            </td>

        </tr>
        ';
                    }
                    $this->message .= '</tbody>
                                </table>
                            </div>
';
                    $this->message .= "Provider Details :  ". json_encode($this->paymentProviderDetails, JSON_THROW_ON_ERROR);
                }

            }
            if (!isset($this->paymentProviderDetails->initiationTranRef)){
                //does not exist on provider;
                $this->message = "Transaction  : $this->transaction_ref cannot be processed, {$this->paymentProviderDetails->tranRemarks}";
                $this->messageType = "danger";
            }

        }
        $this->dispatchBrowserEvent('closeAlert');


    }
}
