<?php

namespace App\Http\Livewire;

use Livewire\Component;

class PaymentLinks extends Component
{
    public function render()
    {
        return view('livewire.payment-links')->extends('layouts.merchant_dashboardapp',['title' => 'Payment Gateways']);
    }
}
