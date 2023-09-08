<?php

namespace App\Http\Controllers;

use App\Models\Gateway;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;

class CashAtBankController extends Controller
{
    public function index()
    {

    }

    public function store(Request $request)
    {
        $transaction = Transaction::firstWhere('transaction_ref', $request->transaction);

        $request->validate([
            "transaction" => ["bail", "required", "min:6",
                function ($attribute, $value, $fail) use ($transaction) {

                    if (is_null($transaction)) {
                        return $fail("The $attribute is invalid!");
                    }
                    if ( in_array($transaction->status,["successful","failed"]) ) {
                        return $fail("The $attribute cannot be processed, Duplicate Transaction");

                    }
                }
            ]
        ]);

        try {
            $status = false;
            $data = [];

            $response = $transaction->invoice->statusOnUI();
            if (!$response['status']){
                $data = [
                    "message" => "This invoice $transaction->invoice_no  is not available for payment, Kindly generate another record!"
                ];
            }

            if ($response['status']) {
                //when transaction is pending mark payment as successful and change channel;
                $gateway = $this->getGateway();
                $gateway_id = $gateway->id;
                $transactionChargeAndTotal = $transaction->computeChargeAndTotal($gateway_id);
                /** @var User $user */
                $user = $transaction->user;
                /** @var Wallet $wallet */
                $wallet = $user->wallet;
                $company = company();
                $transaction->handleSuccessfulPayment($transaction, $gateway->id, '', [], $wallet, $user, $company);
                $transaction->update([
                    "total" => $transactionChargeAndTotal['total'],
                    "fee" => $transactionChargeAndTotal['charge'],
                    'provider' => "UI MicroFinance"

                ]);
                $status = true;
                $data = $transaction->transactionToPayload();
            }

            return response()->json([
                "status" => $status,
                "data"  => $data
            ]);

        } catch (\Exception $e) {

            info("Error Happened while handling successful payment for CashAtBank: \n", ['cause' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return [
                "status" => false,
                "data"  => $transaction->transactionToPayload()
            ];

        }

    }

    public function show($id)
    {
        $transactionToPayload = Transaction::firstWhere('merchant_transaction_ref', $id);
        $status = false;

        if (isset($transactionToPayload)){
            $gateway = $this->getGateway();
            $gateway_id = $gateway->id;
            $transactionChargeAndTotal = $transactionToPayload->computeChargeAndTotal($gateway_id);
            $transactionToPayload->fee = $transactionChargeAndTotal['charge'];
            $transactionToPayload->total = $transactionChargeAndTotal['total'];
            $status = true;
            $transactionToPayload = $transactionToPayload->transactionToPayload();
        }

        return [
            "status" => $status,
            "data" => $transactionToPayload
        ];
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }

    /**
     * @return Gateway
     */
    public function getGateway(): Gateway
    {
        return Gateway::firstWhere('name', "CashAtBank");
    }
}
