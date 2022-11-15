<?php

namespace App\Http\Livewire;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
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

    public $searchQuery;

    public $perPage;

    public function render()
    {

        $data['invoicesCollection'] = $this->getInvoices();

        return view('livewire.invoice-page', $data);
    }

    public function addInvoice()
    {

        $invoiceAdded = "";
        DB::transaction(function () use (&$invoiceAdded) {
            $user = $this->getUser();
            $payload = json_decode($this->invoiceDetails, true, 512, JSON_THROW_ON_ERROR);
            $request_id = Str::random(17);
            $trn_details = [];
            $amount = $payload['amount'];
            $redirect_url = $payload['redirect_url'] ?? null;


            /** @var Invoice $invoiceAdded */
            $invoiceAdded = $user->invoice()->create([
                'invoice_no' => 'INV'. $request_id,
                'quantity' => $payload['quantity'],
                'customer_email' => $payload['email'],
                'due_date' => $payload['due_date'],
                'amount' => $amount,
                'name' => $payload['item_name'],
            ]);
            //check if merchantRedirectURL is set and add it ;
            if (isset($redirect_url)) {
                $trn_details['redirect_url'] = $redirect_url;
            }
            //Add Transaction;
            $invoiceAdded->transaction()->create([
                "transaction_ref" => $request_id,
                "user_id" => $invoiceAdded->user_id,
                "merchant_transaction_ref" => $request_id,
                "status" => "pending",
                "amount" => $amount,
                'details' => $trn_details,
                "flag" => "debit"
            ]);
        });

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

    public function getInvoices()
    {
        $user = $this->getUser();
        $userId = $user->id;
        $isAdmin = $user->type < 5;

        /** @var Builder $builder */

        if (!$isAdmin) {
            //add user_id;
            $this->searchQuery['user_id'] = $userId;
            $builder = Invoice::reportQuery($this->searchQuery);

        }
        if ($isAdmin) {
            $builder = Invoice::reportQuery($this->searchQuery);
        }

        $invoiceCollection = $builder->paginate($this->perPage);

        $this->invoices = $invoiceCollection->items();

        return $invoiceCollection;

    }

}
