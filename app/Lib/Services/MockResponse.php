<?php


namespace App\Lib\Services;


class MockResponse
{
    public function flwavePayload()
    {
        return [
            'status' => 'success',
            'message' => 'Charge initiated',
            'data' =>
                [
                    'id' => 2615403,
                    'tx_ref' => 'MC-TEST-1234568_success_mock',
                    'flw_ref' => 'RQFA6549001367743',
                    'device_fingerprint' => 'gdgdhdh738bhshsjs',
                    'amount' => 10,
                    'charged_amount' => 10,
                    'app_fee' => 0.38,
                    'merchant_fee' => 0,
                    'processor_response' => 'Payment token retrieval has been initiated',
                    'auth_model' => 'GOOGLEPAY_NOAUTH',
                    'currency' => 'USD',
                    'ip' => '54.75.56.55',
                    'narration' => 'Test Google Pay charge',
                    'status' => 'pending',
                    'auth_url' => 'https://rave-api-v2.herokuapp.com/flwv3-pug/getpaid/api/short-url/XPtNw-WkQ',
                    'payment_type' => 'googlepay',
                    'fraud_status' => 'ok',
                    'charge_type' => 'normal',
                    'created_at' => '2022-05-11T20:36:15.000Z',
                    'account_id' => 20937,
                    'customer' =>
                        [
                            'id' => 955307,
                            'phone_number' => NULL,
                            'name' => 'Flutterwave Developers',
                            'email' => 'developers@flutterwavego.com',
                            'created_at' => '2022-05-11T20:36:14.000Z',
                        ],
                    'meta' =>
                        [
                            'authorization' =>
                                [
                                    'mode' => 'redirect',
                                    'redirect' => 'https://rave-api-v2.herokuapp.com/flwv3-pug/getpaid/api/short-url/XPtNw-WkQ',
                                ],
                        ],
                ],
        ];

    }
    public function flwaveGooglepayResponse()
    {
        $response = $this->flwavePayload();
        $response['data']['auth_model'] = "GOOGLEPAY_NOAUTH";
        $response['data']['narration'] = "Test Google Pay charge";
        $response['data']['payment_type'] = "googlepay";
        $response['data']['auth_url'] = "https://rave-api-v2.herokuapp.com/flwv3-pug/getpaid/api/short-url/XPtNw-WkQ";
        $response['data']['meta']['authorization']['redirect'] = "https://rave-api-v2.herokuapp.com/flwv3-pug/getpaid/api/short-url/XPtNw-WkQ";

        return $response;

    }

    public function flwaveApplepayResponse()
    {
        $response = $this->flwavePayload();
        $response['data']['auth_model'] = "APPLEPAY_NOAUTH";
        $response['data']['narration'] = "Test Apple Pay charge";
        $response['data']['payment_type'] = "applepay";
        $response['data']['auth_url'] = "https://applepay.aq2-flutterwave.com?reference=TKVH48681032738026" ;
        $response['data']['meta']['authorization']['redirect'] = "https://applepay.aq2-flutterwave.com?reference=TKVH48681032738026";

        return $response;

    }

}
