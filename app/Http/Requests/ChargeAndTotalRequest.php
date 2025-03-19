<?php

namespace App\Http\Requests;

use App\Models\Gateway;
use App\Traits\RequestIsValidForMerchant;
use Illuminate\Foundation\Http\FormRequest;

class ChargeAndTotalRequest extends FormRequest
{
    use RequestIsValidForMerchant;

    public function rules(): array
    {
        return [
            'request_id' => ['required', $this->isRequestIdValid($this->input('request_id')),],
            'channel' => ['required', 'in:Bank Transfer,Card'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(['channel' => ucwords(str_replace('_', ' ', $this->input('channel')))]);
    }

}
