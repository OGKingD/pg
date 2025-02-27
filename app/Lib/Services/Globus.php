<?php

namespace App\Lib\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Globus
{
    public $auth_url;
    public $client_secret;
    public $client_id;
    public $scope;
    public $base_url;

    public function __construct()
    {
        $this->client_secret = config('globus.client_secret');
        $this->client_id = config('globus.client_id');
        $this->auth_url = config('globus.auth_base_url');
        $this->scope = config('globus.scope');
        $this->base_url = config('globus.base_url');
        $this->linked_partner_account_number = config('globus.linkedPartnerAccountNumber');
        $this->transfer_linked_account_number = config('globus.TransferLinkedAccount');
    }

    private function computeSha256Hash($input_String): string
    {
        return hash('sha256', $input_String);
    }

    private function setUsername(): string
    {
        $currentDate = date('Ymd');
        return $this->computeSha256Hash($currentDate . $this->client_id);

    }

    /**
     * Fetch and return the Globus token, either from cache or by generating a new one.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function generateToken(): ?string
    {
        $token = cache()->get("GlobusToken");

        if (!$token) {
            try {
                $payload = [
                    'client_secret' => $this->client_secret,
                    'client_id' => $this->client_id,
                    'scope' => $this->scope,
                    'username' => $this->setUsername(),
                    'password' => $this->computeSha256Hash($this->client_id),
                    'grant_type' => 'password'
                ];

                $header = [
                    'Accept' => 'application/json',
                    'ClientId' => $this->client_id
                ];

                $response = $this->httpRequest($header)->asMultipart()->post($this->auth_url, $payload)->json();

                if (isset($response['access_token'])) {
                    $token = $response['access_token'];
                    cache()->put("GlobusToken", $token, now()->addSeconds($response['expires_in']));
                }
            } catch (\Exception $e) {
                logger("Error fetching token: " . $e->getMessage());
            }
        }

        return $token;
    }



    public function httpRequest(array $headers = []): \Illuminate\Http\Client\PendingRequest
    {
        if (empty($headers)) {
            $headers = $this->setHeaders();
        }

        return Http::withoutVerifying()->withHeaders($headers);

    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function setHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->generateToken(),
            'Accept' => 'application/json',
            'ClientId' => $this->client_id
        ];
    }

    /**
     * Make an API request and return the response or the result.
     */
    private function handleApiResponse($response)
    {
        if (isset($response['responsecode'])){
            if ($response['responsecode'] == '00'){
                return  ['status' => $response['responsecode']] + $response['result'];
            }
        }


        return $response;
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
        $url = $this->base_url . 'retail-account';
        $response = $this->httpRequest()->post($url, $data)->json();

        return $this->handleApiResponse($response);
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
        $response = $this->httpRequest()->post($url, $data)->json();

        return $this->handleApiResponse($response);
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
        $response = $this->httpRequest()->post($url, $data)->json();

        return $this->handleApiResponse($response);
    }

    public function generateVirtualAccountMax($accountName, $canExpire, $expiredTime, $transactionAmount, $partnerReference, bool $hasTransactionAmount)
    {

        $data = [
            'accountName' => $accountName,
            'linkedPartnerAccountNumber'=> $this->linked_partner_account_number,
            'canExpire'=> $canExpire,
            'expiredTime'=> $expiredTime,
            'hasTransactionAmount'=> $hasTransactionAmount,
            'transactionAmount'=> $transactionAmount,
            'partnerReference' => $partnerReference
        ];
        if (!$hasTransactionAmount){
           unset($data['transactionAmount']);
        }
        if (!$canExpire){
            unset($data['expiredTime']);
        }

        $url = $this->base_url . 'virtual-account-max';
        $response = $this->httpRequest()->post($url, $data)->json();

        return $this->handleApiResponse($response);
    }

    public function getStates()
    {
        $url = $this->base_url.'get-states';
        $response = $this->httpRequest()->get($url)->json();

        return $this->handleApiResponse($response);
    }

    public function getCities($state_id)
    {
        $url = $this->base_url.'city/'.$state_id;
        $response = $this->httpRequest()->get($url)->json();

        return $this->handleApiResponse($response);
    }

    public function getAccountBalanceByAcctNo($account_no)
    {
        $url = $this->base_url.'account-balance/'.$account_no;
        $response = $this->httpRequest()->get($url)->json();

        return $this->handleApiResponse($response);
    }

    public function NameEnquiryByAccountNoAndBankCode($account_no, $bank_code)
    {
        $url = $this->base_url.'name-enquiry/'.$account_no.'/'.$bank_code;
        $response = $this->httpRequest()->get($url)->json();

        return $this->handleApiResponse($response);
    }

    public function getAccountNoByPhoneNo($phone_number)
    {
        $url = $this->base_url.'account-by-phoneno/'.$phone_number;
        return  $this->httpRequest()->get($url)->json();
    }

    public function getBanks()
    {
        $url = $this->base_url.'banks';
        $response = $this->httpRequest()->get($url)->json();

        return $this->handleApiResponse($response);
    }

    public function getBvnDetails($bvn,$firstname,$lastname,$dob)
    {
        $url = $this->base_url.'bbvn-details';
        $details = [
            'bvn' => $bvn,
            'firstName' => $firstname,
            'lastName' => $lastname,
            'dateOfBirth' => $dob
        ];
        $response = $this->httpRequest()->post($url,$details)->json();

        return $this->handleApiResponse($response);


    }

    public function singletTransfer($amount, $destinationAccount, $destinationAccountName, $destinationBankCode, $transId, $narration)
    {
        $data = [
            'sourceAccount' => '1000000483',
            'sourceAccountName' => 'AKINTUNDE M OKUNOLA',
            'sourceCcy' => 'NGN',
            'amount' => $amount,
            'destinationAccount' => $destinationAccount,
            'destinationAccountName' => $destinationAccountName,
            'destinationCcy' => 'NGN',
            'destinationBankCode' => $destinationBankCode,
            'transId' => $transId,
            'appUser' => 'tparty',
            'narration' => $narration,
            'nipNameEnqRef' => $transId
        ];

        //always enquire first;
        $response = $name_enquiry = $this->NameEnquiryByAccountNoAndBankCode($destinationAccount, $destinationBankCode);

        if (isset($name_enquiry['status'])) {
            if ($name_enquiry['status'] == '00') {
                $url = $this->base_url.'single-transfer';
                $data['nipNameEnqRef'] = $name_enquiry['sessionID'];
                $response = $this->httpRequest()->post($url, $data)->json();
                if (isset($response['result'])){
                    $response['result']['sessionID'] = $name_enquiry['sessionID'];
                }
            }
        }
        return $this->handleApiResponse($response);

    }

    public function getTranxStatus($trn)
    {
        $url = $this->base_url.'transaction-status/tparty/'.$trn;
        $response = $this->httpRequest()->get($url)->json();

        return $this->handleApiResponse($response);

    }

    public function notificationByAccountAndDateRange($accountNo, $startDate = null,$endDate = null)
    {
        if (empty($startDate)){
            $startDate = now()->format('Y-m-d');
        }
        if (empty($endDate)){
            $endDate = now()->format('Y-m-d');
        }
        $url = $this->base_url."notifications/$accountNo/$startDate/$endDate";

        $response = $this->httpRequest()->get($url)->json();
        dd($response,$url);

    }
    public function notificationBySessionId($sessionId)
    {

        $url = $this->base_url."notifications/$sessionId";

        $response = $this->httpRequest()->get($url)->json();
        dd($response,$url);

    }

}
