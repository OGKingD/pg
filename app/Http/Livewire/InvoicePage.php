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
    public $invoiceName;
    public $invoiceNo;
    public $invoiceQuantity;
    public $invoiceAmount;
    public $invoiceEmail;
    public $invoiceDueDate;

    protected $listeners = ['addInvoice','updateInvoice'];

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

    public function openEditInvoiceModal($payload)
    {
        //set the values;
        $this->invoiceNo = $payload['invoice_no'];
        $this->invoiceName = $payload['name'];
        $this->invoiceQuantity = $payload['quantity'];
        $this->invoiceAmount = $payload['amount'];
        $this->invoiceEmail = $payload['customer_email'];
        $this->invoiceDueDate = $payload['due_date'];

        $this->dispatchBrowserEvent('openEditInvoiceModal');

    }

    public function updateInvoice()
    {
        $user = $this->getUser();
        $payload = json_decode($this->invoiceDetails, true, 512, JSON_THROW_ON_ERROR);
        $invoiceAdded = $user->invoice()->where('invoice_no', $this->invoiceNo)->update([
            'quantity' => $payload['quantity'],
            'customer_email' => $payload['email'],
            'due_date' => $payload['due_date'],
            'amount' => $payload['amount'],
            'name' => $payload['item_name'],
        ]);
        if ($invoiceAdded){
            $this->dispatchBrowserEvent('invoiceUpdated');
        }

    }

}
