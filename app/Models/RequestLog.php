<?php

namespace App\Models;


class RequestLog extends Model
{
protected $casts = ['response' => 'json', 'payload' => 'json'];
    public static function logRequest($trnxRef, $url, $merchant_id, $payload, $request_response): void
    {
        $request = self::firstOrCreate(
            [
                "request_id" => $trnxRef,
                "user_id" => $merchant_id,
            ]
        );

        //Get the payload;
        $json_decode = json_decode($request_response, false, 512, JSON_THROW_ON_ERROR);
        $request_response = $json_decode->data ?? $json_decode;
        if (is_null($request->response)) {
            // first time call; insert new response;
            $resp = [$request_response];
            $payloadDump = [$payload];
        }
        if (!is_null($request->response)){

            $resp = $request->response;
            $payloadDump = $request->payload;

            if (is_array($resp) && count($resp)) {
                //add response to existing response;
                $resp[] = $request_response;
                $payloadDump[] = $payload;
            }

        }

        $request->update([
            "response" => $resp,
            "url" => $url,
            "payload" => $payloadDump,
        ]);

    }

    public function scopeCriteria($query,$criteria)
    {
        $queryArray = [];

        if (array_key_exists('request_id', $criteria) && !empty($criteria['request_id'])) {
            $queryArray[] = ['request_id', 'like', (string)($criteria['request_id']."%")];
        }
        if (array_key_exists('user_id', $criteria) && !empty($criteria['user_id'])) {
            $queryArray[] = ['user_id', '=', (string)($criteria['user_id'])];
        }

        $query->where($queryArray)->orderBy('id','desc');

        if ((array_key_exists('created_at', $criteria)&& !empty($criteria['created_at'])) && (array_key_exists('end_date', $criteria)&& !empty($criteria['end_date']))) {
            return $query->whereBetween("created_at", [$criteria['created_at'], $criteria['end_date'] . " 23:59:59.999",]);
        }
        if (array_key_exists('created_at', $criteria)&& !empty($criteria['created_at'])) {
            return $query->whereDate('created_at', $criteria['created_at']);
        }
        if(array_key_exists('end_date', $criteria)&& !empty($criteria['end_date'])){
            return $query->whereDate('created_at', $criteria['end_date']);
        }


    }
}
