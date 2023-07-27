<?php

namespace App\Models;

use App\Jobs\PushtoWebhookJob;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = ['details' => 'json'];

    public  function generateCsvReport(array $payload, array $csvHeaders)
    {
        $file = fopen(storage_path("logs/{$payload['filename']}"), "wb");
        $query = self::reportQuery($payload)->orderBy('user_id','desc')->orderBy('updated_at');
        //5,6,7
        $totalFee= $totalAmount = $totalSum =0;
        $query->chunk(3000, function ($results) use ($file, $csvHeaders, &$totalFee, &$totalAmount, &$totalSum) {
            //Define Headers;
            fputcsv($file, $csvHeaders);
            foreach ($results as $result) {
                $totalFee += $result->fee;
                $totalAmount += $result->amount;
                $totalSum += $result->total;
                //Define Content;
                $contents = [
                    $result->user->first_name. " ". $result->user->last_name,
                    $result->merchant_transaction_ref,
                    $result->status,
                    $result->gateway->name ?? "N/A",
                    $result->provider ?? "N/A",
                    $result->type,
                    number_format($result->fee, 2),
                    number_format($result->amount, 2),
                    number_format($result->total, 2),
                    $result->invoice->customer_name ?? "N/A",
                    $result->invoice->customer_email ?? "N/A",
                    $result->flag,
                    $result->updated_at,
                ];
                fputcsv($file, $contents);
            }
        });
        fputcsv($file,["","","","","", number_format($totalFee, 2),
            number_format($totalAmount, 2),
            number_format($totalSum, 2),"",""]);
        fclose($file);

    }

    /**
     * @param $query
     */
    public static function reportQuery($query): Builder
    {
        $queryArray = [];
        $columns_to_select = [
            'merchant_transaction_ref',
            'flutterwave_ref',
            'bank_transfer_ref',
            'remita_ref',
            'provider',
            'gateway_id',
            'transactions.user_id',
            'transactions.invoice_no',
            'type',
            'transactions.amount',
            'fee',
            'total',
            'description',
            'transactions.status',
            'flag',
            'details',
            'transactions.created_at',
            'transactions.updated_at'
        ];

        $builder = self::with(['invoice', 'gateway', 'user'])->select($columns_to_select);
        if (isset($query['spay_ref'])) {
            $queryArray[] = ['spay_ref', '=', (string)($query['spay_ref'])];
        }
        if (isset($query['email'])) {
            $queryArray[] = ['invoices.customer_email', '=', (string)($query['email'])];
            $builder->leftJoin('invoices','transactions.invoice_no',"=","invoices.invoice_no");
        }
        if (isset($query['customer_name'])) {
            $queryArray[] = ['invoices.customer_name', 'like', '%'.(string)($query['customer_name']).'%'];
            $builder->leftJoin('invoices','transactions.invoice_no',"=","invoices.invoice_no");
        }

        if (isset($query['bank_transfer_ref'])) {
            $queryArray[] = ['bank_transfer_ref', '=', (string)($query['bank_transfer_ref'])];
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
            $queryArray[] = ['transactions.user_id', '=', (string)($query['user_id'])];
        }


        //check if group_by is used;
        if (isset($query['group_by'])) {
            /** @var Builder $grp_by_query */
            //determine the relationships to load;
            $relation = [];
            $grp_by = $query['group_by'];

            unset($query['filename'], $query['group_by']);
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

        $builder = $builder->where($queryArray)->whereNotNull("transactions.invoice_no");

        info("Transaction Report Query parameters is :", $queryArray);

        return queryWithDateRange($query, $builder);

    }

    public function summaryReport(array $payload)
    {
        $file = fopen(storage_path("logs/{$payload['filename']}"), "wb");
        $queryArray = [];
        if (isset($payload['status'])) {
            $queryArray[] = ['status', '=', (string)($payload['status'])];
        }
        if (isset($payload['flag'])) {
            $queryArray[] = ['flag', '=', (string)($payload['flag'])];
        }
        if (isset($payload['user_id'])) {
            $queryArray[] = ['user_id', '=', (string)($payload['user_id'])];
        }
        if (isset($payload['gateway_id'])) {
            $queryArray[] = ['gateway_id', '=', $payload['gateway_id'] ];
        }
        //Default Query
        $groupBy = $payload['group_by'];
        unset($payload['group_by'], $payload['filename']);

        $builder = self::with(['user','gateway'])->where($queryArray);

        //check what type of grouping;
        $result = $this->summaryReportQuery($groupBy, $file, $builder);
        $sql = $result['query'];
        $gateways = $result['gateways'];

        $this->writeSummaryReport($groupBy,$sql,$payload,$file,$gateways);

        fclose($file);

    }

    public function writeSummaryReport($type,$query,$payload,$file, $gateways)
    {

        queryWithDateRange($payload, $query)->chunk(100,function ($results) use ($file,$type,$gateways){
            $currentMerchant = 0;
            $summationArray = [
                "successful_bills" => 0,"pending_bills" => 0,"failed_bills" => 0,"total_fees" => 0,"total_amount" => 0,
                "total" => 0,
            ];
            $grandTotalArray = [
                "successful_bills" => 0,"pending_bills" => 0,"failed_bills" => 0,"total_fees" => 0,"total_amount" => 0,
                "total" => 0,
            ];
            $noOfIterations = count($results);
            foreach ($results as $key => $result) {
                [ $currentMerchant,$summationArray ] = $this->generateContentForReport($type, $result, $file, $gateways,$summationArray,$grandTotalArray, $currentMerchant, $key, $noOfIterations);
            }
        });


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

    public function dynamicAccount()
    {
        return $this->hasOne(DynamicAccount::class,'invoice_no');

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
                ]);

                //for Bank transfers close out the dynamic account
                /** @var DynamicAccount $dynamicAccount */
                $dynamicAccount = $invoice->dynamicAccount;
                if (isset($dynamicAccount)){
                    $dynamicAccount->update([
                        'status' => 0,
                    ]);
                }
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
                        "type" => "wallet",
                    ]);
                    if ($fee > 0){
                        $company->transaction()->create([
                            "transaction_ref" => "fee_credit_{$transaction->invoice_no}",
                            "merchant_transaction_ref" => "fee_credit_{$transaction->merchant_transaction_ref}",
                            "gateway_id" => $gateway_id,
                            "amount" => $fee,
                            "total" => $fee,
                            "description" => "Fee payment for $transaction->invoice_no}",
                            "status" => "successful",
                            "flag" => "credit",
                            "type" => "wallet",
                        ]);

                    }
                    $status = true;

                }
                PushtoWebhookJob::dispatch($transaction)->delay(now()->addMinutes(3));

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
     * @return bool
     */
    public function handleFailedPayment(Transaction $transaction, $gateway_id, $payment_provider_message, array $details): bool
    {
        /** @var Invoice $invoice */
        $invoice = $transaction->invoice;
        $transaction->update([
            "status" => "failed",
            "gateway_id" => $gateway_id,
            "payment_provider_message" => $payment_provider_message,
            "details" => $details
        ]);
        $invoice->update([
            'status' => 'failed'
        ]);
        return true;

    }

    public function merchantRedirectUrl()
    {
        $transaction = self::with('gateway')->firstWhere('id', $this->id);
        return $this->details["redirect_url"] . "?" . http_build_query($transaction->transactionToPayload());


    }

    /**
     * @param Transaction $transaction
     * @return array
     */
    public function transactionToPayload(): array
    {
        $payload = $this->only(["gateway","transaction_ref", "merchant_transaction_ref", "invoice_no", "gateway_id", "amount", "description", "status", "flag", "currency","details", "updated_at"]);
        $payload['customer_name'] = $payload['details']['name'] ?? null;
        $payload['customer_email'] = $payload['details']['email'] ?? null;
        $payload["channel"] = $payload['gateway']['name'] ?? "N/A";
        $payload["total"] = $payload['amount'];
        $payload['updated_at'] = str_replace("T"," ",Carbon::parse($payload['updated_at'])->toDateTimeLocalString());
        $payload['created_at'] = str_replace("T"," ",Carbon::parse($payload['updated_at'])->toDateTimeLocalString());
        unset($payload['gateway_id'], $payload['gateway'], $payload['details']);
        return $payload;
    }

    /**
     * @param $type
     * @param  Transaction$result
     * @param resource $file
     */
    public function generateContentForReport($type, Transaction $result, $file, $gateways, $summationArray, $grandTotalArray, $currentMerchant, $counter, $noOfIterations)
    {
        $username = isset($result->user) ? $result->user->first_name . " " . $result->user->last_name : "N/A";
        $body = [];

        if ($type === "default"){

            $merchant = "";
            if ($currentMerchant !== $result->user_id){
                $merchant = $username;

                $body = [
                    'TOTAL',
                    '',
                    '',
                    number_format($summationArray['successful_bills']),
                    number_format($summationArray['pending_bills']),
                    number_format($summationArray['failed_bills']),
                    0,
                    0,
                    number_format($summationArray['total_fees']),
                    0,
                    number_format($summationArray['total_amount']),
                    number_format($summationArray['total']),
                    0,
                    number_format($summationArray['total_amount']),
                ];
                if ($currentMerchant !== 0){
                    fputcsv($file, $body);
                    fputcsv($file,['','', '', '', '', '', '', '', '', '', '', '', '', '',]);

                }
                $grandTotalArray = $this->grandTotalSummation($summationArray, $grandTotalArray);

                //reset footer;
                $summationArray = [
                    "successful_bills" => 0,"pending_bills" => 0,"failed_bills" => 0,"total_fees" => 0,"total_amount" => 0,
                    "total" => 0,
                ];

            }

            $currentMerchant = $result->user_id;
            $channel = "N/A";
            $gatewayName = "N/A";

            if (isset($result->gateway)){
               $channel =  strtolower(str_replace(" ", "_", $result->gateway->name));
               $gatewayName = $result->gateway->name;
           }

            $body = [
                $merchant,
                $result->type,
                $gatewayName,
                number_format($result->{$channel."_successful_bills"}),
                number_format($result->{$channel."_pending_bills"}),
                number_format($result->{$channel."_failed_bills"}),
                0,
                0,
                number_format($result->{$channel."_total_fees"},2),
                0,
                number_format($result->{$channel."_total_amount"},2),

                number_format($result->{$channel."_total"},2),
                0,
                number_format($result->{$channel."_total_amount"},2),

            ];

           $summationArray["successful_bills"] += $result->{$channel."_successful_bills"};
           $summationArray["pending_bills"] += $result->{$channel."_pending_bills"};
           $summationArray["failed_bills"] += $result->{$channel."_failed_bills"};
           $summationArray["total_fees"] += $result->{$channel."_total_fees"};
           $summationArray["total_amount"] += $result->{$channel."_total_amount"};
           $summationArray["total"] += $result->{$channel."_total"};

        }

        if ($type === "user_id") {
            $body = [
                $username,
                number_format($result->successful_bills), number_format($result->total_successful_amount),
                number_format($result->failed_bills), number_format($result->total_failed_amount),
                number_format($result->pending_bills), number_format($result->total_pending_amount),
            ];

        }


        //other Types Go Here;
        if ($type === "gateway_id"){
            $body = [$username,];
            foreach ($gateways as $gateway) {
                $channel = strtolower(str_replace(" ", "_", $gateway->name));

                array_push($body,
                number_format($result->{$channel."_successful_bills"} + $result->{$channel."_pending_bills"} + $result->{$channel."_failed_bills"}),
                number_format($result->{$channel."_successful_bills"}),
                number_format($result->{$channel."_pending_bills"}),
                number_format($result->{$channel."_failed_bills"}),
                number_format($result->{$channel."_total_successful_amount"},2),
                number_format($result->{$channel."_total_pending_amount"},2),
                number_format($result->{$channel."_total_failed_amount"},2),
                number_format($result->{$channel."_total_successful_fees"},2),
                number_format($result->{$channel."_total_pending_fees"},2),
                number_format($result->{$channel."_total_failed_fees"},2),
                number_format($result->{$channel."_total_successful"},2),
                number_format($result->{$channel."_total_pending"},2),
                number_format($result->{$channel."_total_failed"},2),
                );
            }

        }


        //Set the Content;
        fputcsv($file, $body);
        if ($counter === $noOfIterations - 1){
            $body = [
                'TOTAL',
                '',
                '',
                number_format($summationArray['successful_bills']),
                number_format($summationArray['pending_bills']),
                number_format($summationArray['failed_bills']),
                0,
                0,
                number_format($summationArray['total_fees']),
                0,
                number_format($summationArray['total_amount']),
                number_format($summationArray['total']),
                0,
                number_format($summationArray['total_amount']),
            ];
            fputcsv($file, $body);

            $grandTotalArray = $this->grandTotalSummation($summationArray, $grandTotalArray);
            $body2 = [
                'GRAND TOTAL',
                '',
                '',
                number_format($grandTotalArray['successful_bills']),
                number_format($grandTotalArray['pending_bills']),
                number_format($grandTotalArray['failed_bills']),
                0,
                0,
                number_format($grandTotalArray['total_fees']),
                0,
                number_format($grandTotalArray['total_amount']),
                number_format($grandTotalArray['total']),
                0,
                number_format($grandTotalArray['total_amount']),
            ];
            fputcsv($file,['','', '', '', '', '', '', '', '', '', '', '', '', '',]);
            fputcsv($file, $body2);


        }

        return [$currentMerchant,$summationArray];
    }

    /**
     * @param $groupBy
     * @param $file
     * @param $builder
     * @return mixed
     */
    public function summaryReportQuery($groupBy,  $file, $builder)
    {
        $result = "";
        $gateways = Gateway::select(['name','id'])->get();
        $csvHeaders = [];
        if ($groupBy === "user_id") {

            $csvHeaders = [
                "Merchant", "Successful Count", "Successful Amount", "Failed Count", "Failed Amount", "Pending Count", "Pending Amount"
            ];
            //Set the Headers;
            fputcsv($file, $csvHeaders);

            $result = $builder->select('user_id')->

            selectRaw("COUNT(CASE WHEN status = 'successful' THEN 1 end) as successful_bills")->
            selectRaw("COUNT(CASE WHEN status = 'pending' THEN 1 end) as pending_bills")->
            selectRaw("COUNT(CASE WHEN status = 'failed' THEN 1 end) as failed_bills")->

            //sum amount
            selectRaw("SUM(CASE WHEN status = 'successful' THEN amount end) as total_successful_amount")->
            selectRaw("SUM(CASE WHEN status = 'pending' THEN amount end) as total_pending_amount")->
            selectRaw("SUM(CASE WHEN status = 'failed' THEN amount end) as total_failed_amount")->

            //sum fee
            selectRaw("SUM(CASE WHEN status = 'successful' THEN fee end) as total_successful_fees")->
            selectRaw("SUM(CASE WHEN status = 'pending' THEN fee end) as total_pending_fees")->
            selectRaw("SUM(CASE WHEN status = 'failed' THEN fee end) as total_failed_fees")->

            //sum total
            selectRaw("SUM(CASE WHEN status = 'successful' THEN total end) as total_successful_bills")->
            selectRaw("SUM(CASE WHEN status = 'pending' THEN total end) as total_pending_bills")->
            selectRaw("SUM(CASE WHEN status = 'failed' THEN total end) as total_failed_bills")->
            groupBy($groupBy);

        }
        if ($groupBy === "gateway_id") {
            $csvHeaders = [
                "Merchant",
                ];

            $builder = $builder->select('user_id');

            [$csvHeaders, $builder] = $this->summaryQueryWithGateways($gateways, $csvHeaders, $builder);

            fputcsv($file, $csvHeaders);

            $result = $builder->groupBy('user_id');


        }
        if ($groupBy === "default"){
            $csvHeaders = [
                "Merchant",
            ];

            $builder = $builder->where("type","!=","Wallet")->select(['user_id','type','gateway_id']);

            $builder = $this->summaryQueryWithGateways($gateways, $csvHeaders, $builder)[1];
            $csvHeaders = [
                "Merchant","Service Type", "Payment Channel", "Successful", "Initiated", "Failed", "Refunded", "Saanapay Transaction Charge", "Saanapay Merchant Service Charge", "Bank Charges", "Amount", "Total Amount", "Refund Amount", "Merchant Amount"
            ];
            fputcsv($file, $csvHeaders);

            $result = $builder->groupBy('user_id')->groupBy('type')->groupBy('gateway_id')->orderBy('user_id');

        }

        return ["query" =>$result, "csvHeaders"=> $csvHeaders, "gateways" => $gateways];
    }


    /**
     * @param $gateway_id
     * @return  array ["total","charge"]
     */
    public function computeChargeAndTotal($gateway_id): array
    {
        $transactionTotal = $this->total;
        $userGateways = $this->user->usergateway->config_details;
        $gateway_charge = 0;
        if (isset($userGateways[$gateway_id])){
            $gateway_charge = $userGateways[$gateway_id]['charge_factor'] ? ($userGateways[$gateway_id]['charge'] / 100 ) * $this->amount : $userGateways[$gateway_id]['charge'] ;
            $transactionTotal = $this->amount + $gateway_charge;
        }
        return ["total" => $transactionTotal, "charge" => $gateway_charge ];

    }

    /**
     * @param $gateways
     * @param array $csvHeaders
     * @param $builder
     * @return array
     */
    public function summaryQueryWithGateways($gateways, array $csvHeaders, $builder): array
    {
        foreach ($gateways as $gateway) {
            $channel = strtolower(str_replace(" ", "_", $gateway->name));
            array_push($csvHeaders, "{$channel}_Count",
                "Successful_{$channel}_Count", "Pending_{$channel}_Count", "Failed_{$channel}_Count",
                "Successful_{$channel}_Amount", "Pending_{$channel}_Amount", "Failed_{$channel}_Amount",
                "Successful_{$channel}_Fees", "Pending_{$channel}_Fees", "Failed_{$channel}_Fees",
                "Successful_{$channel}_Total", "Pending_{$channel}_Total", "Failed_{$channel}_Total");

            $builder->
            selectRaw("COUNT(CASE WHEN status = 'successful' AND gateway_id = $gateway->id THEN 1 end) as {$channel}_successful_bills")->
            selectRaw("COUNT(CASE WHEN status = 'pending' AND gateway_id = $gateway->id THEN 1 end) as {$channel}_pending_bills")->
            selectRaw("COUNT(CASE WHEN status = 'failed' AND gateway_id = $gateway->id THEN 1 end) as {$channel}_failed_bills")->

            //sum amount
//            selectRaw("SUM(amount) as {$channel}_total_amount")->
            selectRaw("SUM(CASE WHEN gateway_id = $gateway->id  THEN amount end) as {$channel}_total_amount")->


//            selectRaw("SUM(CASE WHEN status = 'successful' AND gateway_id = $gateway->id  THEN amount end) as {$channel}_total_successful_amount")->
//            selectRaw("SUM(CASE WHEN status = 'pending' AND gateway_id = $gateway->id  THEN amount end) as {$channel}_total_pending_amount")->
//            selectRaw("SUM(CASE WHEN status = 'failed' AND gateway_id = $gateway->id  THEN amount end) as {$channel}_total_failed_amount")->

            //sum fee
//            selectRaw("SUM(fee) as {$channel}_total_fees")->
            selectRaw("SUM(CASE WHEN gateway_id = $gateway->id  THEN fee end) as {$channel}_total_fees")->


//            selectRaw("SUM(CASE WHEN status = 'successful' AND gateway_id = $gateway->id  THEN fee end) as {$channel}_total_successful_fees")->
//            selectRaw("SUM(CASE WHEN status = 'pending' AND gateway_id = $gateway->id  THEN fee end) as {$channel}_total_pending_fees")->
//            selectRaw("SUM(CASE WHEN status = 'failed' AND gateway_id = $gateway->id  THEN fee end) as {$channel}_total_failed_fees")->

            //sum total
//            selectRaw("SUM(total) as {$channel}_total");
            selectRaw("SUM(CASE WHEN gateway_id = $gateway->id  THEN total end) as {$channel}_total");


//            selectRaw("SUM(CASE WHEN status = 'successful' AND gateway_id = $gateway->id  THEN total end) as {$channel}_total_successful")->
//            selectRaw("SUM(CASE WHEN status = 'pending' AND gateway_id = $gateway->id  THEN total end) as {$channel}_total_pending")->
//            selectRaw("SUM(CASE WHEN status = 'failed' AND gateway_id = $gateway->id  THEN total end) as {$channel}_total_failed");
        }
        return array($csvHeaders, $builder);
    }

    /**
     * @param array $summationArray
     * @param $grandTotalArray
     */
    public function grandTotalSummation(array $summationArray, $grandTotalArray)
    {
        $grandTotalArray["successful_bills"] += $summationArray["successful_bills"];
        $grandTotalArray["pending_bills"] += $summationArray["pending_bills"];
        $grandTotalArray["failed_bills"] += $summationArray["failed_bills"];
        $grandTotalArray["total_fees"] += $summationArray["total_fees"];
        $grandTotalArray["total_amount"] += $summationArray["total_amount"];
        $grandTotalArray["total"] += $summationArray["total"];
        return $grandTotalArray;
    }
}
