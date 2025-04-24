<?php

namespace App\Http\Requests;

use App\Traits\PaymentRequest;
use Illuminate\Foundation\Http\FormRequest;

class authorizeCardWithPinRequest extends FormRequest
{
    use PaymentRequest;
    public function rules(): array
    {
        $validationRules = $this->cardValidationRules();
        $validationRules['pin'] = ['required', 'min:4','max:6'];
        return $validationRules;
    }
}
