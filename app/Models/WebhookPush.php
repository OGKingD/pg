<?php

namespace App\Models;


class WebhookPush extends Model
{
    protected $casts = [
        "response" => "json",
        "payload" => "json",
    ];


    /**
     * @param $tranx_id
     * @param $user_id
     * @param $payload
     * @return WebhookPush
     */
    public function logWebhookPush($tranx_id,$merchant_ref, $user_id, $payload)
    {
        $webhookPush = $this->firstOrCreate(
            ["transaction_id" => $tranx_id],
            ["merchant_transaction_ref" => $merchant_ref],
            ["user_id" => $user_id]
        );

        $webhookPush->update([
            "payload" => $payload,
        ]);

        return $webhookPush;


    }

    public function logWebhookResponse($response)
    {
        $status = false;

        if (isset($response["status"]) ) {
            $status = true;
        }

        $payloadResp = $this->response;

        if (is_array($payloadResp) && count($payloadResp)) {
            $payloadResp[] = $response;
        } else {
            $payloadResp = [$response];
        }

        $this->update([
            "count" => ($this->count + 1),
            "response" => $payloadResp,
            "status" => $status
        ]);
    }

}
