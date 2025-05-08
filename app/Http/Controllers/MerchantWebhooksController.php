<?php

namespace App\Http\Controllers;

use App\Jobs\PushtoWebhookJob;
use App\Models\Transaction;
use Illuminate\Http\Request;

class MerchantWebhooksController extends Controller
{
    public function trigger($merchantRef)
    {
        $transaction = Transaction::firstWhere('merchant_transaction_ref', $merchantRef);
        $message = "Transaction does not exist, webhook not triggered";
        if ($transaction){
            PushtoWebhookJob::dispatch($transaction)->delay(now());
            $message = "Webhook triggered successfully";
        }
        return $message;


    }
    public function index()
    {

    }

    public function create()
    {
    }

    public function store(Request $request)
    {
    }
}
