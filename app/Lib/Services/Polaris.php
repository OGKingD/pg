<?php


namespace App\Lib\Services;


use Illuminate\Support\Facades\Http;

class Polaris
{
    private $base_url;
    private $client_secret;
    private $api_key;
    private string $request_ref;

    public function __construct()
    {
        $this->base_url = config('polaris.base_url');
        $this->client_secret = config('polaris.client_secret');
        $this->api_key = config('polaris.api_key');
        $this->request_ref = (int)str_replace(".", "", microtime(true)) . random_int(0, 99999);
    }

    /**
     * @return array
     */
    public function getBanks(): array
    {

        $customer = [
            "customer_ref" => $this->request_ref,
        ];
        $request_type = "get_banks";
        $auth = [
            'type' => null,
            'secure' => null,
            'auth_provider' => null,
            'route_mode' => null,
        ];
        $tranx_description = "get list of all banks";
        $payload = $this->payload($request_type, $customer, $auth, $tranx_description);

        $response = $this->callEndpoint("POST", $payload);

        return $this->formatResult($response);

    }

    /**
     * @param $requestType
     * @param $customer
     * @param $auth
     * @param $tranx_description
     * @param null $meta
     * @param null $details
     * @param int $amount
     * @return array
     */
    public function payload($requestType, $customer, $auth, $tranx_description, int $amount = 0, $meta = null, $details = null): array
    {

        return [
            'request_ref' => $this->request_ref,
            'request_type' => $requestType,
            'auth' =>
                $auth,
            'transaction' =>
                [
                    'mock_mode' => null,
                    'transaction_ref' => $this->request_ref,
                    'transaction_desc' => $tranx_description,
                    'transaction_ref_parent' => "",
                    'amount' => $amount,
                    'customer' =>
                        $customer,
                    'meta' => $meta,
                    'details' => $details
                ],
        ];

    }

    /**
     * @param $url
     * @param $httpVerb
     * @param $payload
     * @return array|mixed
     */
    public function callEndpoint($httpVerb, $payload, $url = null)
    {
        $url = is_null($url) ? $this->base_url : $url;

        $response = Http::withHeaders([
            "Signature" => hash("MD5", "$this->request_ref;$this->client_secret"),
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ])->withToken($this->api_key)->withoutVerifying();

        if (strtoupper($httpVerb) === "GET") {
            return $response->get($url, $payload)->json();
        }

        return $response->post($url, $payload)->json();

    }

    /**
     * @param $response
     * @return array
     */
    public function formatResult($response): array
    {
        $result = [
            "status" => $response['status'],
            "message" => $response['message'],
            "request_ref" => $this->request_ref,
        ];

        if ($response['status'] === "Successful") {
            $result['data'] = $response['data']['provider_response'];
            return $result;
        }

        if ($response['status'] === "WaitingForOTP") {
            //OTP validation required;
            $result['data'] = $response['data']['provider_response'];
            $result['flag'] = "otp_required";
            return $result;
        }

        if ($response['status'] === "Processing") {
            $result['data'] = $response['data']['provider_response'];
            return $result;
        }


        if ($response['status'] === "Failed") {
            $result['errors'] = $response['data']['errors'];
            return $result;
        }

        return $result;

    }

    /**
     * @param $account_number
     * @param $bankCode
     * @return array
     */
    public function getBalance($account_number, $bankCode): array
    {
        $customer = [
            "customer_ref" => $this->request_ref,
        ];
        $request_type = "get_balance";
        $secure = encrypt3des("$account_number;$bankCode", $this->client_secret);

        $auth = [
            'type' => 'bank.account',
            'secure' => $secure,
            'auth_provider' => "Polaris",
            'route_mode' => null,
        ];
        $tranx_description = "Getting Customer Balance";

        $payload = $this->payload($request_type, $customer, $auth, $tranx_description);

        $response = $this->callEndpoint("POST", $payload);
        return $this->formatResult($response);

    }

    /**
     * @param $account_number
     * @param $bankCode
     * @return array
     */
    public function collect($account_number, $bankCode, $amount): array
    {
        //{
        //    "request_ref": "{{request-ref}}",
        //    "request_type": "collect",
        //    "auth": {
        //        "type": "bank.account",
        //        "secure": "{{encrypted_source_account_number}}",
        //        "auth_provider": "Polaris",
        //        "route_mode": null
        //    },
        //    "transaction": {
        //        "mock_mode": "Live",
        //        "transaction_ref": "{{transaction-ref}}",
        //        "transaction_desc": "A random transaction",
        //        "transaction_ref_parent": "",
        //        "amount": 10000,
        //        "customer": {
        //            "customer_ref": "{{customer_id}}",
        //            "firstname": "Uju",
        //            "surname": "Usmanu",
        //            "email": "ujuusmanu@gmail.com",
        //            "mobile_no": "234802343132"
        //        },
        //        "meta": {
        //            "a_key": "a_meta_value_1",
        //            "b_key": "a_meta_value_2"
        //        },
        //        "details": null
        //    }
        //}
        //convert Amount to Kobo;
        $amount_in_kobo = $amount * 100;
        $customer = [
            "customer_ref" => "234802343132",
            "firstname" => "John",
            "surname" => "Doe",
            "email" => "johndoe@gmail.com",
            "mobile_no" => "234802343132"
        ];
        $request_type = "collect";
        $secure = encrypt3des("$account_number;$bankCode", $this->client_secret);

        $auth = [
            'type' => 'bank.account',
            'secure' => $secure,
            'auth_provider' => null,
            'route_mode' => null,
        ];
        $tranx_description = "Initiating Payment Request";
        $meta = [
            "a_key" => "a_meta_value_1",
        ];

        $payload = $this->payload($request_type, $customer, $auth, $tranx_description, $amount_in_kobo, $meta);

        $response = $this->callEndpoint("POST", $payload);
        dd($response, $payload, json_encode($payload));
        return $this->formatResult($response);

    }

    public function validateOtp($otp, $transaction_ref)
    {
        //{
        //    "request_ref": "{{request-ref}}",
        //    "request_type": "lookup_bvn_max",
        //    "auth": {
        //        "secure": "boif0gxwkZu3XYOwAD/MkA==",
        //        "auth_provider": "Polaris"
        //    },
        //    "transaction": {
        //        "transaction_ref": "67507990331863"
        //    }
        //}

        $customer = null;
        $request_type = "lookup_otp";
        $secure = encrypt3des($otp, $this->client_secret);

        $auth = [
            'secure' => $secure,
            'auth_provider' => "Polaris",
        ];
        $tranx_description = "Validating OTP";
        $this->request_ref = $transaction_ref;


        $payload = $this->payload($request_type, $customer, $auth, $tranx_description);


        $response = $this->callEndpoint("POST", $payload, $this->base_url . "/validate");
        return $this->formatResult($response);

    }

    public function openAccount($bvn, $full_name, $dob, $gender)
    {
        //{
        //    "request_ref": "{{request-ref}}",
        //    "request_type": "open_account",
        //    "auth": {
        //        "type": "bvn",
        //        "secure": "R83dCymHAdz6WcD2dUPnAdZ6Vpp6Qyxv",
        //        "auth_provider": "Polaris",
        //        "route_mode": null
        //    },
        //    "transaction": {
        //        "mock_mode": "Live",
        //        "transaction_ref": "{{transaction-ref}}",
        //        "transaction_desc": "A random transaction",
        //        "transaction_ref_parent": null,
        //        "amount": 0,
        //        "customer": {
        //            "customer_ref": "2348033000989",
        //            "firstname": "John",
        //            "surname": "Doe",
        //            "email": "john@doe.com",
        //            "mobile_no": "2348033000989"
        //        },
        //        "meta": {},
        //        "details": {
        //            "name_on_account": "John J. Doe",
        //            "middlename": "Jane",
        //            "dob": "2005-05-13",
        //            "gender": "M",
        //            "title": "Mr",
        //            "address_line_1": "23, Okon street, Ikeja",
        //            "address_line_2": "Ikeja",
        //            "city": "Mushin",
        //            "state": "Lagos State",
        //            "country": "Nigeria"
        //        }
        //    }
        //}
        $customer = [
            "customer_ref" => "2348033000989",
            "firstname" => "Joh",
            "surname" => "Doe",
            "email" => "johndoe@gmail.com",
            "mobile_no" => "2348033000989"
        ];
        $request_type = "open_account";
        $secure = encrypt3des($bvn, $this->client_secret);

        $auth = [
            'type' => 'bvn',
            'secure' => $secure,
            'auth_provider' => "Polaris",
            'route_mode' => null,
        ];
        $tranx_description = "Creating Account";
        $meta = [
            "a_key" => "a_meta_value_1",
        ];
        $details = [
            "name_on_account" => $full_name,
            "dob" => $dob,
            "gender" => $gender,
            "title" => ($gender === "M") ? "Mr" : "Mrs",
            "address_line_1" => "23, Okon street, Ikeja",
            "address_line_2" => "Ikeja",
            "city" => "Mushin",
            "state" => "Lagos State",
            "country" => "Nigeria"
        ];

        $payload = $this->payload($request_type, $customer, $auth, $tranx_description, 0, $meta, $details);
//        dd($payload);

        $response = $this->callEndpoint("POST", $payload);
        dd($response,$payload);
        return $this->formatResult($response);


    }

    public function openVirtualAccount($bvn, $full_name, $dob, $gender)
    {
        //{
        //    "request_ref": "{{request-ref}}",
        //    "request_type": "open_account",
        //    "auth": {
        //        "type": "bvn",
        //        "secure": "R83dCymHAdz6WcD2dUPnAdZ6Vpp6Qyxv",
        //        "auth_provider": "Polaris",
        //        "route_mode": null
        //    },
        //    "transaction": {
        //        "mock_mode": "Live",
        //        "transaction_ref": "{{transaction-ref}}",
        //        "transaction_desc": "A random transaction",
        //        "transaction_ref_parent": null,
        //        "amount": 0,
        //        "customer": {
        //            "customer_ref": "2348033000989",
        //            "firstname": "John",
        //            "surname": "Doe",
        //            "email": "john@doe.com",
        //            "mobile_no": "2348033000989"
        //        },
        //        "meta": {},
        //        "details": {
        //            "name_on_account": "John J. Doe",
        //            "middlename": "Jane",
        //            "dob": "2005-05-13",
        //            "gender": "M",
        //            "title": "Mr",
        //            "address_line_1": "23, Okon street, Ikeja",
        //            "address_line_2": "Ikeja",
        //            "city": "Mushin",
        //            "state": "Lagos State",
        //            "country": "Nigeria"
        //        }
        //    }
        //}
        $customer = [
            "customer_ref" => $this->request_ref,
//            "firstname" => "Uju",
//            "surname" => "Usmanu",
//            "email" => "ujuusmanu@gmail.com",
//            "mobile_no" => "234802343132"
        ];
        $request_type = "open_account";
        $secure = encrypt3des($bvn, $this->client_secret);

        $auth = [
            'type' => 'bvn',
            'secure' => null,
            'auth_provider' => "Polaris",
            'route_mode' => null,
        ];
        $tranx_description = "Creating  Virtual Account";
        $meta = [
        ];
        $details = [
            "name_on_account" => $full_name,
            "dob" => $dob,
            "gender" => $gender,
            "title" => ($gender === "M") ? "Mr" : "Mrs",
//                    "address_line_1" => "23, Okon street, Ikeja",
//                    "address_line_2" => "Ikeja",
            //            "city" => "Mushin",
            //            "state" => "Lagos State",
            //            "country" => "Nigeria"
        ];

        $payload = $this->payload($request_type, $customer, $auth, $tranx_description, 0, $meta, $details);
//        dd($payload);

        $response = $this->callEndpoint("POST", $payload);
        return $this->formatResult($response);


    }


    public function openWallet($bvn, $full_name, $dob, $gender)
    {
        //{
        //    "request_ref": "{{request-ref}}",
        //    "request_type": "open_wallet",
        //    "auth": {
        //        "type": "bvn",
        //        "secure": "R83dCymHAdz6WcD2dUPnAdZ6Vpp6Qyxv",
        //        "auth_provider": "Polaris",
        //        "route_mode": null
        //    },
        //    "transaction": {
        //        "mock_mode": "Live",
        //        "transaction_ref": "{{transaction-ref}}",
        //        "transaction_desc": "A random transaction",
        //        "transaction_ref_parent": null,
        //        "amount": 0,
        //        "customer": {
        //            "customer_ref": "2348033000989",
        //            "firstname": "John",
        //            "surname": "Doe",
        //            "email": "john@doe.com",
        //            "mobile_no": "2348033000989"
        //        },
        //        "meta": {
        //            "account_currency": "USD" // USD/NGN
        //        },
        //        "details": {
        //            "name_on_account": "John J. Doe",
        //            "middlename": "Jane",
        //            "dob": "2005-05-13",
        //            "gender": "M",
        //            "title": "Mr",
        //            "address_line_1": "23, Okon street, Ikeja",
        //            "address_line_2": "Ikeja",
        //            "city": "Mushin",
        //            "state": "Lagos State",
        //            "country": "Nigeria"
        //        }
        //    }
        //}
        $customer = [
            "customer_ref" => $this->request_ref,
//            "firstname" => "Uju",
//            "surname" => "Usmanu",
//            "email" => "ujuusmanu@gmail.com",
//            "mobile_no" => "234802343132"
        ];
        $request_type = "open_wallet";
        $secure = encrypt3des($bvn, $this->client_secret);

        $auth = [
            'type' => 'bvn',
            'secure' => $secure,
            'auth_provider' => "Polaris",
            'route_mode' => null,
        ];
        $tranx_description = "Creating Wallet";
        $meta = [
            "a_key" => "a_meta_value_1",
        ];
        $details = [
            "name_on_account" => $full_name,
            "dob" => $dob,
            "gender" => $gender,
            "title" => ($gender === "M") ? "Mr" : "Mrs",
//                    "address_line_1" => "23, Okon street, Ikeja",
//                    "address_line_2" => "Ikeja",
            //            "city" => "Mushin",
            //            "state" => "Lagos State",
            //            "country" => "Nigeria"
        ];

        $payload = $this->payload($request_type, $customer, $auth, $tranx_description, 0, $meta, $details);

        $response = $this->callEndpoint("POST", $payload);
        return $this->formatResult($response);


    }
}
