<?php


namespace App\Lib\Services;


use Illuminate\Support\Facades\Http;

class Remita
{
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private $merchantId;

    public function __construct()
    {
        $this->merchantId = config('remita.merchant_id');

    }

    public function remitaGenerateRRR($amount,$trn_ref,$customerEmail,$description,$serviceId,$lineItems)
    {

        $payload = [
            'amount' => $amount,
            'orderId' => $trn_ref."_".time(),
            'serviceTypeId' => $serviceId,
            'payerName' => $trn_ref,
            'payerEmail' => $customerEmail,
            'payerPhone' => "NA",
            'description' => $description,
            'lineItems' => $lineItems,
        ];

        $apiHash =hash('sha512', $this->merchantId . $payload['serviceTypeId'] . $payload['orderId'] . $payload['amount'] . config('remita.api_key'));
        $response = $this->getWithHeaders($this->merchantId,$apiHash)->post(config('remita.base_url') . '/echannelsvc/merchant/api/paymentinit', $payload);

        return $response->body();

    }

    public function rrrStatus($rrr)
    {

        $apiHash =hash('sha512', $rrr  . config('remita.api_key'). $this->merchantId);
        $response = $this->getWithHeaders($this->merchantId,$apiHash)->get(config('remita.base_url')."/echannelsvc/$this->merchantId/$rrr/$apiHash/status.reg")->json();
        $result = [
            "status" => false,
            "message" => $response['message'] ?? null,
        ];

        if (isset($response['status']) && in_array($response['status'], ['00', '01'], false)) {
            $result['status']  = true;
            $result['data'] = $response;
            $result['message'] = $response['message'];
        }

        return $result;

    }

    /**
     * @return \Illuminate\Http\Client\PendingRequest
     */
    public function getWithHeaders($merchantId,$apiHash): \Illuminate\Http\Client\PendingRequest
    {

        return Http::withHeaders([
            'Authorization' => "remitaConsumerKey=$merchantId,remitaConsumerToken=$apiHash",
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ])->withoutVerifying();
    }



}
