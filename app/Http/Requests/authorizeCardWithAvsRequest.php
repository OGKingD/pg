<?php

namespace App\Http\Requests;

use App\Traits\PaymentRequest;
use Illuminate\Foundation\Http\FormRequest;

class authorizeCardWithAvsRequest extends FormRequest
{
    use PaymentRequest;
    public function rules(): array
    {
        $validationRules = $this->cardValidationRules();

        $validationRules['city'] = ['required', 'string', 'max:255'];
        $validationRules['address'] = ['required', 'string', 'max:255'];
        $validationRules['state'] = ['required', 'string', 'min:2', 'max:4']; // Assuming abbreviation like 'CA'
        $validationRules['country'] = ['required', 'string', 'min:2', 'max:4']; // Assuming country code like 'US','NGN',
        $validationRules['zipcode'] = ['required', 'min:4', 'max:10']; // Validating US Zipcode (5 digits or 9 with a hyphen)

        return $validationRules;

    }
}
