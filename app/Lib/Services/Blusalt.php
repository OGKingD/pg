<?php


namespace App\Lib\Services;


class Blusalt
{
    private $api_key;
    private $base_url;
    private $email;

    public function __construct()
    {
        $this->api_key = config('Blusalt.api_key');
        $this->base_url = config('Blusalt.base_url');
        $this->email = config('Blusalt.email');
    }


    public function callEndPoint($url, $verb, $payload)
    {
        // Convert the HTTP verb to lowercase
        $httpVerb = strtolower($verb);

        // Define the headers to be included in the HTTP request
        $headers = [
            "x-api-key" => $this->api_key,
            "Content-Type" => "application/json"
        ];

        // Make an HTTP request without verifying, setting headers, and sending the request
        return httpRequestWithoutVerifying()
            ->withHeaders($headers)
            ->{$httpVerb}($url, $payload)
            ->json();
    }

    public function activateAccountForPayment(): array
    {
        $url = $this->base_url . "/activate";
        $payload = [
            "email" => $this->email,
            "phone_number" => "08023152779",
            "preferred_bank" => 1,
        ];
        $response = $this->callEndPoint($url, "POST", $payload);
        return $this->handleResponse($response);

    }

    public function otpVerify($otp, $reference)
    {
        $url = $this->base_url. "/charge/card/$reference/auth";
        $payload = ["code" => $otp];
        $response = $this->callEndPoint($url,'PUT', $payload);
        return $response['ok'] ?? false ;

    }

    public function initiatePayment($cardPan, $cvv, $expiry,$pin, $emailAddress, $amount , $redirectUrl, $trnxRef)
    {
        $url = $this->base_url . "/charge/card";
        $payload = [
            "card" => [
                "pan" => $cardPan,
                "cvv" => $cvv,
                "expiry" => $expiry,
            ],
            "user_information" => [
                "device_signature" => $trnxRef."#".request()->ip(),
                "ip_address" => request()->ip(),
                "email_address" => $emailAddress ?? "business@saanapay.ng",
                "phone_number" => "08166332211",
            ],
            "amount" => (float)$amount,
            "currency" => "NGN",
            "auth_redirect_url" => $redirectUrl,
            "transaction_reference" => $trnxRef,
        ];
        if (isset($pin)){
            $payload['card']['pin'] = $pin;
        }

        $response = $this->callEndPoint($url, "POST", $payload);
        return $this->handleResponse($response,'charge_card', $redirectUrl);

    }

    public function verifyTransaction($trnx)
    {
        $url = $this->base_url . "/transactions/$trnx";
        $response = $this->callEndPoint($url, "GET", []);
        return $this->handleResponse($response, 'verify_transaction');


    }

    public function handleResponse( $response, $callType = null, $redirectUrl = null): array
    {
        //cast callType to UpperCase;
        $callType = strtoupper($callType);
        $result = [
            "status" => false,
        ];
        $result['reference'] = null;
        $message = "Empty response! Ensure Details are correct!";
        if (!is_array($response)){
            $result['message'] = $message;
        }

        if (is_array($response)) {
            $result['message'] = $response['message'] ?? $message;

            if (isset($response['status'])) {
                //successful call;
                if ($response['status']) {
                    $result['status'] = $response['status'];
                    //check Type;
                    if ($callType === "CHARGE_CARD") {
                        $data = $response['data'];
                        $result['reference'] = $data['reference'];
                        unset($data['ok'], $data['card']);
                        $result['data'] = $data;
                        if (in_array($data['status'], ["PENDING_AUTH", "SUCCESS"])) {
                            $result['redirect_required'] = true;
                            $result['flag'] = "redirect_required";
                            $result['otp'] = false;
                            $result['url'] = $data['redirect_url'];
                        }
                        if ($data['status'] === "PENDING_AUTH_CAPTURE") {
                            $result['redirect_required'] = false;
                            $result['flag'] = "otp_required";
                            $result['otp'] = true;

                        }
                        if ($data['status'] === "SUCCESS") {
                            $result['url'] = $redirectUrl;
                        }
                    }

                    if ($callType === "VERIFY_TRANSACTION") {
                        $result = $response['data'];
                    }
                }
                if (!$response['status']) {
                    $result['errors'] = $response['message'];
                }
            }
        }
        return $result;
    }


}
