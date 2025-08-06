<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PushTransactionsToWebhookCommand extends Command
{
    protected $signature = 'push:transactions-to-webhook {year : Year to push (e.g. 2024)} {email : User email to fetch transactions for}';


    protected $description = 'Push transactions of a user for the specified year to the Webhook/ Redirect URL';

    protected string $uiEmail = 'ui@donotsend.com';

    public function handle(): void
    {
        $selectedYear = $this->argument('year');
        $email = $this->argument('email');

        if (!$selectedYear || !is_numeric($selectedYear)) {
            $this->error('Invalid or missing year');
            return;
        }

        if (!$email) {
            $this->error('Email Missing');
            return;
        }

        $user =User::select(['email','id'])->firstWhere('email', $email);
        if (!$user) {
            $this->error("User with email $email not found.");
            return;
        }

        $startOfYear = Carbon::create($selectedYear)->startOfYear();
        $endOfYear = Carbon::create($selectedYear)->endOfYear();
        $webhook_url = $user->webhook_url->url;
        $use_redirect_url = $user->email === $this->uiEmail;
        $fileHandle = fopen(storage_path('logs/FailedTransactionPush.csv'), 'w');
//        fputcsv($fileHandle, [ "TransactionRef","Type", "Name", "Email", "Amount", "created_at"]);
        fputcsv($fileHandle, [ "TransactionRef","http_status_code"]);




        try {
            Transaction::with([
                'gateway:id,name',
                'invoice:invoice_no,customer_name,customer_email'
            ])
                ->select(['id', "transaction_ref","type", "merchant_transaction_ref", "invoice_no", "gateway_id", "amount", "description", "status", "flag", "currency", "details","created_at", "updated_at","redirect_url"])
                ->where('user_id', $user->id)
                ->whereNotNull('invoice_no')
                ->whereBetween('created_at', [$startOfYear, $endOfYear])
//                ->chunkById(10, function ($transactions) use ($webhook_url, $use_redirect_url) {
//                    /** @var Transaction $transaction */
//                    foreach ($transactions as $transaction) {
//                        $payload = $this->getPayload($transaction);
//
//                        if ($use_redirect_url){
//                            $webhook_url = $transaction->redirect_url;
//                        }
//
//                        $response = Http::withoutVerifying()->get($webhook_url, $payload)->json();
//
//                        $this->info("Push to Webhook for $transaction->merchant_transaction_ref on URL :$webhook_url with Response \n". json_encode($response, JSON_THROW_ON_ERROR));
//
//                    }
//                });

             ->chunkById(150, function ($transactions) use ($webhook_url, $use_redirect_url, $fileHandle) {
                                $responses = Http::pool(function ($pool) use ($transactions, $webhook_url, $use_redirect_url) {
                                    foreach ($transactions as $transaction) {
                                        $payload = $this->getPayload($transaction);

                                        $url = $use_redirect_url ? $transaction->redirect_url : $webhook_url;

                                        // Add GET request to the pool
                                        $pool->as($transaction->merchant_transaction_ref)
                                            ->get($url, $payload);
                                    }
                                });

                                // Log each response
                                foreach ($responses as $ref => $response) {
                                    if ($response->successful()) {
                                        $this->info("Pushed transaction {$ref} successfully. Response: " . json_encode($response->json(), JSON_THROW_ON_ERROR));
                                    } else {
                                        $this->error("Failed to push transaction {$ref}. HTTP Status: " . $response->status());
                                        fputcsv($fileHandle, [$ref, $response->status()]);
                                    }
                                }
                            });
        } catch (\Throwable $exception) {

            $this->error("Error pushing transactions. Check logs.");

        }

    }

    /**
     * @param Transaction $transaction
     * @return array
     */
    public function getPayload(Transaction $transaction): array
    {
        $status = strtolower($transaction->status);
        $payload = $transaction->toArray();
        $payload['channel'] = $payload['gateway']['name'] ?? "N/A";
        $payload['customer_name'] = $payload['invoice']['customer_name'] ?? $payload['details']['name'] ?? "N/A";
        $payload['customer_email'] = $payload['invoice']['customer_email'] ?? $payload['details']['email'] ?? "N/A";
        $payload['total'] = $payload['amount'];
        //2025-01-01T05:43:51
        $payload['updated_at'] = $transaction->updated_at->format("Y-m-d H:i:s");
        $payload['created_at'] = $transaction->created_at->format("Y-m-d H:i:s");

        unset($payload['id'], $payload['gateway_id'], $payload['gateway'], $payload['details'], $payload['invoice'], $payload['redirect_url']);
        //special case when it is failed set the amount to null; created_at to null;
        if (in_array($status, ['failed','pending'])) {
            $payload['amount'] = null;
            $payload['created_at'] = null;
        }
        return $payload;
    }
}
