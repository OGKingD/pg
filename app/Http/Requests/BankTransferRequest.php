<?php

namespace App\Http\Requests;

use App\Models\Gateway;
use App\Models\Transaction;
use App\Traits\PaymentRequest;
use Illuminate\Foundation\Http\FormRequest;

class BankTransferRequest extends FormRequest
{
    use PaymentRequest;
    public function rules(): array
    {
        return [
            'currency' => ['sometimes'],
            'request_id' => ['required',$this->isRequestIdValid($this->input('request_id'))],
            'bank_transfer' => ['sometimes'],
            'expires_at' => ['sometimes','integer'],
            'account_name_prefix' => ['sometimes',"max:5","min:3"],
            'amount' => ['required',$this->amountTalliesWithTotal($this->input('amount'))],

        ];
    }

    protected function prepareForValidation()
    {
        $gateway_id = Gateway::firstWhere('name','Bank Transfer')->id;
        $this->merge(['gateway_id' => $gateway_id]);
    }






}
