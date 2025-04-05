<?php

namespace App\Http\Requests;

use App\Traits\PaymentRequest;
use Illuminate\Foundation\Http\FormRequest;

class CardTransferRequest extends FormRequest
{
    use PaymentRequest;
    public function rules(): array
    {
        return $this->cardValidationRules();
    }
}
