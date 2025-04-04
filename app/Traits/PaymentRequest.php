<?php

namespace App\Traits;

use App\Models\Transaction;
use Illuminate\Foundation\Auth\Access\Authorizable;

trait PaymentRequest
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

    public function amountTalliesWithTotal($amount): \Closure
    {
        return function ($attribute, $value, \Closure $fail) use ($amount) {
            /** @var Transaction $transaction */
            $transaction = $this->input('transaction');
            $gateway_id = $this->input('gateway_id');
            if ($transaction) {
                $transactionTotal = $transaction->computeChargeAndTotal($gateway_id);
                if ((float)$transactionTotal['total'] != floatval($amount)) {
                    $fail("The amount does not tally! Please use the compute charge endpoint to get the breakdown of the transaction amount");
                }
            }
        };

    }

}
