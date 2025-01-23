<?php

namespace App\Lib\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class Globus
{
    public $auth_url;
    public $client_secret;
    public $client_id;
    public $scope;
    public $access_token;
    public $base_url;

    public function __construct()
    {
        $this->client_secret = config('globus.client_secret');
        $this->client_id = config('globus.client_id');
        $this->auth_url = config('globus.auth_base_url');
        $this->scope = config('globus.scope');
        $this->base_url = config('globus.base_url');
    }

    private function computeSha256Hash($input_String): string
    {
        return hash('sha256', $input_String);
    }

    private function setUsername(): string
    {
        $current_date = date('Ymd');
        $username_input = $current_date.$this->client_id;
        return $this->computeSha256Hash($username_input);
    }

    public function authService()
    {
        $header = [
            'Accept' => 'application/json',
            'ClientId' => $this->client_id
        ];

        $payload = [
            'client_secret' => $this->client_secret,
            'client_id' => $this->client_id,
            'scope' => $this->scope,
            'username' => $this->setUsername(),
            'password' => $this->computeSha256Hash($this->client_id),
            'grant_type' => 'password'
        ];

        $response = $this->httpRequest($this->auth_url, 'post', $payload, $header);
        $this->access_token = $response['access_token'];
    }

    public function httpRequest(string $url, string $method, array $payload = [], array $headers = [])
    {
        if (empty($headers)) {
            $headers = $this->setHeaders();
        }

        $response = Http::withHeaders($headers)->{$method}($url, $payload);

        if ($response->ok()) {
            return $response->json();
        }

        return [
            'message' => 'failed'
        ];
    }

    private function setHeaders(): array
    {
        $this->authService();
        return [
            'Authorization' => 'Bearer '.$this->access_token,
            'Accept' => 'application/json',
            'ClientId' => $this->client_id
        ];
    }

    public function generateRetailAccount(array $data)
    {
//        $arr = [
//            "salutation",
//            "firstname",
//            "middlename",
//            "lastname",
//            "bvn",
//            "streetName",
//            "city",
//            "state",
//            "dob",
//            "sex",
//            "postalCode",
//            "phoneNo",
//            "email",
//            "maritalstatus",
//            "vanityNumber",
//            "isVanity",
//            "faxNum",
//            "faxNum2",
//            "enableNofication"
//        ];

        $data['dob'] = Carbon::parse($data['dob'])->format('d-m-Y');
        $url = $this->base_url.'retail-account';
        $response = $this->httpRequest($url, 'post', $data, $this->setHeaders());
        if ($response['responsecode'] == '00') {
            return $response['result'];
        }

        return $response;
    }

    public function generateVirtualAccount(array $data)
    {
//        $arr = [
//            "dob",
//            "sex",
//            "state",
//            "city",
//            "maritalstatus",
//            "accountName",
//            "virtualAccountNumber",
//            "bvn",
//            "linkedPartnerAccountNumber",
//            "canExpire",
//            "phoneNo",
//            "customerAddress",
//            "isCardable",
//            "email",
//            "hasTransactionAmount",
//            "partnerReference",
//        ];

        $data['dob'] = Carbon::parse($data['dob'])->format('d-m-Y');
        $url = $this->base_url.'virtual-account';
        $response = $this->httpRequest($url, 'post', $data, $this->setHeaders());
        if ($response['responsecode'] == '00') {
            return $response['result'];
        }

        return $response;
    }

    public function generateVirtualAccountLite(array $data)
    {
//        $arr = [
//            'accountName',
//            'linkedPartnerAccountNumber',
//            'virtualAccountNumber',
//            'canExpire',
//            'expiredTime',
//            'hasTransactionAmount',
//            'transactionAmount',
//            'partnerReference'
//        ];

        $url = $this->base_url.'virtual-account-lite';
        $response = $this->httpRequest($url, 'post', $data, $this->setHeaders());
        if ($response['responsecode'] == '00') {
            return $response['result'];
        }

        return $response;
    }

    public function generateVirtualAccountMax(array $data)
    {
//        $arr = [
//            'accountName',
//            'linkedPartnerAccountNumber',
//            'canExpire',
//            'expiredTime',
//            'hasTransactionAmount',
//            'transactionAmount',
//            'partnerReference'
//        ];

        $url = $this->base_url.'virtual-account-max';
        $response = $this->httpRequest($url, 'post', $data, $this->setHeaders());
        if ($response['responsecode'] == '00') {
            return $response['result'];
        }

        return $response;
    }

    public function getStates()
    {
        $url = $this->base_url.'get-states';
        $response = $this->httpRequest($url, 'get', [], $this->setHeaders());
        if ($response['responsecode'] == '00') {
            return $response['data'];
        }

        return $response;
    }

    public function getCities($state_id)
    {
        $url = $this->base_url.'city/'.$state_id;
        $response = $this->httpRequest($url, 'get', [], $this->setHeaders());
        if ($response['responsecode'] == '00') {
            return $response['data'];
        }

        return $response;
    }

    public function getAccountBalanceByAcctNo($account_no)
    {
        $url = $this->base_url.'account-balance/'.$account_no;
        $response = $this->httpRequest($url, 'get', [], $this->setHeaders());
        if ($response['responsecode'] == '00') {
            return $response['result'];
        }

        return $response;
    }

    public function NameEnquiryByAccountNoAndBankCode($account_no, $bank_code)
    {
        $url = $this->base_url.'name-enquiry/'.$account_no.'/'.$bank_code;
        $response = $this->httpRequest($url, 'get', [], $this->setHeaders());
        if ($response['responsecode'] == '00') {
            return $response['result'];
        }

        return $response;
    }

    public function getAccountNoByPhoneNo($phone_number)
    {
        $url = $this->base_url.'account-by-phoneno/'.$phone_number;
        return $this->httpRequest($url, 'get', [], $this->setHeaders());
    }

    public function getBanks()
    {
        $url = $this->base_url.'banks';
        $response = $this->httpRequest($url, 'get', [], $this->setHeaders());
        if ($response['responsecode'] == '00') {
            return $response['result'];
        }
        return $response;
    }

    public function singletTransfer(array $data)
    {
//        $arr = [
//            'sourceAccount' => '1000019531',
//            'sourceAccountName' => 'Olufemi Abayomi',
//            'sourceCcy' => 'NGN',
//            'amount' => 100,
//            'destinationAccount' => '5215640024',
//            'destinationAccountName' => 'TAIWO ADEOLA ADENIYI',
//            'destinationCcy' => 'NGN',
//            'destinationBankCode' => '103',
//            'transId' => '769081257899',
//            'appUser' => 'tparty',
//            'narration' => 'RENT',
//            'nipNameEnqRef' => '22304817892'
//        ];

        $url = $this->base_url.'single-transfer';
        $response = $this->httpRequest($url, 'post', $data, $this->setHeaders());
        if ($response['responsecode'] == '00') {
            return $response['result'];
        }
        return $response;
    }

}
