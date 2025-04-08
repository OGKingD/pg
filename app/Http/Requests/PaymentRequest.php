<?php

namespace App\Http\Requests;

use App\Models\Gateway;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    public array $gateways;
    public array $currencies = ['NGN','USD'];
    public function rules(): array
    {
        return [
            "name" => "required",
            "amount" => ["required", "numeric", ($this->input('channel') === "NGN") ? "min:100" : "min:1"],
            "email" => ["required", 'email:rfc'],
            'request_id' => ["required", "min:5", "max:32"],
            "channel" => ['sometimes', Rule::in($this->gateways)],
            "currency" => ['sometimes', Rule::in($this->currencies)],
            "redirect_url" => ["sometimes", "url"]
        ];
    }


    protected function prepareForValidation()
    {
        $channel = null;
        $this->gateways = [];
        $currency = "NGN";
        $quantity = $this->input('quantity') ?? 1;
        if ($this->has('channel')) {
            $this->gateways = Gateway::all()->pluck('id', 'name')->toArray();
            $channel = strtolower(str_replace(" ", "", array_search($this->input('channel'), $this->gateways)));
        }
        if ($this->has('currency')) {
            $currency = strtoupper($this->input('currency'));
        }
        $this->merge(['currency' => $currency, 'channel' => $channel, 'quantity' => $quantity]);


    }
}
