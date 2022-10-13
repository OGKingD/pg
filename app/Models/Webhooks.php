<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Webhooks extends Model
{
    use HasFactory;

    protected $dateFormat = "Y-m-d H:i:s.u";
    protected $guarded = ["id"];
    protected $casts = ['response' => 'json', "payment_provider_message" => 'json'];

    public function logWebhook($paymentProviderId, $userRef, $paymentProviderMessage, $response)
    {

        $webhook = $this->firstOrCreate(
            ["payment_provider_id" => $paymentProviderId],
            ["user_ref" => $userRef]
        );

        $payload = $webhook->payment_provider_message;
        $response2payload = $webhook->response;

        $payload[] = $paymentProviderMessage;
        $response2payload[] = $response;



        $webhook->update([
            "payment_provider_message" => $payload,
            "response" => $response2payload,
            "count" => ($webhook->count + 1),
        ]);


    }

    //
    public function scopeCriteria($query, $criteria)
    {
        $queryArray = [];

        if (array_key_exists('payment_provider_id', $criteria) && !empty($criteria['payment_provider_id'])) {
            $queryArray[] = ['payment_provider_id', '=', (string)($criteria['payment_provider_id'])];
        }
        if (array_key_exists('user_ref', $criteria) && !empty($criteria['user_ref'])) {
            $queryArray[] = ['user_ref', '=', (string)($criteria['user_ref'])];
        }

        $query->where($queryArray)->orderBy('id', 'desc');

        if ((array_key_exists('created_at', $criteria) && !empty($criteria['created_at'])) && (array_key_exists('end_date', $criteria) && !empty($criteria['end_date']))) {
            return $query->whereBetween("created_at", [$criteria['created_at'], $criteria['end_date'] . " 23:59:59.999",]);
        }
        if (array_key_exists('created_at', $criteria) && !empty($criteria['created_at'])) {
            return $query->whereDate('created_at', $criteria['created_at']);
        }
        if (array_key_exists('end_date', $criteria) && !empty($criteria['end_date'])) {
            return $query->whereDate('created_at', $criteria['end_date']);
        }


    }
}
