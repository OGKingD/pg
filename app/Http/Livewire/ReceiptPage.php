<?php

namespace App\Http\Livewire;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class ReceiptPage extends Component
{
    public $searchVia = "";
    public $searchViavalue;
    public $hasTransactions;
    protected $listeners = ['searchTransactions'];
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $searchVia = strtoupper($this->searchVia);
        $this->hasTransactions = false;
        $transactions = null;
        if (empty($this->searchViavalue)){
            $this->dispatchBrowserEvent('closeAlert');
        }
        if (!empty($this->searchViavalue)){
            $needle = ["status" => "successful"];

            if ($searchVia === "EMAIL"){
                $needle ["email"]  = trim($this->searchViavalue);
            }
            if ($searchVia === "MERCHANT_TRANSACTION_REF"){
                $needle["merchant_transaction_ref"] = trim($this->searchViavalue);

            }
            $this->hasTransactions = true;
            $transactions = Transaction::reportQuery($needle)->paginate(3);
        }
        $this->dispatchBrowserEvent('closeAlert');

        return view('livewire.receipt-page',['transactions' => $transactions])->extends('layouts.app', ["title" => "Search Transactions "]);
    }



    public function searchTransactions ()
    {





    }

}
