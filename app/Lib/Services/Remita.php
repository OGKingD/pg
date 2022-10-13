<?php


namespace App\Lib\Services;


use Illuminate\Support\Facades\Http;

class Remita
{
    public function remitaGenerateRRR($amount,$trn_ref,$customerEmail)
    {
        $lineitemAmount1 = (40/100 * $amount) ;
        $lineitemAmount2 =  $amount - $lineitemAmount1;
        $payload = [
            'amount' => 10000,
            'orderId' => time(),
            'serviceTypeId' => "4430731",
            'payerName' => "Remita Payment $trn_ref",
            'payerEmail' => $customerEmail,
            'payerPhone' => "NA",
            'description' => time(),
            'lineItems' => [
                [
                    "lineItemsId"=>"itemid1",
                    "beneficiaryName"=>"Alozie Michael",
                    "beneficiaryAccount"=>"6020067886",
                    "bankCode"=>"058",
                    "beneficiaryAmount"=>3000,
                    "deductFeeFrom"=>"0"
                ],
                [
                    "lineItemsId"=>"itemid1",
                    "beneficiaryName"=>"Alozie Michael",
                    "beneficiaryAccount"=>"6020067886",
                    "bankCode"=>"058",
                    "beneficiaryAmount"=>7000,
                    "deductFeeFrom"=>"1"
                ]
            ],
        ];

        $merchantId = config('remita.merchant_id');
        $apiHash =hash('sha512', $merchantId . $payload['serviceTypeId'] . $payload['orderId'] . $payload['amount'] . config('remita.api_key'));
        $response = Http::withHeaders(['Authorization' => "remitaConsumerKey=$merchantId,remitaConsumerToken=$apiHash"])->post(config('remita.base_url') . '/echannelsvc/merchant/api/paymentinit', $payload);

        return $response->body();

    }



}
