<?php

namespace App\Http\Livewire;

use App\Models\Settings;
use Exception;
use App\Lib\Services\{Blusalt, CoralPay, Flutterwave, NinePSB, Providus, Remita};
use App\Models\DynamicAccount;
use App\Models\Gateway;
use App\Models\PaymentRequest;
use App\Models\RRR;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Component;

class PaymentPage extends Component
{
    public $invoiceNo;
    public $invoiceTotal;
    public $invoiceCharge;
    public $merchantGateways;
    public $merchantSettings;
    public $cardProvider = "FLUTTERWAVE";
    public $invoice;
    public $activeTab = "card";
    public $remitaDetails;
    public $virtualAccDetails;
    public $cardDetails;
    public $gatewayId;
    public $merchantRedirectUrl;
    public $merchantAvatar;
    public $qRCodeDetails;
    public $cc_Number;
    public $cc_Expiration;
    public $cc_Pin;
    public $cc_Otp;
    public $isPinRequired;
    public $isOtpRequired;
    public $hideCardFields;
    public $user;
    public $transaction;

    protected $listeners = ['generateRRR', 'processCardTransaction',
        'cardAuthorizationWithPin', 'cardAuthorizationWithOtp', 'cardAuthorizationWithAvs',
        'generateVirtualAccountNumber', 'payWith', 'generateQRCode'];

    public function render()
    {
//        $this->setActiveTab($this->activeTab);

        return view('livewire.payment-page');
    }

    /**
     * @throws \JsonException
     * @var float|int|mixed
     */

    public function generateQRCode(CoralPay $coralPay)
    {
        $amount = $this->merchantGateways[$this->activeTab]['invoiceTotal'];

        $this->qRCodeDetails = $coralPay->createQrCode($amount, $this->invoice->transaction->transaction_ref);

        $this->dispatchBrowserEvent('qRCodeGenerated', $this->qRCodeDetails);
    }
    public function generateRRR(Remita $remitaService)
    {

        //get the INvoice NO;
        $rrr = $this->invoice->rrr;

        $this->logPaymentRequest("remita");

        $status = false;
        //check if transaction has RRR already;
        if ($rrr){
            if (!empty($rrr->rrr)) {
                $remitaUrl = config('remita.redirect_url') . "/remita/onepage/biller/$rrr->rrr/payment.spa";
                $status = true;
                $this->remitaDetails = ["status" => $status, 'RRR' => $rrr->rrr, 'url' => $remitaUrl];

            }
        }else {
            //Get Merchant Charge;
            $amount = $this->merchantGateways[$this->activeTab]['invoiceTotal'];
            $charge = $this->merchantGateways[$this->activeTab]['invoiceCharge'];
            $lineItems = [
                [
                    "lineItemsId" => "itemid1",
                    "beneficiaryName" => "University of Ibadan TSA Collections Account",
                    "beneficiaryAccount" => "0070217761016",
                    "bankCode" => "000",
                    "beneficiaryAmount" => $amount - $charge,
                    "deductFeeFrom" => "0"
                ],
                [
                    "lineItemsId" => "itemid2",
                    "beneficiaryName" => "Saanapay Collection Account",
                    "beneficiaryAccount" => "0048954708",
                    "bankCode" => "032",
                    "beneficiaryAmount" => $charge,
                    "deductFeeFrom" => "1"
                ]
            ];


            $remitaServiceId = $this->getRemitaServiceTypeId($this->invoice->transaction);
            $response = $remitaService->remitaGenerateRRR($amount, $this->invoice->invoice_no,$this->invoice->customer_name, $this->invoice->customer_email, $this->invoice->name, $remitaServiceId, $lineItems);
            $parsedResult = $tempResult = $response;
            $varType = gettype($parsedResult);

            if ($varType === "string"){

                if (str_contains($tempResult, "jsonp")) {
                    $status = true;

                    $parsedResult = json_decode(trim($response, 'jsonp ()'), false, 512, JSON_THROW_ON_ERROR);
                }

                if (!str_contains($tempResult, "jsonp")) {
                    $status = true;
                    $parsedResult = json_decode($parsedResult, false, 512, JSON_THROW_ON_ERROR);
                }

            }
            logger("Remita Response: " . json_encode($parsedResult, JSON_THROW_ON_ERROR));
            //insert RRR into rrr table;
            $this->remitaDetails = ["status" => false, 'RRR' => "N/A", 'url' => "N/A", "errors" => " Remita Service Currently Unavailable, Please contact Support@saanapay.ng for assistance!."];

            if (isset($parsedResult->RRR)){
                RRR::create([
                    'rrr' => $parsedResult->RRR,
                    'invoice_no' => $this->invoice->invoice_no,
                ]);
                $remitaUrl = config('remita.redirect_url') . "/remita/onepage/biller/$parsedResult->RRR/payment.spa";
                $this->remitaDetails = ["status" => $status, 'RRR' => $parsedResult->RRR, 'url' => $remitaUrl];

            }

        }


        $this->dispatchBrowserEvent('rrrGenerated');

    }

    public function setActiveTab($tab): void
    {
        $this->dispatchBrowserEvent('alertBox', ['type' => 'processing','message' =>'Please Wait!']);
        $this->activeTab = $tab;
        $this->dispatchBrowserEvent('closeAlert');

    }

    /**
     * @param $channel
     * @throws \JsonException
     */
    public function logPaymentRequest($channel): void
    {
        $paymentRequest = (new PaymentRequest)->firstOrCreate(
            [
                "invoice_no" => $this->invoice->invoice_no,
            ]
        );
        $response = [
            "channel" => $channel,
            "date" => Carbon::now()->toDayDateTimeString(),
        ];
        //Get the payload;
        $payload = $paymentRequest->details;
        $payload[] = $response;


        $paymentRequest->update([
            "details" => $payload,
        ]);
    }

    public function confirmPayment()
    {
        $status = false;
        if ($this->invoice->transaction->status === "successful") {
            $status = true;
        }
        $data = ['status' => $status, 'redirect_url' => route('receipt', ['id' => $this->invoice->invoice_no])];

        $this->dispatchBrowserEvent('paymentConfirmation', $data);

    }

    /**
     */
    public function generateVirtualAccountNumber($spayPrefix, $minutes=30): void
    {
        $providus = new Providus();
        $created_at = Carbon::now();
        $endTime = Carbon::parse()->addMinutes($minutes)->format('Y-m-d H:i:s T');

        try {
            $status = false;
            $accountName = "";
            $generateDynamic = true;
            $this->setActiveTab('banktransfer');
            $transferProvider = strtoupper(Settings::firstWhere("name", 'bank_transfer_provider')->value);
            $result = null;//check table to see if virtual Account Exists;
            $virtualAcc = DynamicAccount::firstWhere('invoice_no', $this->invoice->invoice_no);
            if (isset($virtualAcc)) {
                $status = true;
                //check if virtual Account has expired;
                if (Carbon::parse()->diffInMinutes($virtualAcc->created_at) < $virtualAcc->expires_at) {
                    $generateDynamic = false;
                    $created_at = $virtualAcc->created_at;
                    $endTime = Carbon::parse($virtualAcc->created_at)->addMinutes($virtualAcc->expires_at)->format('Y-m-d H:i:s T');
                    $this->virtualAccDetails = ["status" => $status, "accountNumber" => $virtualAcc->account_number, "accountName" => $virtualAcc->account_name, "bankName" => $virtualAcc->bank_name, "endtime" => $endTime];

                }

            }
            if ($generateDynamic) {

                //Check provider for bank transfer
                // Default;
                if ($transferProvider !== "9PSB") { //call Providus or Db to generate Account Number;
                    //$result = (new Providus())->reserveAccount("SAANAPAY LIMITED","", "","","","");
                    $result = $providus->generateDynamicAccountNumber($this->invoice->invoice_no, '');
                    $accountName = "PROVIDUS BANK";

                }

                // if provider = NINEPSB
                if ($transferProvider === "9PSB") {
                    $gateway = Gateway::where('name', "Bank Transfer")->select(['id','name'])->get()->pluck("id", "name");
                    $gateway_id = $gateway["Bank Transfer"];
                    /** @var Transaction $transaction */
                    $transaction = $this->invoice->transaction;
                    $transactionTotal = $transaction->computeChargeAndTotal($gateway_id);

                    $result = (object)(new NinePSB())->reserveDynamicAccount($this->invoice->invoice_no, $transactionTotal['total'],$minutes/60,$spayPrefix);
                    $result->requestSuccessful = false;

                    if ($result->status) {
                        $result = (object)$result->data;
                        $result->requestSuccessful = true;
                        $accountName = "9 Payment Service Bank (9PSB)";

                    }
                }


                if ($result->requestSuccessful) {
                    $status = true;
                    //store details into table;
                    DynamicAccount::updateOrCreate(
                        ['invoice_no' => $this->invoice->invoice_no],
                        [
                            'invoice_no' => $this->invoice->invoice_no,
                            'expires_at' => $minutes,
                            'created_at' => $created_at,
                            'account_number' => $result->account_number,
                            'account_name' => $result->account_name,
                            'bank_name' => $accountName,
                            'initiationTranRef' => $result->initiationTranRef,
                            'status' => 1
                        ]
                    );

                }
                $this->virtualAccDetails = ["status" => $status, "accountNumber" => $result->account_number ?? "N/A", "accountName" => $result->account_name ?? "N/A", "bankName" => $accountName, "endtime" => $endTime];

            }
        } catch (Exception $e) {
            logger("Error Happened while trying to generate Dynamic Account Number : {$e->getMessage()} \n {$e->getTraceAsString()}");

            $this->virtualAccDetails = ["status" => $status, "accountNumber" => null, "accountName" => null, "bankName" => $accountName];

        }

        $this->dispatchBrowserEvent('virtualAccountGenerated', $this->virtualAccDetails);


    }

    /**
     * @throws \JsonException
     */
    public function processCardTransaction(): void
    {
        //do validation;
        $this->cardDetails = json_decode($this->cardDetails, true, 512, JSON_THROW_ON_ERROR);


        list($expiry_month, $expiry_year, $cardNo, $cvv, $pin, $customer_email, $invoiceTotal) = $this->getCardDetails();
        //check for the authorization
        $details = ['status' => false, 'errors' => "Cannot Authorize Card!"];
        $this->cardDetails = array_merge($this->cardDetails, [
            "card_number" => $cardNo,
            "cvv" => $cvv,
            "expiry_month" => trim($expiry_month),
            "expiry_year" => trim($expiry_year),
            "currency" => $this->invoice->transaction->currency,
            "amount" => $invoiceTotal,
            "email" => $customer_email,
            "tx_ref" => ""
        ]);
        $this->cardProvider = isset($this->merchantSettings->values['card_provider']) ? strtoupper($this->merchantSettings->values['card_provider']) : $this->cardProvider;

        $validator = Validator::make($this->cardDetails, [
            "email" => ['required'],
            "card_number" => ['required', 'between:16,19', 'string'],
            "cvv" => ['required', 'size:3',],
            "cc_expiration" => ['required'],
            "amount" => ['required'],
        ]);


        if ($validator->fails()) {
            $messages = $validator->messages()->all();
            $error = "";
            foreach ($messages as $message) {
                $error.=  "$message \n";
            }

            $details['errors'] = $error;
            $this->dispatchBrowserEvent('cardPaymentProcessed', $details);
            return;

        }

        //call Flutterwave to charge Card;
        try {
            $trnxId = Str::random(6)."_".$this->invoice->invoice_no;
            $response = ['status' => false];

            if ($this->cardProvider === "FLUTTERWAVE"){
                $this->cardDetails['redirect_url'] = config('app.url') . "/payment/card/validate/{$this->invoice->invoice_no}";
                /** @var Flutterwave $flwave */
                [$flwave, $response] = $this->flwChargeCard();
                $trnxId = $flwave->getTxRef();
                $this->cardProvider = isset($this->merchantGateways['card']['flwave_percent']) ?'FLWAVEPERCENT' :'FLWAVEFLAT';
                //format response;
                $response = $flwave->formatChargeCardResponse($response);

            }

            if ($this->cardProvider === "BLUSALT"){
                $blusalt = new Blusalt();
                $initiateCardCharge = true;
                $this->cardDetails['redirect_url'] = config('app.url') . "/payment/card/validate/{$this->invoice->invoice_no}";
                //handle verve cards;
                $vervePattern = "/^(?:50[067][180]|6500)(?:\d{12,15})$/";
                $isVerveCard = preg_match($vervePattern, $cardNo);
                if ($isVerveCard){
                    //if it's verve and card details has Pin;
                    if (!isset($pin)){
                        $response['status'] = true;
                        $response['flag'] = "pin_required";
                        $response['authorization'] = $response['flag'];
                        $initiateCardCharge = false;
                    }
                }

                if ($initiateCardCharge){
                    $chargeDetails = $blusalt->initiatePayment($cardNo, $cvv,$this->cardDetails['cc_expiration'], $pin,$customer_email,$invoiceTotal,$this->cardDetails['redirect_url'],$trnxId);
                    $response['reference'] = $chargeDetails['reference'];
                    if ($chargeDetails['status']){
                        $response['status'] = true;
                        //check if redirectUrl;
                        if ($chargeDetails['redirect_required']){
                            $response['flag'] = "redirect_required";
                            $response['url'] = $chargeDetails['url'];
                            $response['authorization'] = $response['flag'];
                        }
                    }
                }

            }

            if ($response['status']){
                $details = $response;
                $this->cardDetails['authorization'] = $response['authorization'];
                if ( strtoupper($response['flag']) === "PIN_REQUIRED"){
                    $this->isPinRequired = true;
                    $this->hideCardFields = true;
                }
            }

            $blusaltRef = $response['reference'] ?? null;
            $tranxAtrributes = [
                "spay_ref" => $trnxId,
                'gateway_id' => $this->merchantGateways[$this->activeTab]['gateway_id'],
                'amount' => $invoiceTotal - $this->merchantGateways[$this->activeTab]['invoiceCharge'],
                'fee' => $this->merchantGateways[$this->activeTab]['invoiceCharge'],
                'total' => $invoiceTotal,
                'provider' => $this->cardProvider,
                'flutterwave_ref' => $blusaltRef,
                'provider_ref' => [
                    'blusalt' => $blusaltRef,
                    'flutterwave' => $trnxId,
                ]
            ];

            if (!isset($this->transaction)) {
                //create transaction;
                $orderedUuid = Str::orderedUuid();
                $tranxAtrributes = array_merge($tranxAtrributes, [
                    'transaction_ref' => $orderedUuid,
                    'invoice_no' => $this->invoice->invoice_no,
                    'merchant_transaction_ref' => $orderedUuid,
                    'status' => 'pending',
                    'flag' => 'debit',

                ]);
                $this->invoice->user->transaction()->create(
                    $tranxAtrributes
                );
            }

            //Update Transaction;
            if (isset($this->transaction)) {
                $this->transaction->update(
                    $tranxAtrributes
                );
            }




        } catch (Exception $e) {
            logger("An Error Occurred while trying to Process Card Payment : \n {$e->getMessage()} \n {$e->getTraceAsString()} ");
            $details = ['status' => false, 'errors' => $e->getMessage()];
        }

        $this->dispatchBrowserEvent('cardPaymentProcessed', $details);


    }

    /**
     * @return array
     * @throws Exception
     */
    public function flwChargeCard(): array
    {
        $this->user = $this->invoice->user;
        $flwave = getFlwave(isset($this->merchantGateways['card']['flwave_percent']));
        $flwave->setTxRef("SPAY{$this->invoice->invoice_no}");
        $response = $flwave->cardCharge($this->cardDetails);
        return array($flwave, $response);
    }

    public function cardAuthorizationWithPin()
    {
        list($expiry_month, $expiry_year, $cardNo, $cvv, $pin, $customer_email, $invoiceTotal) = $this->getCardDetails();
        $details = [];
        $response['status'] = false;

        try {
            if ($this->cardProvider === "BLUSALT"){

                $blusalt = new Blusalt();
                $trnxRef = Str::random(6)."_".$this->transaction->invoice_no;
                $response = $blusalt->initiatePayment($cardNo, $cvv,$this->cardDetails['cc_expiration'], $pin,$customer_email,$invoiceTotal,$this->cardDetails['redirect_url'],$trnxRef);
                $provider_ref = $this->transaction->provider_ref;

                if ($response['status']){
                    $details = $response;
                    $blusaltRef = $response['data']['reference'];
                    $provider_ref['blusalt'] = $blusaltRef;
                    $this->transaction->update([
                        'flutterwave_ref' => $blusaltRef,
                        'provider_ref' => $provider_ref,
                        'spay_ref' => $trnxRef
                    ]);
                }
            }

            if (in_array($this->cardProvider,['FLUTTERWAVE','FLWAVEPERCENT','FLWAVEFLAT'])){
                //add pin to cardDetails
                $this->cardDetails['authorization']['pin'] = $this->cc_Pin;

                //charge card finally
                /** @var Flutterwave $flwave */
                [$flwave, $response] = $this->flwChargeCard();
                //format response;
                $data = $response['data'];
                $response = $flwave->formatChargeCardResponse($response);

                $this->transaction->update([
                    'flutterwave_ref' => $data['id'],
                ]);
                $this->cardDetails['flw_ref'] = $data['flw_ref'];

            }
            $details['errors'] = $response['message'] ?? "";

            if ($response['status']){
                $details = $response;
                //check if it's otp required;
                if ($details['flag'] === "otp_required"){
                    $this->isOtpRequired = true;
                    $this->isPinRequired = false;
                    $this->hideCardFields = true;
                }
            }
        } catch (Exception $e) {
            logger("An Error Occurred while trying to Authorize with PIN: \n {$e->getMessage()} \n {$e->getTraceAsString()} ");
            $details = ['status' => false, 'errors' => "Connection Lost."];
        }

        $this->dispatchBrowserEvent('cardPaymentProcessed', $details);

    }

    public function cardAuthorizationWithOtp()
    {
        $details = [];

        try {

            if ($this->cardProvider === "BLUSALT"){
                $otpVerified = (new Blusalt())->otpVerify($this->cc_Otp,$this->transaction->provider_ref['blusalt']);
                if (!$otpVerified){
                    $details['status'] = false;
                    $details['errors'] = "Could not Verify OTP, Possibly wrong OTP!";
                }

            }

            if (in_array($this->cardProvider,['FLUTTERWAVE','FLWAVEPERCENT','FLWAVEFLAT'])){
                $flwave = getFlwave(isset($this->merchantGateways['card']['flwave_percent']));
                $response = $flwave->validateTransaction($this->cc_Otp, $this->cardDetails['flw_ref']);
                $this->verifyFlwaveResponse($response);
                if (strtoupper($response['status']) === "SUCCESS"){
                    $details['status'] = true;
                }
            }


        } catch (Exception $e) {
            logger("An Error Occurred while trying to Authorize with OTP: \n {$e->getMessage()} \n {$e->getTraceAsString()} ");

            $details = ['status' => false, 'errors' => $e->getMessage()];
            $this->dispatchBrowserEvent('cardPaymentProcessed', $details);

        }
        $this->dispatchBrowserEvent('cardPaymentProcessed', $details);

    }

    public function cardAuthorizationWithAvs()
    {
        try {
            $flwave = getFlwave(isset($this->merchantGateways['card']['flwave_percent']));
            $response = $flwave->cardCharge($this->cardDetails);
            $this->verifyFlwaveResponse($response);
        } catch (Exception $e) {
            logger("An Error Occurred while trying to Authorize with AVS: \n {$e->getMessage()} \n {$e->getTraceAsString()} ");

            $details = ['status' => false, 'errors' => $e->getMessage()];
            $this->dispatchBrowserEvent('cardPaymentProcessed', $details);
        }
    }

    public function payWith($processor)
    {


        try {
            $this->setActiveTab(strtolower($processor));
            $flwave = getFlwave(isset($this->merchantGateways['card']['flwave_percent']));
            $tx_ref = $flwave->getTxRef();
            $amount = $this->merchantGateways[$this->activeTab]['invoiceTotal'];
            $payload = [
                "amount" => $amount,
                "currency" => "NGN",
                "email" => $this->invoice->customer_email,
                "fullname" => $this->invoice->name,
                "tx_ref" => $tx_ref
            ];
            $response = $flwave->{"charge" . $processor}($payload);
            $details = [];
            if ($response) {
                $details['message'] = $response['message'];
                $details['errors'] = $response['message'];
                $details['flag'] = "payment_failed";
                $details['status'] = isset($response['data']['meta']['authorization']['redirect']);
                if ($details['status']) {
                    $details['flag'] = "redirect_required";
                    $details['url'] = $response['data']['meta']['authorization']['redirect'];
                }
            }
            logger("Payment for charge$processor Response is : ", $response);
            // {"status":"error","message":"Merchant is not enabled for ApplePay collections.","data":null}


        } catch (Exception $e) {
            logger("An Error Occurred while trying to charge$processor Payment : \n {$e->getMessage()} \n {$e->getTraceAsString()} ");
            $details = ['status' => false, 'errors' => $e->getMessage()];
        }


        $this->dispatchBrowserEvent('cardPaymentProcessed', $details);


    }


    /**
     * @param $response
     * @param Transaction $transaction
     * @param $wallet
     * @param User $user
     * @param User $company
     */
    public function verifyFlwaveResponse($response): void
    {
        /**
         * @var User $user
         * @var User $company
         * @var Wallet $wallet
         * @var Transaction $transaction
         **/
        $company = User::firstWhere('email', config('app.company_email'));
        $user = $this->user;
        $wallet = $user->wallet;
        $transaction = $this->transaction;
        info("Response for " . $this->cardDetails['flw_ref'] . json_encode($response, JSON_THROW_ON_ERROR));

        $details = ['status' => true, 'flag' => 'processing'];
        if (isset($response['data'])) {
            if (strtoupper($response['data']['status']) === "SUCCESSFUL") {

                $details = ['status' => true, 'flag' => "payment_completed"];

                //Payment Successful;
                $payment_provider_message = $response['data']['flw_ref'] . " " . $response['data']['processor_response'];
                $params = array_merge($response['data']['customer'], [
                    "narration" => $response['data']['narration'],
                    "tx_ref" => $response['data']['tx_ref'],
                    "id" => $response['data']['id'],
                    "ip" => $response['data']['ip'],
                    "payment_type" => $response['data']['payment_type']
                ]);

                $transaction->handleSuccessfulPayment($transaction, $this->merchantGateways[$this->activeTab]['gateway_id'], $payment_provider_message, $params, $wallet, $user, $company);


            }
            if (strtoupper($response['data']['status']) === "FAILED") {
                //Payment UNSuccessful;
                $details = ['status' => false, 'flag' => "payment_failed"];
            }

        }


        $this->dispatchBrowserEvent('paymentCompleted', $details);
    }

    public function getRemitaServiceTypeId(Transaction $transaction)
    {
        $user = $transaction->user;
        $serviceId = config('remita.service_type_id');

        $type = strtolower(str_replace(" ", "", $transaction->type));
        if ($user->id === 3) {
            if (str_contains($type, "tuition") || str_contains($type, "school")) {
                $serviceId = "10298195252";
            }

            if (str_contains($type, "application")) {
                $serviceId = "1972701988";
            }

            if (str_contains($type, "undergraduatetranscript")) {
                $serviceId = "744536409";
            }

            if (str_contains($type, "acceptance")) {
                $serviceId = "1971104380";
            }

        }
        return $serviceId;

    }

    /**
     * @return array
     */
    public function getCardDetails(): array
    {
        $expiry = explode("/", $this->cardDetails['cc_expiration']);
        [$expiry_month, $expiry_year] = $expiry;
        $cardNo = str_replace(' ', '', $this->cardDetails['card_number']);
        $cvv = $this->cardDetails['cvv'];
        $pin = $this->cc_Pin ?? null;
        $customer_email = $this->invoice->customer_email;
        $invoiceTotal = $this->merchantGateways[$this->activeTab]['invoiceTotal'];
        $this->transaction = $this->invoice->transaction;
        return array($expiry_month, $expiry_year, $cardNo, $cvv, $pin, $customer_email, $invoiceTotal);
    }
}
