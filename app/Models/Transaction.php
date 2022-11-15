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


        //check if group_by is used;
        if (isset($query['group_by'])) {
            /** @var Builder $grp_by_query */
            //determine the relationships to load;
            $relation = [];
            $grp_by = $query['group_by'];

            unset($query['filename'], $query['group_by']);
            $builder = "";
            $exclude_from_columns_to_select = ['end_date'];
            $columns_to_select = array_diff(array_keys($query), $exclude_from_columns_to_select);
            if ($grp_by === "gateway_id") {
                $relation[] = "gateway";
                $columns_to_select[] = "gateway_id";
            }
            if ($grp_by === "user_id") {
                $relation[] = "user";
                $columns_to_select[] = "user_id";
            }
            //user didn't select any search criteria;
            if (empty($columns_to_select)) {
                $columns_to_select[] = "user_id";
            }
            if (!in_array($grp_by, ['user_id', 'gateway_id'])) {
                $columns_to_select[] = $grp_by;
            }

            if (count($relation)) {
                $builder = self::with($relation)->select($columns_to_select);
            }

            if (empty($relation)) {
                $builder = self::select($columns_to_select);
            }

            $builder
                ->selectRaw("@rn:=@rn+1  AS id")
                ->selectRaw("COUNT(id) as transaction_count")
                ->selectRaw("SUM(amount) as amount")
                ->selectRaw("SUM(fee) as fee")
                ->selectRaw("SUM(total) as total")->where($queryArray);

            $grp_by_query = queryWithDateRange($query, $builder);

            $grp_by_query->groupBy($columns_to_select)->orderBy('id');


            return $grp_by_query;
        }

        $builder = self::with(['invoice', 'gateway', 'user'])->select($columns_to_select)->where($queryArray);

        info("Transaction Report Query parameters is :", $queryArray);

        return queryWithDateRange($query, $builder);

    }

    public static function summaryReport(array $payload)
    {
        $file = fopen(storage_path("logs/{$payload['filename']}"), "wb");
        $query = self::reportQuery($payload);

        $query->chunk(3000, function ($results) use ($file) {
            //Define Headers;
            $csvHeaders = [];
            //group by user_id, status, flag,gateway_id
            $headers = $results[0]->original;
            foreach ($headers as $key => $head) {
                $title = strtoupper($key);
                if ($title === "ID") {
                    continue;
                }
                if ($title === "GATEWAY_ID") {
                    $csvHeaders[] = "CHANNEL";
                    continue;
                }
                if ($title === "USER_ID") {
                    $csvHeaders[] = "MERCHANT";
                    continue;
                }

                $csvHeaders[] = $title;
            }

            fputcsv($file, $csvHeaders);
            foreach ($results as $result) {
                $contents = [];

                $data = $result->original;

                //Define Content;
                foreach ($data as $key => $datum) {

                    if ($key === "id") {
                        continue;
                    }
                    if ($key === "gateway_id") {
                        $contents[] = $result->gateway->name ?? "N/A";
                        continue;
                    }
                    if ($key === "user_id") {
                        $contents[] = "{$result->user->first_name} {$result->user->last_name}";
                        continue;
                    }
                    if ($key === "amount") {
                        $contents[] = number_format($result->amount, 2);
                        continue;
                    }
                    if ($key === "fee") {
                        $contents[] = number_format($result->fee, 2);
                        continue;
                    }
                    if ($key === "total") {
                        $contents[] = number_format($result->total, 2);
                        continue;
                    }

                    $contents[] = $datum;
                }

                fputcsv($file, $contents);
            }
        });
        fclose($file);

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
        $transaction = self::with('gateway')->select(["merchant_transaction_ref", "invoice_no", "gateway_id", "amount", "fee", "total", "description", "status", "flag", "currency", "updated_at"])->first()->toArray();
        $transaction["channel"] = $transaction['gateway']['name'];
        unset($transaction['gateway_id'], $transaction['gateway']);
        return $this->details["redirect_url"] . "?" . http_build_query($transaction);


    }
}
