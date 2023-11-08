<?php


namespace App\Lib\Services;


use JetBrains\PhpStorm\ArrayShape;

class CoralPay
{
    private $nqrUsername;
    private $nqrPassword;
    private $nqrClientId;
    private $nqrBaseUrl;

    public function __construct()
    {
        $this->nqrUsername = config('coralpay.nqr_username');
        $this->nqrPassword = config('coralpay.nqr_password');
        $this->nqrClientId = config('coralpay.nqr_client_id');
        $this->nqrBaseUrl = config('coralpay.nqr_base_url');
        $this->nqrMerchantId = config('coralpay.nqr_merchant_id');
        $this->nqrTerminalId = config('coralpay.nqr_terminal_id');
    }

    public function generateKeys()
    {
        $private_key = openssl_pkey_new();
        $public_key_pem = openssl_pkey_get_details($private_key)['key'];
        $public_key = openssl_pkey_get_public($public_key_pem);

        return [$public_key_pem, $public_key];


    }

    public function pgpEncryption()
    {

    }




    /**
     * @return array{'status' : bool, 'token' : string }
     */
    public function nqrAuthenticate(): array
    {
        $url = config('coralpay.nqr_base_url') . 'authentication';

        return $this->authenticate($this->nqrUsername, $this->nqrPassword, $url);

    }

    /**
     * @param $username
     * @param $password
     * @param $url
     * @return array{
     * status : bool,
     * token : string,
     * }
     */
    public function authenticate($username, $password, $url): array
    {
        $payload = [
            'username' => $username,
            'password' => $password
        ];
        $result = ['status' => false];
        $response = httpRequestWithoutVerifying()->post($url, $payload)->json();
        if (array_key_exists('Token', $response)) {
            $result['status'] = true;
            $result['token'] = $response['Token'];
            $result['key'] = $response['Key'];
        }
//        dump($payload,$response);
        return $result;

    }

    /**
     * @return array
     */
    private function generatePayloadData(): array
    {
        $timestamp = time();
        $nqrToken = $this->nqrAuthenticate();
        $key = '';
        $status = false;
        if ($nqrToken['status']) {
            $key = $nqrToken['key'];
            $status = true;
        }

        $signature = hash('sha256', $this->nqrClientId . $timestamp . $key);
        return array($timestamp, $nqrToken, $status, $signature);
    }

    public function createMerchant(): array
    {
        $url = $this->nqrBaseUrl . 'createmerchant';
        [$timestamp, $nqrToken, $status, $signature] = $this->generatePayloadData();

        $payload =
            [
                "RequestHeader" => [
                    "ClientId" => $this->nqrClientId,
                    "TimeStamp" => $timestamp,
                    "Signature" => $signature
                ],
                "Merchants" => [
                    [
                        "MerchantName" => "Coza Abuja",
                        "Tin" => "900990309",
                        "ContactName" => "Oladele Akiode",
                        "PhoneNumber" => "089782828891",
                        "Email" => "coza1@coza.com",
                        "AccountNumber" => "0016563223",
                        "AccountName" => "Amaechi Vera",
                        "BankCode" => "999998"
                    ],
                ]
            ];
        $response = [
            'status' => false
        ];
        if ($status) {
            $result = httpRequestWithoutVerifying()->withToken($nqrToken['token'])->post($url, $payload)->json();
            if (array_key_exists('ResponseHeader', $result)) {
                if ($result['ResponseHeader']['ResponseCode'] === '00') {
                    $response['status'] = true;
                    $response['merchant_id'] = $result['MerchantDetails']['MerchantId'];
                }
            }
        }
        return $response;
    }


    public function createTerminal(): array
    {
        $url = $this->nqrBaseUrl . 'createterminal';
        [$timestamp, $nqrToken, $status, $signature] = $this->generatePayloadData();

        $payload =
            [
                "RequestHeader" => [
                    'MerchantId' => $this->nqrMerchantId,
                    "ClientId" => $this->nqrClientId,
                    "TimeStamp" => $timestamp,
                    "Signature" => $signature
                ],
                "Terminals" => [
                    [
                        "TerminalName" => "Coza Abuja",
                        "ContactName" => "Oladele Akiode",
                        "PhoneNumber" => "089782828891",
                        "Email" => "coza1@coza.com",
                        "QrType" => "0",
                    ],
                ]
            ];
        $response = [
            'status' => false
        ];
        if ($status) {
            $result = httpRequestWithoutVerifying()->withToken($nqrToken['token'])->post($url, $payload)->json();
            if (array_key_exists('ResponseHeader', $result)) {
                if ($result['ResponseHeader']['ResponseCode'] === '00') {
                    $response['status'] = true;
                    $response['terminal_id'] = $result['TerminalDetails'][0]['TerminalId'];
                    $response['terminal_name'] = $result['TerminalDetails'][0]['TerminalName'];
                }
            }
        }
        return $response;
    }

    public function queryMerchants(): array
    {
        $url = $this->nqrBaseUrl . 'querymerchant';
        [$timestamp, $nqrToken, $status, $signature] = $this->generatePayloadData();

        $payload =
            [
                "RequestHeader" => [
                    "ClientId" => $this->nqrClientId,
                    "TimeStamp" => $timestamp,
                    "Signature" => $signature
                ],
                'MerchantId' => $this->nqrMerchantId,

            ];
        $response = [
            'status' => false
        ];
        if ($status) {
            $result = httpRequestWithoutVerifying()->withToken($nqrToken['token'])->post($url, $payload)->json();
            if (array_key_exists('ResponseHeader', $result)) {
                if ($result['ResponseHeader']['ResponseCode'] === '00') {
                    $response['status'] = true;
                    unset($result['ResponseHeader']);
                    $response['data'] = $result;
                }
            }
        }
        return $response;
    }

    public function createQrCode($amount, $trn_ref): array
    {
        $url = $this->nqrBaseUrl . 'invokedynamicqr';
        [$timestamp, $nqrToken, $status, $signature] = $this->generatePayloadData();

        $payload =
            [
                "RequestHeader" => [
                    "ClientId" => $this->nqrClientId,
                    "TimeStamp" => $timestamp,
                    "Signature" => $signature
                ],
                'TransactionDetails' => [
                    'MerchantId' => $this->nqrMerchantId,
                    'TerminalId' => $this->nqrTerminalId,
                    'Amount' => $amount,
                    'TraceId' => $trn_ref,
                ],

            ];
        $response = [
            'status' => false
        ];
        if ($status) {
            $result = httpRequestWithoutVerifying()->withToken($nqrToken['token'])->post($url, $payload)->json();
            if (array_key_exists('ResponseHeader', $result)) {
                if ($result['ResponseHeader']['ResponseCode'] === '00') {
                    $response['status'] = true;
                    $response['trn_ref'] = $result['TraceId'];
                    $response['payment_provider_id'] = $result['TransactionId'];
                    $response['qr_code'] = $result['QrCodeData'];
                }
            }
        }
        return $response;
    }
    public function queryTransaction($start_date,$end_date): array
    {
        $url = $this->nqrBaseUrl . 'transactionquery';
        [$timestamp, $nqrToken, $status, $signature] = $this->generatePayloadData();

        $payload =
            [
                "RequestHeader" => [
                    "ClientId" => $this->nqrClientId,
                    "TimeStamp" => $timestamp,
                    "Signature" => $signature
                ],
                'MerchantId' => 'M0000015433',
                'TerminalId' => '',
                'StartTime' => '',
                'EndTime' => '',

            ];
        $response = [
            'status' => false
        ];
        if ($status) {
            $result = httpRequestWithoutVerifying()->withToken($nqrToken['token'])->post($url, $payload)->json();
            if (array_key_exists('ResponseHeader', $result)) {
                if ($result['ResponseHeader']['ResponseCode'] === '00') {
                    $response['status'] = true;
                    unset($result['ResponseHeader']);
                    $response['data'] = $result;
                }
            }
        }
        return $response;
    }

    public function generateNqrCode()
    {
        $url = config('coralpay.nqr_base_url') . 'invokedynamicqr';
        $clientId = $this->nqrClientId;
        $timestamp = time();
        $signature = hash('sha512', $clientId . $timestamp . '');

        $payload = [
            [
                "RequestHeader" => [
                    "ClientId" => $clientId,
                    "TimeStamp" => $timestamp,
                    "Signature" => "2887e2df753f9f3dbb9225045c8da03e82e64d"
                ],
                "TransactionDetails" => [
                    "MerchantId" => "M0000003569",
                    "TerminalId" => "S0000005789",
                    "Amount" => 600.00,
                    "TraceId" => "100000500000011"
                ]]
        ];

    }
    //callback sample
    //{
    // "ClientId": “1000000TMB01”,
    // "ClientName": "Oladele Akin",
    // "TerminalId": "9782828891",
    // "TerminalName": "Donation 1",
    // "MerchantId": "M0000003582",
    // "MerchantName": "Parish 5",
    // "OrderSn": "00098992929929929929",
    // "Charge": 0.0,
    // "NetAmount": 0.0,
    // "Date": "2021-01-01T00:00:00",
    // "Signature": ksldklk3lk3klk53lklskdlkfsldf,
    // "TraceId": "3420000003542",
    // "TimeStamp": 16004034303",
    // "ResponseCode": "00"
    // }



}
