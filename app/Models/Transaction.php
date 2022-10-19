<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts =  ['details' => 'json'];

    public function user()
    {
        return $this->belongsTo(User::class);

    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class,'invoice_no','invoice_no');

    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class);

    }
    /**
     * @param Transaction $transaction
     * @param mixed $gateway_id
     * @param mixed $payment_provider_message
     * @param array $details
     * @param mixed $wallet
     * @param User $user
     * @param User $company
     */
    public function handleSuccessfulPayment(Transaction $transaction,  $gateway_id, $payment_provider_message, array $details, Wallet $wallet, User $user, User $company): bool
    {
        /** @var Invoice $invoice */
        $invoice = $transaction->invoice;
        $details = array_merge($transaction->details,$details);

        $transaction->update([
            "status" => "successful",
            "gateway_id" => $gateway_id,
            "payment_provider_message" => $payment_provider_message,
            "details" => $details
        ]);

        $invoice->update([
            'status' => 'successful'
        ]);


        //credit merchant wallet with amount - charge
        $amount = $transaction->amount;
        $fee = $transaction->fee;

        if ($wallet->credit($amount)) {

            //add Transaction
            $user->transaction()->create([
                "transaction_ref" => "credit_{$transaction->invoice_no}",
                "merchant_transaction_ref" => "credit_{$transaction->merchant_transaction_ref}",
                "gateway_id" => $gateway_id,
                "amount" => $amount,
                "total" => $amount,
                "description" => "Credit payment for $transaction->invoice_no}",
                "status" => "successful",
                "flag" => "credit",
            ]);
            $company->transaction()->create([
                "transaction_ref" => "fee_credit_{$transaction->invoice_no}",
                "merchant_transaction_ref" => "fee_credit_{$transaction->merchant_transaction_ref}",
                "gateway_id" => $gateway_id,
                "amount" => $fee,
                "total" => $fee,
                "description" => "Fee payment for $transaction->invoice_no}",
                "status" => "successful",
                "flag" => "credit",
            ]);

            return true;

        }
        return false;
    }

    /**
     * @param Transaction $transaction
     * @param $gateway_id
     * @param $payment_provider_message
     * @param array $details
     */
    public function handleFailedPayment(Transaction $transaction, $gateway_id, $payment_provider_message, array $details): bool
    {
        $transaction->update([
            "status" => "failed",
            "gateway_id" => $gateway_id,
            "payment_provider_message" => $payment_provider_message,
            "details" => $details
        ]);

        return true;

    }
}
