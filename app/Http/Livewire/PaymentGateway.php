<?php

namespace App\Http\Livewire;

use App\Models\Gateway;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentGateway extends Component
{
    public $gateways;

    public $gatewayId;
    public $gatewayName;
    public $gatewayStatus;

    use WithPagination;

    protected $paginationTheme = 'bootstrap';


    public function render()
    {
        $data['gatewaysCollection'] = Gateway::latest()->paginate(4);
        $data['gatewayId'] = $this->gatewayId;
        $data['gatewayName'] = $this->gatewayName;
        $data['gatewayStatus'] = $this->gatewayStatus;

        $this->gateways = ($data['gatewaysCollection'])->items();

        return view('livewire.payment-gateway',$data)->extends('layouts.admin.admin_dashboardapp',['title' => 'Payment Gateways']);
    }

    public function updatePaymentGateway()
    {
        $this->dispatchBrowserEvent('processingEvent');

        $gatewayUpdated = Gateway::where('id',$this->gatewayId)->first()->update([
            'name' => $this->gatewayName,
            'status' => $this->gatewayStatus ? 1 : 0,
        ]);

        if ($gatewayUpdated) {
            $this->dispatchBrowserEvent('gatewayUpdated');
        }

    }

    public function addPaymentGateway()
    {
        $this->dispatchBrowserEvent('processingEvent');

        $gatewayCreated = Gateway::create([
            "name" => $this->gatewayName,
            'status' => $this->gatewayStatus ? 1 : 0,
        ]);
        if ($gatewayCreated) {
            $this->dispatchBrowserEvent('gatewayCreated');
        }
    }

}
