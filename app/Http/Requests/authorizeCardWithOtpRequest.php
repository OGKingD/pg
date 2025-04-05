<?php

namespace App\Http\Requests;

use App\Traits\PaymentRequest;
use Illuminate\Foundation\Http\FormRequest;

class authorizeCardWithOtpRequest extends FormRequest
{
    use PaymentRequest;
    public function rules(): array
    {
        return  [
            'otp' => ['required', 'min:4','max:8'],
            'request_id' => ['required',$this->isRequestIdValid($this->input('request_id'))],
        ];
    }
}
