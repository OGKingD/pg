<?php

namespace App\Traits;

use App\Models\Transaction;
use Illuminate\Foundation\Auth\Access\Authorizable;

trait RequestIsValidForMerchant
{
    use AuthorizeApi;

    public function authorize(): bool
    {
        return (bool)$this->user();
    }

    public function isRequestIdValid($request_id): \Closure
    {

        return function (string $attribute, $value, \Closure $fail) use ($request_id): void {
            $transaction = Transaction::where('merchant_transaction_ref', $request_id)
                ->where('status', '!=', 'successful')
                ->where('user_id', $this->user()->id)->first();
            $this->merge(['transaction' => $transaction]);

            if (!$transaction) {
                $fail('The request id is invalid');
            }

        };


    }
}
