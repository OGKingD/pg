<?php

namespace App\Http\Livewire;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Component;

class InvoicePage extends Component
{
    public $invoices;
    public $invoiceDetails;

    protected $listeners = ['addInvoice'];

    public function render()
    {
        $invoices = \App\Models\Invoice::paginate(5);
        $this->invoices = $invoices->items();
        return view('livewire.invoice-page');
    }

    public function addInvoice()
    {
        $user = $this->getUser();
        $payload = json_decode($this->invoiceDetails, true, 512, JSON_THROW_ON_ERROR);
        logger($payload);
        $invoiceAdded = $user->invoice()->create([
            'invoice_no' => 'INV'.Str::random(17),
            'quantity' => $payload['quantity'],
            'customer_email' => $payload['email'],
            'due_date' => $payload['due_date'],
            'amount' => $payload['amount'],
            'name' => $payload['item_name'],
        ]);
        if ($invoiceAdded){
            $this->dispatchBrowserEvent('invoiceAdded');
        }

    }


    /**
     * @return User|\Illuminate\Contracts\Auth\Authenticatable
     */
    public function getUser()
    {
        return auth()->user();
    }

}
