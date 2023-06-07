<?php


namespace App\Lib\Services;


use Illuminate\Support\Facades\Http;

class Providus
{
    private $base_url;
    private $client_secret;
    private $client_id;
    private $password;
    private $username;
    private $rest_url;

    public function __construct()
    {
        $this->base_url = config('providus.base_url');
        $this->rest_url = config('providus.rest_url');
        $this->client_secret = config('providus.client_secret');
        $this->client_id = config('providus.client_id');
        $this->client_secret_old_ptpp = config('providus.client_secret_old_ptpp');
        $this->client_id_old_ptpp = config('providus.client_id_old_ptpp');
        $this->password = config('providus.password');
        $this->username = config('providus.username');
    }



    function reserveAccount($account_name, $bvn, string $first_name, string $lats_name, string $phone_number, string $email)
    {
        try {
            $response = $this->getWithHeaders()->post($this->base_url."PiPCreateReservedAccountNumber",[
                "account_name" => $account_name,
            ]);
            return json_decode($response->body());
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }
    public function repushNotification($trnx)
    {
        try {
            //if $trnx length > 25 that means it's session Id
            if (strlen($trnx)>25) {
                $data = ['session_id' => $trnx];
            } else {
                $data = ["settlement_id" => $trnx];
            }
            $response = $this->getWithHeaders()->post($this->base_url."PiP_RepushTransaction_SettlementId",$data);
            return json_decode($response->body(), false, 512, JSON_THROW_ON_ERROR);

        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }
    public function repushNotificationOldPtpp($trnx)
    {
        try {
            //if $trnx length > 25 that means it's session Id
            if (strlen($trnx)>25) {
                $data = ['session_id' => $trnx];
            } else {
                $data = ["settlement_id" => $trnx];
            }
            $this->client_id = $this->client_id_old_ptpp;
            $this->client_secret = $this->client_secret_old_ptpp;


            $response = $this->getWithHeaders()->post($this->base_url."PiP_RepushTransaction_SettlementId",$data);
            return json_decode($response->body(), false, 512, JSON_THROW_ON_ERROR);

        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }

    public function verifyTransaction($trnx)
    {
        try {
            //if $trnx length > 25 that means it's session Id
            if (strlen($trnx)>25) {
                $url = $this->base_url."PiPverifyTransaction_sessionid";
                $data = ['session_id' => $trnx];
            } else {
                $url = $this->base_url."PiPverifyTransaction_settlementid";
                $data = ["settlement_id" => $trnx];
            }
            $response = $this->getWithHeaders()->get($url,$data);
            return json_decode($response->body());
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }

    public function verifyTransactionOldPtpp($trnx)
    {
        try {
            //if $trnx length > 25 that means it's session Id
            if (strlen($trnx)>25) {
                $url = $this->base_url."PiPverifyTransaction_sessionid";
                $data = ['session_id' => $trnx];
            } else {
                $url = $this->base_url."PiPverifyTransaction_settlementid";
                $data = ["settlement_id" => $trnx];
            }
            $this->client_id = $this->client_id_old_ptpp;
            $this->client_secret = $this->client_secret_old_ptpp;

            $response = $this->getWithHeaders()->get($url,$data);
            return json_decode($response->body(), false);
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }

    public function getBvnDetails($bvn)
    {
        try {
            $response = $this->getWithRestfulHeaders()->post($this->rest_url."postingrest/GetBVNDetails",[
                "bvn" => $bvn,
                "userName" => $this->username,
                "password" => $this->password,
            ]);
            return json_decode($response->body());
        }catch (\Exception $exception){
            return $exception->getMessage();
        }


    }
    public function  getNIPBanks()
    {
        try {
            $response = $this->getWithRestfulHeaders()->get($this->rest_url."postingrest/GetNIPBanks");
            return json_decode($response->body());
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }

    public function  generateDynamicAccountNumber($account_name, $bvn)
    {

        try {
            $response = $this->getWithHeaders()->post($this->base_url."PiPCreateDynamicAccountNumber", [
                "account_name" => $account_name,
            ]);
            return json_decode($response->body());
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }
    public function  getProvidusAccount($accountNumber)
    {
        try {
            $response = $this->getWithRestfulHeaders()->post($this->rest_url."postingrest/GetProvidusAccount",[
                "accountNumber" => $accountNumber,
                "userName" => $this->username,
                "password" => $this->password,
            ]);
            return json_decode($response->body());
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }

    public function  NIPFundTransfer($accountName,$accountNumber,$bankCode,$amount,$narration,$sourceAccountName,$transRef)
    {
        try {
            $response = $this->getWithRestfulHeaders()->post($this->rest_url."postingrest/NIPFundTransfer",[
                "beneficiaryAccountName"=>$accountName,
                "transactionAmount"=> $amount,
                "currencyCode"=>"NGN",
                "narration"=>$narration,
                "sourceAccountName"=>"Nnamdi Adebayo Hamzat" ,
                "beneficiaryAccountNumber"=>$accountNumber,
                "beneficiaryBank"=>$bankCode,
                "transactionReference"=>$transRef,
                "userName" => $this->username,
                "password" => $this->password,
            ]);
            return json_decode($response->body());
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }
    public function  getNIPAccount($accountNumber,$bankCode)
    {
        try {
            $response = $this->getWithRestfulHeaders()->post($this->rest_url."postingrest/GetNIPAccount",[
                "accountNumber" => $accountNumber,
                "beneficiaryBank" => $bankCode,
                "userName" => $this->username,
                "password" => $this->password,
            ]);
            return json_decode($response->body());
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }
    public function  getNIPTransactionStatus($transRef)
    {
        try {
            $response = $this->getWithRestfulHeaders()->post($this->rest_url."postingrest/GetNIPTransactionStatus",[
                "transactionReference" => $transRef,
                "userName" => $this->username,
                "password" => $this->password,
            ]);
            return json_decode($response->body());
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }
    public function  providusFundTransfer($accountNumber,$amount,$narration,$transRef)
    {
        try {
            $response = $this->getWithRestfulHeaders()->post($this->rest_url."postingrest/ProvidusFundTransfer",[
                "creditAccount"=>$accountNumber,
                "debitAccount"=>"1700313889",
                "transactionAmount"=> $amount,
                "currencyCode"=>"NGN",
                "narration"=>$narration,
                "transactionReference"=>$transRef,
                "userName" => $this->username,
                "password" => $this->password,
            ]);
            return json_decode($response->body());
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }
    public function  getProvidusTransactionStatus($transRef)
    {
        try {
            $response = $this->getWithRestfulHeaders()->post($this->rest_url."postingrest/GetProvidusTransactionStatus",[
                "transactionReference" => $transRef,
                "userName" => $this->username,
                "password" => $this->password,
            ]);
            return json_decode($response->body());
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }

    /**
     * @return \Illuminate\Http\Client\PendingRequest
     */
    public function getWithHeaders(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders([
            "Client-Id" => $this->client_id,
            "X-Auth-Signature" => hash("sha512", "$this->client_id:$this->client_secret"),
        ])->withoutVerifying();
    }

    /**
     * @return \Illuminate\Http\Client\PendingRequest
     */
    public function getWithRestfulHeaders(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders([
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ])->withoutVerifying();
    }
}
