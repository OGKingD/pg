<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = ['details' => 'json'];

    public static function generateCsvReport(array $payload, array $csvHeaders)
    {
        $file = fopen(storage_path("logs/{$payload['filename']}"), "wb");
        /** @var Builder $query */
        $query = self::reportQuery($payload);
        $query->chunk(3000, function ($results) use ($file, $csvHeaders) {
            //Define Headers;
            fputcsv($file, $csvHeaders);
            foreach ($results as $result) {
                //Define Content;
                $contents = [
                    $result->merchant_transaction_ref,
                    $result->gateway->name ?? "N/A",
                    number_format($result->amount, 2),
                    number_format($result->fee, 2),
                    number_format($result->total, 2),
                    $result->description,
                    $result->status,
                    $result->flag,
                    $result->created_at,
                ];
                fputcsv($file, $contents);
            }
        });
        fclose($file);

    }

    /**
     * @param $query
     */
    public static function reportQuery($query): Builder
    {
        $queryArray = [];
        $columns_to_select = [
            "transaction_ref",
            'merchant_transaction_ref',
            'gateway_id',
            'amount',
            'fee',
            'total',
            'description',
            'status',
            'flag',
            'created_at'];

        if (isset($query['transaction_ref'])) {
            $queryArray[] = ['transaction_ref', '=', (string)($query['transaction_ref'])];
        }
        if (isset($query['merchant_transaction_ref'])) {
            $queryArray[] = ['merchant_transaction_ref', '=', (string)($query['merchant_transaction_ref'])];
        }
        if (isset($query['gateway_id'])) {
            $queryArray[] = ['gateway_id', '=', (int)($query['gateway_id'])];
        }
        if (isset($query['amount'])) {
            $queryArray[] = ['amount', '=', ($query['amount'])];
        }
        if (isset($query['total'])) {
            $queryArray[] = ['total', '=', (string)($query['total'])];
        }
        if (isset($query['description'])) {
            $queryArray[] = ['description', 'like', "%" . $query['transaction_ref'] . "%"];
        }
        if (isset($query['status'])) {
            $queryArray[] = ['status', '=', (string)($query['status'])];
        }
        if (isset($query['flag'])) {
            $queryArray[] = ['flag', '=', (string)($query['flag'])];
        }
        if (isset($query['user_id'])) {
            $queryArray[] = ['user_id', '=', (string)($query['user_id'])];
        }
        if (isset($query['transaction_ref'])) {
            $queryArray[] = ['transaction_ref', '=', (string)($query['transaction_ref'])];
        }

        $builder = self::with(['invoice', 'gateway', 'user'])->select($columns_to_select)->where($queryArray);

        info("Transaction Report Query parameters is :", $queryArray);

        if ((array_key_exists('created_at', $query) && !empty($query['created_at'])) && (array_key_exists('end_date', $query) && !empty($query['end_date']))) {
            return $builder->whereBetween("created_at", [$query['created_at'], $query['end_date'] . " 23:59:59.999",]);
        }
        if (array_key_exists('created_at', $query) && !empty($query['created_at'])) {
            return $builder->whereDate('created_at', $query['created_at']);
        }
        if (array_key_exists('end_date', $query) && !empty($query['end_date'])) {
            return $builder->whereDate('created_at', $query['end_date']);
        }
        return $builder;

    }

    public function user()
    {
        return $this->belongsTo(User::class);

    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_no', 'invoice_no');

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
    public function handleSuccessfulPayment(Transaction $transaction, $gateway_id, $payment_provider_message, array $details, Wallet $wallet, User $user, User $company): bool
    {
        $status = false;

        try {
            DB::transaction(function () use ($company, $user, $wallet, $payment_provider_message, $gateway_id, $details, $transaction, &$status) {
                /** @var Invoice $invoice */
                $invoice = $transaction->invoice;
                $details = array_merge($transaction->details, $details);
                $transaction->update([
                    "status" => "successful",
                    "gateway_id" => $gateway_id,
                    "payment_provider_message" => $payment_provider_message,
                    "details" => $details
                ]);
                $invoice->update([
                    'status' => 'successful'
                ]);//credit merchant wallet with amount - charge
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

                    $status = true;

                }

            });
        } catch (\Throwable $e) {
            info("Error Happened while handling successful payment: ", ['cause' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
        return $status;
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

    public function merchantRedirectUrl()
    {
        $transaction = $this->only(["merchant_transaction_ref", "invoice_no", "amount", "fee", "total", "description", "status", "flag", "currency"]);
        return $this->details["redirect_url"] . "?" . http_build_query($transaction);


    }
}
