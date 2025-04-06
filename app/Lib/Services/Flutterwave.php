<?php

namespace App\Lib\Services;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Http;

class Flutterwave
{

    public string $txRef;
    /**
     * @var Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private mixed $baseUrl;
    /**
     * @var mixed
     */
    private $secretKey;
    /**
     * @var mixed
     */
    private $encryptionKey;


    public function __construct($secretKey,$encryptionKey)
    {
        $this->baseUrl = config('flutterwave.base_url');
        $this->secretKey = $secretKey;
        $this->encryptionKey = $encryptionKey;
    }

    public function setTxRef($ref): void
    {
        $this->txRef = $ref;
    }

    public function getTxRef(): string|int
    {
        return $this->txRef;
    }


    /**
     * Card Charge
     *
     * @param array $data
     * @return array
     *
     * @throws \Exception
     */
    public function cardCharge(array $data): array
    {
        if (empty($data['tx_ref'])){
            $data['tx_ref'] = $this->getTxRef();
        }

        $url = $this->baseUrl."/charges?type=card";
        $payload = ["client" => $this->encryptPayload($data)];
        return $this->callEndpoint($url,'POST',$payload);

    }

    /**
     * Charge a transaction using Google Pay
     *
     * @param array $payload The payload data for the transaction
     * @return array The response from the API call
     */
    public function chargeGooglePay($payload)
    {
        $url = config('flutterwave.google_pay_url');

        return $this->callEndpoint($url,"POST",$payload);

    }


    /**
     * Charge a transaction using Apple Pay
     *
     * @param array $payload The payload data for the transaction
     * @return array The response from the API call
     */
    public function chargeApplePay($payload)
    {
        $url = config('flutterwave.apple_pay_url');

        return $this->callEndpoint($url,"POST",$payload);

    }


    /**
     * Format Charge Card Response
     *
     * @param array $response
     * @return array
     * <pre>
     * [
     * 'status' => boolean,
     * 'authorization' => [
     *      'mode' => string, //pin|avs_noauth|redirect
     *      'pin' => string,
     *      'city' => string,
     *      'address' => string,
     *      'state' => string,
     *      'country' => string,
     *      'zipcode' => string,
     * ],
     * 'flag' => string, //pin_required|charge_card|redirect_required|otp_required
     * 'url' => string,
     * ]
     * </pre>
     */
    public function formatChargeCardResponse(array $response): array
    {
        $result = ['status' => false, "errors" => "Cannot Authorize Card!", "message" => $response['message']?? "", ];

        if (isset($response['meta']['authorization'])) {
            $result['status'] = true;
            $result['errors'] = null;

            $authorizationMode = $response['meta']['authorization']['mode'];
            $result['authorization']['mode'] = $authorizationMode;


            if ($authorizationMode === 'pin') {
                //pin required;
                $result['flag'] = "pin_required";
                $result['authorization']['pin'] = "";
            }
            if ($authorizationMode === 'avs_noauth') {
                $result["authorization"] = array("mode" => "avs_noauth", "city" => "Sampleville", "address" => "", "state" => "Simplicity", "country" => "Nigeria", "zipcode" => "000000",);
                $result['flag'] = "charge_card";

            }
            if ($authorizationMode === 'redirect') {
                $result['flag'] = "redirect_required";
                $result['url'] = $response['meta']['authorization']['redirect'];
            }


            if ($authorizationMode === 'otp') {
                $result['flag'] = "otp_required";
                $result['status'] = true;
            }
        }

        //when OTP is required;
        if (isset($response['data'])){
            $data = $response['data'];
            $result['errors'] = null;
            if (isset($data['auth_mode'])){
                if ($data['auth_mode'] === "otp"){
                    $result['flag'] = "otp_required";
                    $result['status'] = true;
                }
            }

            if (isset($data['status'])){
                if (strtoupper($data['status']) === "SUCCESSFUL"){
                    $result['status'] = true;
                    $result['flag'] = "payment_completed";
                }
            }
        }
        return $result;

    }

    //Authorize charge;
    public function validateTransaction($otp, $ref, $type="card")
    {
        $url = $this->baseUrl."/validate-charge";
        $payload = [
            "otp" => $otp,
            "flw_ref" => $ref,
            "type" => $type
        ];

        return $this->callEndpoint($url,"POST",$payload);

    }


    /**
     * Verify a transaction by reference
     *
     * @param string $ref transaction reference
     * @return array
     */
    public function verifyTransactionByRef($ref): array
    {
        $url = $this->baseUrl.'/transactions/verify_by_reference';
        $payload = ['tx_ref' => $ref];

        return $this->callEndpoint($url,"GET",$payload);
    }

    public function verifyTransaction($ref): array
    {
        $url = "$this->baseUrl/transactions/$ref/verify";

        return $this->callEndpoint($url,"GET",[]);
    }

    public function callEndpoint($url,$httpVerb,$payload)
    {
        return Http::withHeaders([
            'Authorization' => $this->secretKey,
            'content-type' => 'application/json'])->{strtolower($httpVerb)}($url, $payload)->json();

    }


    //verify transaction;
    function encryptPayload(array $payload): string
    {
        $encryptionKey = $this->encryptionKey;
        $encrypted = openssl_encrypt(json_encode($payload), 'DES-EDE3', $encryptionKey, OPENSSL_RAW_DATA);
        return base64_encode($encrypted);
    }

}
