<?php

namespace App\Http\Requests;

use App\Models\Gateway;
use App\Models\Transaction;
use App\Traits\RequestIsValidForMerchant;
use Illuminate\Foundation\Http\FormRequest;

class BankTransferRequest extends FormRequest
{
    use RequestIsValidForMerchant;
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


    public function amountTalliesWithTotal($amount): \Closure
    {
        return function ($attribute, $value, \Closure $fail) use ($amount) {
            /** @var Transaction $transaction */
            $transaction = $this->input('transaction');
            $gateway_id = $this->input('gateway_id');
            $transactionTotal = $transaction->computeChargeAndTotal($gateway_id);

            if ((float)$transactionTotal['total'] != floatval($amount) ){
                $fail("The amount does not tally! Please use the compute charge endpoint to get the breakdown of the transaction amount");
            }
        };

    }




}
