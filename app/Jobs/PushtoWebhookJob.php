<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\WebhookPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class PushtoWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $transaction;
    private $user_id;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;

        $this->user_id = $transaction->user_id;
    }



    public function handle(WebhookPush $webhookPush)
    {
        $webhook_url = $this->transaction->user->webhook_url;
        if ($webhook_url) {
            //log into webhook push table that request has been triggered;
            $payload = $this->transaction->transactionToPayload();
            $webhookPush = $webhookPush->logWebhookPush($this->transaction->id,$this->transaction->merchant_transaction_ref,$this->user_id,$payload);
            //send request to webhookUrl;
            $url = $webhook_url->url;
            if (isset($transaction->details["redirect_url"])){
                $url = $transaction->details['redirect_url'];
            }
            //send to the URL;
            $response = Http::withoutVerifying()->post($url, $payload)->json();
            //update with response from webhookUrl;
            $webhookPush->logWebhookResponse($response);

        }
    }
}
