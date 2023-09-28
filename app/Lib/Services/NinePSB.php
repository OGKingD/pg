<?php

namespace App\Lib\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Psr\Container\ContainerExceptionInterface;

class NinePSB
{


    private $username;
    private $password;
    private $clientId;
    private $clientSecret;
    private $dynamicAccBaseUrl;
    private $dynamicAccPrivKey;
    private $dynamicAccPubKey;

    public function __construct()
    {
        $this->baseUrl = config('9psb.baseUrl');
        $this->dynamicAccBaseUrl = config('9psb.dynamic_account_baseUrl');
        $this->dynamicAccPrivKey = config('9psb.dynamic_account_privateKey');
        $this->dynamicAccPubKey = config('9psb.dynamic_account_publicKey');
        $this->username = config('9psb.username');
        $this->password = config('9psb.password');
        $this->clientId = config('9psb.clientId');
        $this->clientSecret = config('9psb.clientSecret');
    }

    public function createWallet($firstname, $lastname, $phoneNumber, $dob, $address, $email, $bvn)
    {
        $dob = Carbon::parse($dob)->format('d/m/Y');

        $url = $this->baseUrl . "waas/api/v1/open_wallet";

        $payload = [
            "transactionTrackingRef" => "SPAY/".time(),
            "lastName" => $lastname,
            "otherNames" => null,
            "accountName" => $firstname. " ".$lastname,
            "phoneNo" => $phoneNumber,
            "gender" => 0,
            "placeOfBirth" => null,
            "dateOfBirth" => $dob,
            "address" => null,
            "nationalIdentityNo" => null,
            "nextOfKinPhoneNo" => null,
            "nextOfKinName" => null,
            "referralPhoneNo" => null,
            "referralName" => null,
            "otherAccountInformationSource" => null,
            "email" => $email,
            "customerImage" => null,
            "customerSignature" => null,
            "notificationPreference" => null,
            "transactionPermission" => null,
            "bvn" => $bvn,
            "customerID" => null,
            "walletType" => "INDIVIDUAL"
        ];
        $result = $this->getWithRestfulHeaders($url, "post", $payload, $this->generateToken());
        if (isset($result['data']['accountNumber'])){
            return [
                "account_number" => $result
            ];
        }
    }

    /**
     * @param $url
     * @param $verb
     * @param $payload
     * @return mixed
     */
    public function getWithRestfulHeaders($url, $verb, $payload, $token = null)
    {
        $httpVerb = strtolower($verb);


        return httpRequestWithoutVerifying()->withToken($token)->{$httpVerb}($url, $payload)->json();
    }


    /**
     * @return false|\Illuminate\Contracts\Cache\Repository|mixed
     * @throws \Exception
     */
    public function generateToken()
    {
        $token = false;
        //check to see if token has expired;
        try {
            $token = cache()->get("9psbToken");
            if (is_null($token)) {
                $url = $this->baseUrl . "bank9ja/api/v2/k1/authenticate";
                $payload = [
                    "username" => $this->username,
                    "password" => $this->password,
                    "clientId" => $this->clientId,
                    "clientSecret" => $this->clientSecret,
                ];
                $result = $this->getWithRestfulHeaders($url, "post", $payload);
                //store token in cache ;
                if (isset($result['accessToken'])) {
                    cache([
                        "9psbToken" => $result['accessToken'],
                        "expiry" => $result['expiresIn'],
                    ], now()->addSeconds($result['expiresIn']));
                    $token = $result['accessToken'];
                }
            }

        } catch (ContainerExceptionInterface $e) {
            logger("Error Happened While Fetching Token For 9psb:");
        }
        return $token;
    }
    public function generateTokenVirtualAccounts()
    {
        $token = false;
        //check to see if token has expired;
        try {
            $token = cache()->get("9psbVirtualToken");
            if (is_null($token)) {
                $url = $this->dynamicAccBaseUrl . "vmw-api/v1/merchant/authenticate";
                $payload = [
                    "publickey" => $this->dynamicAccPubKey,
                    "privatekey" => $this->dynamicAccPrivKey,
                ];
                $result = $this->getWithRestfulHeaders($url, "post", $payload);

                //store token in cache ;
                if (isset($result['access_token'])) {
                    cache([
                        "9psbVirtualToken" => $result['access_token'],
                        "expiry" => $result['expires_in'],
                    ], now()->addSeconds($result['expires_in']));
                    $token = $result['access_token'];
                }
            }

        } catch (\Exception $e) {
            logger("Error Happened While Fetching Token For 9psb:");
        }
        return $token;
    }

    /**
     * @throws \Exception
     */
    public function getBanks()
    {
        $banks = [];
        $url = $this->baseUrl . "waas/api/v1/get_banks";
        $payload = [
            "merchantID" => "GROOVE"
        ];
        $result = $this->getWithRestfulHeaders($url, "post", $payload, $this->generateToken());
        if (isset($result['data']["bankList"])) {
            $bankList = $result['data']['bankList'];
            foreach ($bankList as $item) {
                $banks[] = [
                    "name" => $item['bankName'],
                    "code" => $item['nibssBankCode'] ?? $item['bankCode']
                ];
            }
        }
        return $banks;

    }

    public function reserveDynamicAccount($trnx_id,$amount)
    {
        $message = "Failed to Reserve Dynamic Account! ";
        $status = false;
        $resp = [
            "status" => $status,
            "message" => $message
        ];
        if ($trnx_id > 30){
            $trnx_id = substr($trnx_id,0,25);
        }
        $url = $this->dynamicAccBaseUrl."vmw-api/v1/merchant/account/generate";
        $spayRef = $trnx_id."_".microtime(true);
        $payload = [
            "transaction" => [
                "reference" => $spayRef
            ],
            "order" => [
                "amount" => $amount,
                "currency" => "NGN",
                "description" => "Spay$trnx_id",
                "country" => "NG",
            ],
            "customer" => [
                "account" => [
                    "name" => "SAANAPAY-$trnx_id",
                    "type" => "DYNAMIC",
                    "expiry" => [
                        "hours" => 1
                    ],
                ],
            ],
        ];

        $result = $this->getWithRestfulHeaders($url, "post", $payload, $this->generateTokenVirtualAccounts());


        if (isset($result['code'])){
            if ($result['code'] === "S20"){
                $data = [
                  "initiationTranRef" => $result['transaction']['linkingreference'] ,
                    "account_number" => $result['customer']['account']['number'],
                    "account_name" => $result['customer']['account']['name'],
                    "bank_name" => $result['customer']['account']['bank']

                ];

                $resp = [
                    "status" => true,
                    "data" => $data,
                    "message" => $result['message']
                ];

            }

            $resp['message'] = $result['message'];

        }
        return  $resp;


    }

    public function updateDynamicAccount($account_number,$account_name)
    {
        $resp = false;
        $url = $this->dynamicAccBaseUrl."vmw-api/v1/merchant/account/update";
        $payload = [
            "transaction" => ["reference" => microtime(true)],

            "customer" => [
                "account" => [
                    "name" => $account_name,
                    "number" => $account_number
                ],
            ],
        ];


        $result = $this->getWithRestfulHeaders($url, "post", $payload, $this->generateTokenVirtualAccounts());

        if (isset($result['code'])){
            if ($result['code'] === "S20"){
                $resp = true;
            }

        }
        return  $resp;

    }


    public function toogleDynamicAccount($account_number,$type)
    {
        $type = strtolower($type);
        $message = "Failed to toggle Dynamic Account $account_number ";
        $status = false;
        $resp = [
            "status" => $status,
            "message" => $message
        ];

        //type must be in
        $blockType = ["block", "unblock"];
        if (!in_array($type, $blockType, true)){
            $message .= "Type must be in ". json_encode($blockType, JSON_THROW_ON_ERROR);
            $resp['message'] = $message;
            return  $resp;
        }

        $url = $this->dynamicAccBaseUrl."vmw-api/v1/merchant/account/$type";
        $payload = [
            "transaction" => ["reference" => microtime(true)],
            "customer" => [
                "account" => [
                    "number" => $account_number
                ],
            ],
        ];


        $result = $this->getWithRestfulHeaders($url, "post", $payload, $this->generateTokenVirtualAccounts());


        if (isset($result['code'])){
            if ($result['code'] === "S20"){
                $resp = [
                    "status" => true,
                    "message" => $result["message"]
                ];
            }

        }
        return  $resp;

    }

    public function transactionStatusDynamicAccount($trnx)
    {
        $message = "Failed to get Status for Dynamic Account Ref $trnx ";
        $status = false;
        $resp = [
            "status" => $status,
            "payment" => "UNKNWN",
            "message" => $message
        ];

        $payload = [
            "externalreference" => $trnx
        ];
        $linkingreference = str_contains($trnx,"9PSB");

        if ($linkingreference){
            $payload = [
                "linkingreference" => $trnx,
            ];
        }

        $url = $this->dynamicAccBaseUrl."vmw-api/v1/merchant/account/transaction";

        $result = $this->getWithRestfulHeaders($url, "get", $payload, $this->generateTokenVirtualAccounts());

        if (count($result['transactions'])){
            //overwrite result;
            $result = $result['transactions'][0];
            if (isset($result['code'])){
                if ($result['code'] === "S12"){
                    $resp = [
                        "status" => true,
                        "payment" => "failed",
                        "message" => $result["message"],

                    ];
                }
                if ($result['code'] === "S20"){
                    $resp = [
                        "status" => true,
                        "payment" => "pending",
                        "message" => $result["message"]
                    ];
                }
                if ($result['code'] === "0" || $result['code'] === "00"){
                    $resp = [
                        "status" => true,
                        "payment" => "successful",
                        "message" => $result["message"]
                    ];
                }
                $resp['data'] = $result;

            }
        }
        return  $resp;

    }

}
