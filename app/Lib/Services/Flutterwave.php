<?php


namespace App\Lib\Services;




use Illuminate\Support\Facades\Http;
use Laravel\Flutterwave\EventHandler;
use Laravel\Flutterwave\Rave;

class Flutterwave extends Rave
{

    public function cardCharge($array)
    {
        $this->setType('card');
        if (!isset($array['tx_ref']) || empty($array['tx_ref'])) {
            $array['tx_ref'] = $this->getTxRef();
        } else {
            $this->setTxRef($array['tx_ref']);
        }

        //set the payment handler
        $this->eventHandler(new EventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/charges?type=".$this->getType());

        //returns the value from the results
        return $this->chargePayment($array);
    }

    public function chargeGooglePay($payload)
    {
        $url = config('flutterwave.google_pay_url');

        return $this->callEndpoint($url,"POST",$payload);

    }
    public function chargeApplePay($payload)
    {
        $url = config('flutterwave.apple_pay_url');

        return $this->callEndpoint($url,"POST",$payload);

    }

    public function callEndpoint($url,$httpVerb,$payload)
    {
        return Http::withHeaders([
            'Authorization' => config('flutterwave.secret_key'),
            'content-type' => 'application/json'])->{strtolower($httpVerb)}($url, $payload)->json();

    }


}
