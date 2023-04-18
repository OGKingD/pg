<?php


namespace App\Lib\Services;


use Illuminate\Support\Facades\Log;

class Rave
{
//Api keys
    protected $publicKey;
    protected $env;
    protected $secretKey;
    protected $txref;
    protected $integrityHash;
    protected $payButtonText = 'Proceed with Payment';
    protected $redirectUrl;
    protected $meta = array();
    protected $transactionPrefix;
    protected $handler;
    protected $stagingUrl = 'https://api.flutterwave.com';
    protected $liveUrl = 'https://api.flutterwave.com';
    protected $baseUrl;
    protected $transactionData;
    protected $overrideTransactionReference;
    protected $requeryCount = 0;

    //Payment information
    protected $account;
    protected $accountno;
    protected $key;
    protected $pin;
    protected $json_options;
    protected $post_data;
    protected $options;
    protected $card_no;
    protected $cvv;
    protected $expiry_month;
    protected $expiry_year;
    protected $amount;
    protected $paymentOptions = null;
    protected $customDescription;
    protected $customLogo;
    protected $customTitle;
    protected $country;
    protected $currency;
    protected $customerEmail;
    protected $customerFirstname;
    protected $customerLastname;
    protected $customerPhone;
    protected $subaccounts = array();

    //EndPoints
    protected $end_point;
    protected $authModelUsed;
    protected $flwRef;
    protected $type;


    /**
     * Construct
     * @param string $secretKey Your Rave secretKey. Sign up on https://rave.flutterwave.com to get one from your settings page
     * @param string $prefix This is added to the front of your transaction reference numbers
     * @param boolean $overrideRefWithPrefix Set this parameter to true to use your prefix as the transaction reference
     */
    public function __construct($secretKey, $prefix = 'RV', $overrideRefWithPrefix = false)
    {
        $this->secretKey = $secretKey;
        $this->publicKey = config('flutterwave.public_key');
        $this->env = config('flutterwave.env');
        $this->transactionPrefix = $overrideRefWithPrefix ? $prefix : $prefix . '_';
        $this->overrideTransactionReference = $overrideRefWithPrefix;

        $this->baseUrl = $this->stagingUrl;

        if ($this->env === 'live') {
            $this->baseUrl = $this->liveUrl;
        }

        $this->createReferenceNumber();

        Log::notice('Rave Class Initializes....');
    }

    /**
     * Generates a transaction reference number for the transactions
     * @return void
     * */
    public function createReferenceNumber(): void
    {
        Log::notice('Generating Reference Number....');
        $this->txref = uniqid($this->transactionPrefix, true);

        if ($this->overrideTransactionReference) {
            $this->txref = $this->transactionPrefix;
        }
        Log::notice('Generated Reference Number....' . $this->txref);
    }

    /**
     * gets the current transaction reference number for the transaction
     * @return string
     * */
    public function getReferenceNumber(): string
    {
        return $this->txref;
    }

    /**
     * Sets the transaction account number
     * @param  $accountno
     * @return void
     * */
    public function setAccountNumber($accountno): void
    {
        $this->accountno = $accountno;
    }

    /**
     * Sets the transaction card number
     * @param $card_no
     * @return void
     * */
    public function setCardNo($card_no): void
    {
        $this->card_no = $card_no;
    }

    /**
     * Sets the transaction CVV
     * @param $cvv
     * @return void
     */
    public function setCVV($cvv)
    {
        $this->cvv = $cvv;
    }

    /**
     * Sets the transaction expiry_month
     * @param $expiry_month
     * @return void
     * */
    public function setExpiryMonth($expiry_month)
    {
        $this->expiry_month = $expiry_month;
    }

    /**
     * Sets the transaction expiry_year
     * @param integer $expiry_year Transaction expiry_year
     * @return object
     * */
    public function setExpiryYear($expiry_year)
    {
        $this->expiry_year = $expiry_year;
        return $this;
    }

    /**
     * Sets the transaction authmodel
     * @param string $authmodel
     * @return object
     * */
    public function setAuthModel($authmodel)
    {
        $this->authModelUsed = $authmodel;
        return $this;
    }

    /**
     * gets the transaction amount
     * @return string
     * */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Sets the transaction amount
     * @param $amount
     * @return void
     * */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    /**
     * gets the allowed payment methods
     * @return string
     * */
    public function getPaymentOptions()
    {
        return $this->paymentOptions;
    }

    /**
     * Sets the allowed payment methods
     * @param string $paymentOptions The allowed payment methods. Can be card, account or both
     * @return object
     * */
    public function setPaymentOptions($paymentOptions)
    {
        $this->paymentOptions = $paymentOptions;
        return $this;
    }

    /**
     * Sets the transaction description
     * @param string $customDescription The description of the transaction
     * @return object
     * */
    public function setDescription($customDescription)
    {
        $this->customDescription = $customDescription;
        return $this;
    }

    /**
     * gets the transaction description
     * @return string
     * */
    public function getDescription()
    {
        return $this->customDescription;
    }

    /**
     * Sets the payment page logo
     * @param string $customLogo Your Logo
     * @return object
     * */
    public function setLogo($customLogo)
    {
        $this->customLogo = $customLogo;
        return $this;
    }

    /**
     * gets the payment page logo
     * @return string
     * */
    public function getLogo()
    {
        return $this->customLogo;
    }

    /**
     * Sets the payment page title
     * @param string $customTitle A title for the payment. It can be the product name, your business name or anything short and descriptive
     * @return object
     * */
    public function setTitle($customTitle)
    {
        $this->customTitle = $customTitle;
        return $this;
    }

    /**
     * gets the payment page title
     * @return string
     * */
    public function getTitle()
    {
        return $this->customTitle;
    }

    /**
     * gets the transaction country
     * @return string
     * */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Sets transaction country
     * @param string $country The transaction country. Can be NG, US, KE, GH and ZA
     * @return object
     * */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * gets the transaction currency
     * @return string
     * */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Sets the transaction currency
     * @param string $currency The transaction currency. Can be NGN, GHS, KES, ZAR, USD, EUR and GBP
     * @return object
     * */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Sets the customer email
     * @param string $customerEmail This is the paying customer's email
     * @return object
     * */
    public function setEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    /**
     * gets the customer email
     * @return string
     * */
    public function getEmail()
    {
        return $this->customerEmail;
    }

    /**
     * Sets the customer firstname
     * @param string $customerFirstname This is the paying customer's firstname
     * @return object
     * */
    public function setFirstname($customerFirstname)
    {
        $this->customerFirstname = $customerFirstname;
        return $this;
    }

    /**
     * gets the customer firstname
     * @return string
     * */
    public function getFirstname()
    {
        return $this->customerFirstname;
    }

    /**
     * Sets the customer lastname
     * @param string $customerLastname This is the paying customer's lastname
     * @return object
     * */
    public function setLastname($customerLastname)
    {
        $this->customerLastname = $customerLastname;
        return $this;
    }

    /**
     * gets the customer lastname
     * @return string
     * */
    public function getLastname()
    {
        return $this->customerLastname;
    }

    /**
     * Sets the customer phonenumber
     * @param string $customerPhone This is the paying customer's phonenumber
     * @return object
     * */
    public function setPhoneNumber($customerPhone)
    {
        $this->customerPhone = $customerPhone;
        return $this;
    }

    /**
     * gets the customer phonenumber
     * @return string
     * */
    public function getPhoneNumber()
    {
        return $this->customerPhone;
    }

    /**
     * gets payment page button text
     * @return string
     * */
    public function getPayButtonText()
    {
        return $this->payButtonText;
    }

    /**
     * Sets the payment page button text
     * @param string $payButtonText This is the text that should appear on the payment button on the Rave payment gateway.
     * @return object
     * */
    public function setPayButtonText($payButtonText)
    {
        $this->payButtonText = $payButtonText;
        return $this;
    }

    /**
     * gets the transaction redirect url
     * @return string
     * */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Sets the transaction redirect url
     * @param string $redirectUrl This is where the Rave payment gateway will redirect to after completing a payment
     * @return object
     * */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    /**
     * Sets the transaction meta data. Can be called multiple time to set multiple meta data
     * @param array $meta This are the other information you will like to store with the transaction. It is a key => value array. eg. PNR for airlines, product colour or attributes. Example. array('name' => 'femi')
     * @return void
     * */
    public function setMetaData($meta)
    {
        $this->meta[] = $meta;
    }

    /**
     * gets the transaction meta data
     * @return array
     * */
    public function getMetaData()
    {
        return $this->meta;
    }

    /**
     * Sets the transaction sub account. Can be called multiple time to set multiple sub accounts
     * @param array $subaccount
     * @return object
     * */
    public function setSingleSubaccount($subaccount)
    {
        $this->subaccounts[] = $subaccount;
        return $this;
    }

    /**
     * gets the transaction sub accounts
     * @return array
     * */
    public function getSubAccount()
    {
        return $this->subaccounts;
    }

    /**
     * Gets the transaction reference
     * @return object
     * */
    public function getTxRef()
    {
        return $this->txref;
    }

    /**
     * Sets the transaction reference
     * @param string $txref
     * @return object
     * */
    public function setTxRef($txref)
    {
        $this->txref = $txref;
        return $this;
    }

    /**
     * Gets the transaction type
     * @return object
     * */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the transaction type
     * @param string $txref
     * @return object
     * */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Sets the event hooks for all available triggers
     * @param object $handler This is a class that implements the Event Handler Interface
     * @return object
     * */
    public function eventHandler($handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * Gets the rave instance
     * @return object
     * */
    public function getRaveInstance()
    {
        return $this;
    }

    /**
     * Requerys a previous transaction from the Rave payment gateway
     * @param string $referenceNumber This should be the reference number of the transaction you want to requery
     * @return object
     * */
    public function requeryTransaction($referenceNumber)
    {
        $this->txref = $referenceNumber;
        $this->requeryCount++;
        Log::notice('Requerying Transaction....' . $this->txref);
        if (isset($this->handler)) {
            $this->handler->onRequery($this->txref);
        }

        $data = array(
            'id' => (int)$referenceNumber
            // 'only_successful' => '1'
        );

        // make request to endpoint using unirest.
        $headers = array('Content-Type' => 'application/json', 'Authorization' => $this->secretKey);
        $body = Body::json($data);
        $url = $this->baseUrl . '/v3/transactions/' . $data['id'] . '/verify';
        // Make `POST` request and handle response with unirest
        $response = Request::get($url, $headers);

        // print_r($response);

        //check the status is success
        if ($response->body && $response->body->status === "success") {
            if ($response->body && $response->body->data && $response->body->data->status === "successful") {
                Log::notice('Requeryed a successful transaction....' . json_encode($response->body->data));
                // Handle successful
                if (isset($this->handler)) {
                    $this->handler->onSuccessful($response->body->data);
                }
            } elseif ($response->body && $response->body->data && $response->body->data->status === "failed") {
                // Handle Failure
                Log::warn('Requeryed a failed transaction....' . json_encode($response->body->data));
                if (isset($this->handler)) {
                    $this->handler->onFailure($response->body->data);
                }
            } else {
                // Handled an undecisive transaction. Probably timed out.
                Log::warn('Requeryed an undecisive transaction....' . json_encode($response->body->data));
                // I will requery again here. Just incase we have some devs that cannot setup a queue for requery. I don't like this.
                if ($this->requeryCount > 4) {
                    // Now you have to setup a queue by force. We couldn't get a status in 5 requeries.
                    if (isset($this->handler)) {
                        $this->handler->onTimeout($this->txref, $response->body);
                    }
                } else {
                    Log::notice('delaying next requery for 3 seconds');
                    sleep(3);
                    Log::notice('Now retrying requery...');
                    $this->requeryTransaction($this->txref);
                }
            }
        } else {
            // Log::warn('Requery call returned error for transaction reference.....'.json_encode($response->body).'Transaction Reference: '. $this->txref);
            // Handle Requery Error
            if (isset($this->handler)) {
                $this->handler->onRequeryError($response->body);
            }
        }
        return $this;
    }

    /**
     * Generates the final json to be used in configuring the payment call to the rave payment gateway
     * @param array $options
     * @return  string (HTML response)
     * */
    public function initialize(array $options = array())
    {
        $this->createCheckSum();

        // get paymentOptions
        $payment_options = 'card,mobilemoney,ussd';
        if (isset($this->paymentOptions) and !empty($this->paymentOptions)) {
            $payment_options = $this->paymentOptions;
        }

        $data = [
            'public_key' => $this->publicKey,
            'tx_ref' => $this->txref,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'country' => $this->country,
            'payment_options' => $payment_options,
            'redirect_url' => $this->redirectUrl,
            'customer' => [
                'email' => $this->customerEmail,
                'phone_number' => $this->customerPhone,
                'name' => "{$this->customerFirstname} {$this->customerLastname}",
            ],
            'customizations' => [
                'title' => $this->customTitle,
                'description' => $this->customDescription,
                'logo' => $this->customLogo,
            ],
        ];

        // set subaccounts
        if (!empty($this->subaccounts)) {
            $data['subaccounts'] = $this->subaccounts;
        }

        // return the json array
        if ($options['json_response'] ?? false) {
            return json_encode($data, JSON_THROW_ON_ERROR);
        }

        $response = '';
        $response .= '<html>';
        $response .= '<body>';
        $response .= '<center>Proccessing...<br /><img style="height: 50px;" src="https://media.giphy.com/media/swhRkVYLJDrCE/giphy.gif" /></center>';

        $response .= '<script type="text/javascript" src="https://checkout.flutterwave.com/v3.js"></script>';

        $response .= '<script>';
        $response .= 'document.addEventListener("DOMContentLoaded", function(event) {';

        $response .= 'var data = ' . json_encode($data) . ';';

        $response .= 'data["onclose"] = function() {
            window.location = "?cancelled=cancelled";
        };';

        $response .= 'data["callback"] = function (data) {
            console.log(data);
        };';

        $response .= 'FlutterwaveCheckout(data);';
        $response .= '});';

        $response .= '</script>';
        $response .= '</body>';
        $response .= '</html>';

        // echo the response
        if ($options['echo_response'] ?? true ) {
            echo $response;
        }

        // return the html response string
        return $response;
    }

    /**
     * Generates a checksum value for the information to be sent to the payment gateway
     * @return void
     *
     * @throws \JsonException
     */
    public function createCheckSum(): void
    {
        Log::notice('Generating Checksum....');
        $options = array(
            "public_key" => $this->publicKey,
            "amount" => $this->amount,
            "tx_ref" => $this->txref,
            "currency" => $this->currency,
            "payment_options" => "card,mobilemoney,ussd",
            "customer" => [
                "email" => $this->customerEmail,
                "phone_number" => $this->customerPhone,
                "name" => $this->customerFirstname . " " . $this->customerLastname
            ],
            "redirect_url" => $this->redirectUrl,
            "customizations" => [
                "description" => $this->customDescription,
                "logo" => $this->customLogo,
                "title" => $this->customTitle,
            ],
            "meta" => $this->meta
        );

        ksort($options);

        $this->transactionData = $options;

        if (isset($this->handler)) {
            $this->handler->onInit((object)$this->transactionData);
        }

        //encrypt the required options to pass to the server
        $this->json_options = json_encode($this->transactionData, JSON_THROW_ON_ERROR);
        $this->integrityHash = $this->encryption($this->json_options);

    }

    /**
     * this is the encryption function that combines the getkey() and encryptDes().
     * @param string
     * @return string
     * */

    public function encryption($options)
    {
        //encrypt and return the key using the secrekKey
        $this->key = config('flutterwave.encryption_key');
        //set the data to transactionData
        $this->transactionData = $options;
        //encode the data and the
        return $this->encrypt3Des($this->transactionData, $this->key);
    }

    /**
     * this is the encrypt3Des function that generates an encryption Key for you by passing your transaction Data and Secret Key as a parameter.
     * @param $data
     * @param $key
     * @return string
     */

    public function encrypt3Des($data, $key): string
    {
        $encData = openssl_encrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA);
        return base64_encode($encData);
    }

    /**
     * this is the getKey function that generates an encryption Key for you by passing your Secret Key as a parameter.
     * @param string
     * @return string
     * */

    public function getKey($seckey)
    {
        $hashedkey = md5($seckey);
        $hashedkeylast12 = substr($hashedkey, -12);

        $seckeyadjusted = str_replace("FLWSECK-", "", $seckey);
        $seckeyadjustedfirst12 = substr($seckeyadjusted, 0, 12);

        return $seckeyadjustedfirst12 . $hashedkeylast12;
    }

    /**
     * verify the transaction before giving value to your customers
     * @param string
     * @return object
     * */
    public function verifyTransaction($id = null)
    {
        Log::notice('Verifying transaction...');

        $url = "";
        if (isset($id)) {
            $url = "/" . $id . "/verify";
            $this->setEndPoint("v3/transactions");
        }

        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    /**
     * Sets the transaction end point
     * @param string $end_point Transaction expiry_year
     * @return object
     * */
    public function setEndPoint($end_point)
    {
        $this->end_point = $end_point;
        return $this;
    }

    /**
     * makes a get call to the api
     * @param array
     * @return object
     * */

    public function getURL($url)
    {
        // make request to endpoint using unirest.
        $bearerTkn = 'Bearer ' . $this->secretKey;
        $headers = array('Content-Type' => 'application/json', 'Authorization' => $bearerTkn);
        //$body = Body::json($data);
        $path = $this->baseUrl . '/' . $this->end_point;
        return Request::get($path . $url, $headers)->raw_body;    // Unparsed body
    }

    /**
     * Validate the transaction to be charged
     * @param string
     * @return object
     * */
    public function validateTransaction($otp, $ref, $type)
    {
        Log::notice('Validating otp...');
        $this->setEndPoint("v3/validate-charge");
        $this->post_data = array(
            'type' => $type,    //type can be card or account
            'flw_ref' => $ref,
            'otp' => $otp
        );
        $result = $this->postURL($this->post_data);
        return json_decode($result, true);
    }

    /**
     * makes a post call to the api
     * @param array
     * @return object
     * */

    public function postURL($data)
    {
        // make request to endpoint using unirest

        $bearerTkn = 'Bearer ' . $this->secretKey;
        $headers = array('Content-Type' => 'application/json', 'Authorization' => $bearerTkn);
        $body = Body::json($data);
        $url = $this->baseUrl . '/' . $this->end_point;
        $response = Request::post($url, $headers, $body);
        return $response->raw_body;    // Unparsed body
    }

    public function validateTransactionPin($otp, $Ref)
    {
        Log::notice('Validating pin...');
        $this->setEndPoint("v3/validate-charge");
        $this->post_data = array(
            'PBFPubKey' => $this->publicKey,
            'transaction_reference' => $Ref,
            'otp' => $otp
        );
        $result = $this->postURL($this->post_data);
        return json_decode($result, true);
    }

    /**
     * Get all Transactions
     * @return object
     * */

    public function getAllTransactions(array $array = array())
    {
        Log::notice('Getting all Transactions...');

        // create url query
        $url = "";
        if (!empty($array)) {
            $query = http_build_query($array);
            $url .= "?{$query}";
        }

        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    public function getTransactionFee(array $array = array())
    {
        // create url query
        $url = "";
        if (!empty($array)) {
            $query = http_build_query($array);
            $url .= "?{$query}";
        }

        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    public function transactionTimeline()
    {
        $url = "";
        $result = $this->getURL($url);
        return json_decode($result, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Get all Settlements
     * @return object
     * */

    public function getAllSettlements()
    {
        Log::notice('Getting all Subscription...');
        $url = "";
        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    /**
     * Validating your bvn
     * @param string
     * @return object
     * */

    public function bvn($bvn)
    {
        Log::notice('Validating bvn...');
        $url = "/" . $bvn;
        return json_decode($this->getURL($url), true);
    }

    /**
     * Get all Subscription
     * @return object
     * */

    public function getAllSubscription()
    {
        Log::notice('Getting all Subscription...');
        $url = '';
        return json_decode($this->getURL($url), true);
    }

    /**
     * Get a Subscription
     * @param $id ,$email
     * @return object
     * */

    public function cancelSubscription()
    {
        Log::notice('Canceling Subscription...');
        $data = array();
        $result = $this->putURL($data);
        return json_decode($result, true);
    }

    public function putURL($data)
    {
        $bearerTkn = 'Bearer ' . $this->secretKey;
        $headers = array('Content-Type' => 'application/json', 'Authorization' => $bearerTkn);
        $body = Body::json($data);
        $url = $this->baseUrl . '/' . $this->end_point;
        return Request::put($url, $headers, $body)->raw_body;
    }

    /**
     * Get a Settlement
     * @param $id ,$email
     * @return object
     * */

    public function fetchASettlement()
    {
        Log::notice('Fetching a Subscription...');
        $url = "?seckey=" . $this->secretKey;
        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    /**
     * activating  a subscription
     * @return object
     * */

    public function activateSubscription()
    {
        Log::notice('Activating Subscription...');
        $data = array();
        $result = $this->putURL($data);
        return json_decode($result, true);
    }

    /**
     * Creating a payment plan
     * @param array
     * @return object
     * */

    public function createPlan($array)
    {
        Log::notice('Creating Payment Plan...');
        $result = $this->postURL($array);
        return json_decode($result, true);
    }

    public function updatePlan($array)
    {
        Log::notice('Updating Payment Plan...');

        $result = $this->putURL($array);
        return json_decode($result, true);
    }

    public function cancelPlan($array)
    {
        Log::notice('Canceling Payment Plan...');

        $result = $this->putURL($array);
        return json_decode($result, true);
    }

    public function getPlans()
    {
        $url = "";
        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    public function getSinglePlan()
    {
        $url = "";
        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    /**
     * Creating a beneficiary
     * @param array
     * @return object
     * */

    public function createBeneficiary($array)
    {
        Log::notice('Creating beneficiaries ...');
        $result = $this->postURL($array);
        return json_decode($result, true);
    }

    /**
     * get  beneficiaries
     * @param array
     * @return object
     * */


    public function getBeneficiaries()
    {
        $url = "";
        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    /**
     * transfer payment api
     * @param array
     * @return object
     * */

    public function transferSingle($array)
    {
        Log::notice('Processing transfer...');
        $result = $this->postURL($array);
        return json_decode($result, true);
    }

    public function deleteBeneficiary()
    {
        $url = "";
        $result = $this->delURL($url);
        return json_decode($result, true);
    }

    public function delURL($url)
    {
        $bearerTkn = 'Bearer ' . $this->secretKey;
        $headers = array('Content-Type' => 'application/json', 'Authorization' => $bearerTkn);
        //$body = Body::json($data);
        $path = $this->baseUrl . '/' . $this->end_point;
        return Request::delete($path . $url, $headers)->raw_body;
    }

    /**
     * bulk transfer payment api
     * @param array
     * @return object
     * */

    public function transferBulk($array)
    {
        Log::notice('Processing bulk transfer...');
        $result = $this->postURL($array);
        return json_decode($result, true);
    }

    /**
     * Refund payment api
     * @param array
     * @return object
     * */

    public function refund($array)
    {
        Log::notice('Initiating a refund...');
        $result = $this->postURL($array);
        return json_decode($result, true);
    }

    /**
     * Generates the final json to be used in configuring the payment call to the rave payment gateway api
     * @param array
     * @return object
     * */
    public function chargePayment($array)
    {
        //remove the type param from the payload
        $this->options = $array;

        if ($this->type === 'card') {
            $this->json_options = json_encode($this->options);
            Log::notice('Checking payment details..');
            //encrypt the required options to pass to the server
            $this->integrityHash = $this->encryption($this->json_options);
            $this->post_data = array('client' => $this->integrityHash);

            $result = $this->postURL($this->post_data);
            // the result returned requires validation
            $result = json_decode($result, true);

            if ($result['status'] == 'success') {
                if ($result['meta']['authorization']['mode'] == 'pin'
                    || $result['meta']['authorization']['mode'] == 'avs_noauth'
                    || $result['meta']['authorization']['mode'] == 'redirect'
                    || $result['meta']['authorization']['mode'] == 'otp') {
                    Log::notice('Payment requires otp validation...authmodel:' . $result['meta']['authorization']['mode']);
                    $this->authModelUsed = $result['meta']['authorization']['mode'];

                    if ($this->authModelUsed == 'redirect') {
                        // header('Location:'.$result['meta']['authorization']['redirect']);
                        return $result;
                    }

                    if ($this->authModelUsed == 'pin' || $this->authModelUsed == 'avs_noauth') {
                        return $result;
                    }

                    if ($this->authModelUsed == 'otp') {
                        $this->flwRef = $result['data']['flw_ref'];
                        return [
                            'data' => [
                                "flw_ref" => $this->flwRef,
                                "id" => $result['data']['id'],
                                "auth_mode" => $result['meta']['authorization']['mode'],
                            ]
                        ];
                    }
                }
            } else {
                throw new \Exception($result['message'], 1);
            }

            //passes the result to the suggestedAuth function which re-initiates the charge
        } elseif ($this->type == "momo") {
            $result = $this->postURL($array);
            $result = json_decode($result, true);

            // print_r($result['meta']);
            if (isset($result['meta']['authorization'])) {
                // header('Location:'.$result['meta']['authorization']['redirect']);
                return $result;
            }

            return $result;
        } else {
            $result = $this->postURL($array);
            // the result returned requires validation
            $result = json_decode($result, true);

            if (isset($result['meta']['redirect'])) {
                // header('Location:'.$result['meta']['redirect']);
                return $result;
            }

            if (isset($result['data']['status'])) {
                Log::notice('Payment requires otp validation...');
                $this->authModelUsed = $result['data']['auth_model'];
                $this->flwRef = $result['data']['flw_ref'];
                $this->txref = $result['data']['tx_ref'];
            }


            return $result;
        }
    }

    /**
     * sends a post request to the virtual APi set by the user
     * @param array
     * @return object
     * */

    public function vcPostRequest($array)
    {
        $this->post_data = $array;
        //post the data to the API
        $result = $this->postURL($this->post_data);
        //decode the response
        return json_decode($result, true);
    }

    public function vcGetRequest()
    {
        $url = "";
        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    public function vcPutRequest($array = array())
    {
        $result = $this->putURL($array);
        return json_decode($result, true);
    }

    /**
     * Used to create sub account on the rave dashboard
     * @param array
     * @return object
     * */
    public function createSubaccount($array)
    {
        $this->options = $array;
        Log::notice('Creating Sub account...');
        //pass $this->options to the postURL function to call the api
        $result = $this->postURL($this->options);
        return json_decode($result, true);
    }

    public function getSubaccounts()
    {
        $url = "";
        //pass $this->options to the postURL function to call the api
        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    /**
     * Sets the transaction sub account. Can be called multiple time to set multiple sub accounts
     * @param array $subaccounts
     * @return object
     * */
    public function setSubaccounts($subaccounts)
    {
        $this->subaccounts = $subaccounts;
        return $this;
    }

    public function fetchSubaccount()
    {
        $url = "";
        //pass $this->options to the postURL function to call the api
        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    public function updateSubaccount($array)
    {
        $this->options = $array;
        Log::notice('updating Sub account...');
        //pass $this->options to the postURL function to call the api
        $result = $this->putURL($this->options);
        return json_decode($result, true);
    }

    public function deleteSubaccount($array = array())
    {
        Log::notice('deleting  Sub account...');
        //pass $this->options to the postURL function to call the api
        $result = $this->putURL($array);
        return json_decode($result, true);
    }

    /**
     * Handle canceled payments with this method
     * @param string $referenceNumber This should be the reference number of the transaction that was canceled
     * @return object
     * */
    public function paymentCanceled($referenceNumber)
    {
        Log::notice('Payment was canceled by user..' . $this->txref);
        if (isset($this->handler)) {
            $this->handler->onCancel($referenceNumber);
        }
        return $this;
    }

    /**
     * This is used to create virtual account for a merchant.
     * @param string $array
     * @return object
     */
    public function createVirtualAccount($array)
    {
        $this->options = $array;
        Log::notice('creating virtual account..');
        $result = $this->postURL($this->options);
        return json_decode($result, true);
    }

    /**
     * Create bulk virtual accounts with this method
     * @param string $array
     * @return object
     * */

    public function createBulkAccounts($array)
    {
        $this->options = $array;
        Log::notice('creating bulk virtual account..');
        $result = $this->postURL($this->options);
        return json_decode($result, true);
    }


    /**
     * Get  bulk virtual virtual cards method
     * @return object
     * */

    public function getBulkAccounts()
    {
        $url = "";
        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    /**
     * Create an Order with this method
     * @param array $array
     * @return object
     * */

    public function createOrder($array)
    {
        Log::notice('creating Ebill order for customer with email: ' . $array['email']);

        if (empty($array['narration']) || !array_key_exists('narration', $array)) {
            $array['narration'] = '';
        }
        if (empty($data['IP'])) {
            $array['IP'] = '10.30.205.3';
        }
        if (!isset($array['custom_business_name']) || empty($array['custom_business_name'])) {
            $array['custom_business_name'] = '';
        }

        if (empty($array['number_of_units']) || !array_key_exists('number_of_units', $array)) {
            $array['number_of_units'] = "1";
        }

        $data = array(
            'narration' => $array['narration'],
            'number_of_units' => $array['number_of_units'],
            'currency' => $array['currency'],
            'amount' => $array['amount'],
            'phone_number' => $array['phone_number'],
            'email' => $array['email'],
            'tx_ref' => $array['tx_ref'],
            'ip' => $array['ip'],
            'country' => $array['country'],
            'custom_business_name' => $array['custom_business_name']
        );
        $result = $this->postURL($data);
        return json_decode($result, true);
    }

    /**
     * Update an Order with this method
     * @param string $array
     * @return object
     * */
    public function updateOrder($array)
    {
        Log::notice('updating Ebill order..');

        $data = array(
            'amount' => $array['amount'],
            'currency' => "NGN"// only NGN can be passed
        );


        $result = $this->putURL($data);
        $result = json_decode($result, true);
        return json_decode($result, true);
    }

    /**
     * pay bill or query bill information with this method
     * @param array $array
     * @return object
     * */

    public function bill($array)
    {
        if (!isset($array['type'])) {
            $error = array('Type' => 'Missing the type property in the payload');
            return $error;
        }

        Log::notice($array['type'] . ' Billing ...');

        $data = array();
        $data["type"] = $array["type"];
        $data["country"] = $array["country"];
        $data["customer"] = $array["customer"];
        $data["amount"] = $array["amount"];
        $data["recurrence"] = $array["recurrence"];
        $data["reference"] = $array["reference"];
        $result = $this->postUrl($data);

        return json_decode($result, true);
    }

    public function bulkBills($array)
    {
        $data = $array;

        $result = $this->postUrl($data);

        return json_decode($result, true);
    }

    public function getBill($array)
    {
        if (array_key_exists('reference', $array) && !array_key_exists('from', $array)) {
            $url = "/" . $array['reference'];
        } elseif (array_key_exists('code', $array) && !array_key_exists('customer', $array)) {
            $url = "/" . $array['item_code'];
        } elseif (array_key_exists('id', $array) && array_key_exists('product_id', $array)) {
            $url = "/" . $array['id'] . "/products/" . $array['product_id'];
        } elseif (array_key_exists('from', $array) && array_key_exists('to', $array)) {
            if (isset($array['page']) && isset($array['reference'])) {
                $url = '?from=' . $array['from'] . '&' . $array['to'] . '&' . $array['page'] . '&' . $array['reference'];
            } else {
                $url = '?from=' . $array['from'] . '&' . $array['to'];
            }
        }

        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    public function getBillers()
    {
        $url = '/billers';
        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    public function getBillCategories()
    {
        $url = '/bill-categories';
        $result = $this->getURL($url);
        return json_decode($result, true);
    }


    public function tokenCharge($array)
    {
        $data = $array;

        if (!isset($data['token']) && !isset($data['currency']) &&
            !isset($data['country']) && !isset($data['amount']) &&
            !isset($data['tx_ref']) && !isset($data['email'])) {
            $error = array('error' => 'Your payload is missing all properties');
            return $error;
        }

        $result = $this->postUrl($array);
        return json_decode($result, true);
    }

    /**
     * List of all transfers with this method
     * @param string $data
     * @return object
     * */

    public function listTransfers(array $array = array())
    {
        Log::notice('Fetching list of transfers...');

        // create url query
        $url = "";
        if (!empty($array)) {
            $query = http_build_query($array);
            $url .= "?{$query}";
        }

        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    /**
     * Check  a bulk transfer status with this method
     * @param string $data
     * @return object
     * */

    public function bulkTransferStatus($data)
    {
        Log::notice('Checking bulk transfer status...');
        $url = "?batch_id=" . $data['batch_id'];
        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    /**
     * Check applicable fees with this method
     * @param string $data
     * @return object
     * */

    public function applicableFees(array $array = array())
    {
        Log::notice('Fetching applicable fees...');

        // create url query
        $url = "";
        if (!empty($array)) {
            $query = http_build_query($array);
            $url .= "?{$query}";
        }

        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    /**
     * Retrieve Transfer balance with this method
     * @param string $array
     * @return object
     * */

    public function getTransferBalance($array)
    {
        Log::notice('Fetching Transfer Balance...');
        if (empty($array['currency'])) {
            $array['currency'] = 'NGN';
        }
        $data = array(
            "currency" => $array['currency']
        );
        $result = $this->postURL($data);
        return json_decode($result, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Verify an Account to Transfer to with this method
     * @param string $array
     * @return object
     * */

    public function verifyAccount($array)
    {
        Log::notice('Verifying transfer recipents account...');
        $data = array(
            "account_number" => $array['account_number'],
            "account_bank" => $array['account_bank']
        );
        $result = $this->postURL($data);
        return json_decode($result, true);
    }

    /**
     * Lists banks for Transfer with this method
     * @return object
     * */

    public function getBanksForTransfer()
    {
        Log::notice('Fetching banks available for Transfer...');

        //get banks for transfer
        $url = "";
        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    /**
     * Lists banks for Transfer with this method
     * @return object
     * */

    public function getBankBranchesForTransfer()
    {
        Log::notice('Fetching bank branches available for Transfer...');

        //get banks for transfer
        $url = "";
        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    /**
     * Captures funds this method
     * @param string $array
     * @return object
     * */

    public function captureFunds($array)
    {
        Log::notice('capturing funds for flwRef: ' . $array['flwRef'] . ' ...');
        $data = array(
            "flwRef" => $array['flwRef'],
            "amount" => $array['amount']

        );
        $result = $this->postURL($data);
        return json_decode($result, true);
    }

    public function getvAccountsNum()
    {
        $url = "";
        $result = $this->getURL($url);
        return json_decode($result, true);
    }

    /**
     * Refund or Void a fund with this method
     * @param array $array
     * @return object
     * */

    public function refundOrVoid($array)
    {
        Log::notice($array['action'] . 'ing a captured fund with the flwRef=' . $array['flwRef']);

        $data = array(
            "ref" => $array['flwRef'],
            "action" => $array['action'],
            "SECKEY" => $this->secretKey
        );
        $result = $this->postURL($data);
        return json_decode($result, true);
    }


}
