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
        $this->transaction = $transaction->refresh();

        $this->user_id = $transaction->user_id;
    }



    public function handle(WebhookPush $webhookPush)
    {
        $webhook_url = $this->transaction->user->webhook_url;
        $payload = $this->transaction->transactionToPayload();
        if ($webhook_url) {
            $pendingRequest = Http::withoutVerifying();
            $httpVerb = 'post';
            //log into webhook push table that request has been triggered;
            $webhookPush = $webhookPush->logWebhookPush($this->transaction->id,$this->transaction->merchant_transaction_ref,$this->user_id,$payload);
            //send request to webhookUrl;
            $url = $webhook_url->url;
            if ($this->transaction->user->id === 3){
                $url = $this->transaction->redirect_url;
                $httpVerb = 'get';
            }
            //send to the URL;
           if ($url){
               $response = $pendingRequest->$httpVerb($url, $payload)->json();
               //update with response from webhookUrl;
               $webhookPush->logWebhookResponse($response);
           }

        }
    }
}
