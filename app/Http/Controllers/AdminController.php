<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class AdminController extends Controller
{


    public function adminlogin(){
        $title['title'] = "Administrator Login";
        return view('admin.admin_login', $title);
    }


    public function submitadminlogin(LoginRequest $request){

        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $data['title'] = "Administrative Dashboard";

        $db = DB::connection();
        $year = !empty((request('year'))) ? (request('year')) : "Today";
        $data['greeting']  = " Good Evening!🌝 ";


        if ((int)Carbon::now()->format('G') < 12) {
            $data['greeting']  = "👋 ️ Good Morning!⛅ ";
        }
        if ((int)Carbon::now()->format('G') >= 12 and (int)Carbon::now()->format('G') < 17) {
            $data['greeting']  = "👋  Good Afternoon!☀️ ";
        }
        $data['greeting'] .= auth()->user()->first_name;

        $wallet = $db->table('wallets');
        /** @var object $walletQuery  */
        $walletQuery = $wallet
            ->selectRaw("SUM(balance) as total_wallet_balance")
            ->selectRaw("COUNT(wallets.id) as wallet_count")->first();

        //set Result for Wallet
        $data['wallet_count'] = $walletQuery->wallet_count;
        $data['total_wallet_balance'] = $walletQuery->total_wallet_balance;


        $user = $db->table('users');
        /** @var object $userQuery */
        $userQuery = $user
            ->selectRaw("COUNT( CASE WHEN type = 5 THEN true END) as total_merchants")
            ->selectRaw("COUNT( CASE WHEN status = 0 and type = 5 THEN true END) as total_active_merchants")
            ->selectRaw("COUNT( CASE WHEN status = 1 and type = 5 THEN true END) as total_inactive_merchants")->first();

        //set Result for Users
        $data['total_merchants'] = $userQuery->total_merchants;
        $data['total_active_merchants'] = $userQuery->total_active_merchants;
        $data['total_inactive_merchants'] = $userQuery->total_inactive_merchants;
        $data['total_api_merchants'] = PersonalAccessToken::count();


        /** @var Builder $transactions */
        $transactions = $db->table('transactions');


        $transactionsQuery = $transactions
            //count transactions issued out for people to pay
            ->selectRaw("COUNT( CASE WHEN flag = 'debit' THEN 1 END) as total_bills_generated")
            //sum transactions issued out for people to pay
            ->selectRaw("SUM( CASE WHEN flag = 'debit' THEN total END) as total_expected_revenue")
            //count SUCCESSFUL TRANSACTIONS
            ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  status = 'successful' THEN total END) as successful_transactions")
            //transactions issued out and people have paid
            ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'successful' THEN total END) as successful_transactions_total")
            //COUNT PENDING TRANSACTIONS
            ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  status = 'pending' THEN total END) as pending_transactions")
            //sum pending transactions
            ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'pending' THEN total END) as pending_transactions_total")
            //COUNT FAILED TRANSACTIONS
            ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  status = 'failed' THEN total END) as failed_transactions")
            //sum FAILED transactions
            ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'failed' THEN total END) as failed_transactions_total")

            //sum of all the fees generated;
            ->selectRaw("SUM( CASE WHEN flag = 'credit' AND  transaction_ref LIKE 'fee_credit_%' THEN total END) as total_fees_charge")
            //count of all the fees;
            ->selectRaw("COUNT( CASE WHEN flag = 'credit' AND  transaction_ref LIKE 'fee_credit_%' THEN total END) as total_fees_count")

            //GATEWAYS SECTION;

            //card gateways
            ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  gateway_id = 1 THEN total END) as total_card_transactions_count")
            ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  gateway_id = 1 THEN total END) as total_card_transactions_amount")
            ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  status = 'successful' AND gateway_id = 1 THEN total END) as successful_card_transactions")
            ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'successful' AND gateway_id = 1 THEN total END) as successful_card_transactions_total")
            ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  status = 'pending' AND gateway_id = 1 THEN total END) as pending_card_transactions")
            ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'pending' AND gateway_id = 1 THEN total END) as pending_card_transactions_total")

            //bank transfer gateways
            ->selectRaw("COUNT( CASE WHEN flag = 'debit'  AND gateway_id = 2 THEN total END) as total_bank_transfer_transactions_count")
            ->selectRaw("SUM( CASE WHEN flag = 'debit'  AND gateway_id = 2 THEN total END) as total_bank_transfer_transactions_amount")
            ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  status = 'successful' AND gateway_id = 2 THEN total END) as successful_bank_transfer_transactions")
            ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'successful' AND gateway_id = 2 THEN total END) as successful_bank_transfer_transactions_total")
            ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  status = 'pending' AND gateway_id = 2 THEN total END) as pending_bank_transfer_transactions")
            ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'pending' AND gateway_id = 2 THEN total END) as pending_bank_transfer_transactions_total")

            //Remita gateways
            ->selectRaw("COUNT( CASE WHEN flag = 'debit'  AND gateway_id = 3 THEN total END) as total_remita_transactions_count")
            ->selectRaw("SUM( CASE WHEN flag = 'debit'  AND gateway_id = 3 THEN total END) as total_remita_transactions_amount")
            ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  status = 'successful' AND gateway_id = 3 THEN total END) as successful_remita_transactions")
            ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'successful' AND gateway_id = 3 THEN total END) as successful_remita_transactions_total")
            ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  status = 'pending' AND gateway_id = 3 THEN total END) as pending_remita_transactions")
            ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'pending' AND gateway_id = 3 THEN total END) as pending_remita_transactions_total")

            //GooglePay gateways
            ->selectRaw("COUNT( CASE WHEN flag = 'debit'  AND gateway_id = 4 THEN total END) as total_google_pay_transactions_count")
            ->selectRaw("SUM( CASE WHEN flag = 'debit'  AND gateway_id = 4 THEN total END) as total_google_pay_transactions_amount")
            ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  status = 'successful' AND gateway_id = 4 THEN total END) as successful_google_pay_transactions")
            ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'successful' AND gateway_id = 4 THEN total END) as successful_google_pay_transactions_total")
            ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  status = 'pending' AND gateway_id = 4 THEN total END) as pending_google_pay_transactions")
            ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'pending' AND gateway_id = 4 THEN total END) as pending_google_pay_transactions_total")

            //ApplePay gateways
            ->selectRaw("COUNT( CASE WHEN flag = 'debit'  AND gateway_id = 5 THEN total END) as total_apple_pay_transactions_count")
            ->selectRaw("SUM( CASE WHEN flag = 'debit'  AND gateway_id = 5 THEN total END) as total_apple_pay_transactions_amount")
            ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  status = 'successful' AND gateway_id = 5 THEN total END) as successful_apple_pay_transactions")
            ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'successful' AND gateway_id = 5 THEN total END) as successful_apple_pay_transactions_total")
            ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  status = 'pending' AND gateway_id = 5 THEN total END) as pending_apple_pay_transactions")
            ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'pending' AND gateway_id = 5 THEN total END) as pending_apple_pay_transactions_total")


            ->selectRaw("SUM( CASE WHEN type = 'Withdrawal' AND status = 'successful' THEN total END) as withdrawal")
            ->first();

        $data['latest_transactions'] = $transactions->select(['merchant_transaction_ref','updated_at','total','flag'])->latest()->limit(10)->get();

        //set Result for Transactions;
        $data['transactions_count'] = $transactionsQuery->total_bills_generated;
        $data['transactions_expected_revenue'] = $transactionsQuery->total_expected_revenue;
        $data['successful_transactions'] = $transactionsQuery->successful_transactions_total;
        $data['successful_transactions_count'] = $transactionsQuery->successful_transactions;
        $data['pending_transactions'] = $transactionsQuery->pending_transactions_total;
        $data['pending_transactions_count'] = $transactionsQuery->pending_transactions;
        $data['failed_transactions'] = $transactionsQuery->failed_transactions_total;
        $data['failed_transactions_count'] = $transactionsQuery->failed_transactions;
        $data['total_fees_charge'] = $transactionsQuery->total_fees_charge;
        $data['total_fees_charge_count'] = $transactionsQuery->total_fees_count;

        $data['card_transactions_count'] = $transactionsQuery->total_card_transactions_count;
        $data['card_transactions_total'] = $transactionsQuery->total_card_transactions_amount;
        $data['successful_card_transactions_total'] = $transactionsQuery->successful_card_transactions_total;
        $data['successful_card_transactions_count'] = $transactionsQuery->successful_card_transactions;
        $data['pending_card_transactions_total'] = $transactionsQuery->pending_card_transactions_total;
        $data['pending_card_transactions_count'] = $transactionsQuery->pending_card_transactions;
        $data['failed_card_transactions_total'] = $data['card_transactions_total'] - ($data['successful_card_transactions_total'] + $data['pending_card_transactions_total']);
        $data['failed_card_transactions_count'] = $data['card_transactions_count'] - ($data['successful_card_transactions_count'] + $data['pending_card_transactions_count']);

        $data['bank_transfer_transactions_count'] = $transactionsQuery->total_bank_transfer_transactions_count;
        $data['bank_transfer_transactions_total'] = $transactionsQuery->total_bank_transfer_transactions_amount;
        $data['successful_bank_transfer_transactions_total'] = $transactionsQuery->successful_bank_transfer_transactions_total;
        $data['successful_bank_transfer_transactions_count'] = $transactionsQuery->successful_bank_transfer_transactions;
        $data['pending_bank_transfer_transactions_total'] = $transactionsQuery->pending_bank_transfer_transactions_total;
        $data['pending_bank_transfer_transactions_count'] = $transactionsQuery->pending_bank_transfer_transactions;
        $data['failed_bank_transfer_transactions_total'] = $data['bank_transfer_transactions_total'] - ($data['successful_bank_transfer_transactions_total'] + $data['pending_bank_transfer_transactions_total']);
        $data['failed_bank_transfer_transactions_count'] = $data['bank_transfer_transactions_count'] - ($data['successful_bank_transfer_transactions_count'] + $data['pending_bank_transfer_transactions_count']);

        $data['remita_transactions_count'] = $transactionsQuery->total_remita_transactions_count;
        $data['remita_transactions_total'] = $transactionsQuery->total_remita_transactions_amount;
        $data['successful_remita_transactions_total'] = $transactionsQuery->successful_remita_transactions_total;
        $data['successful_remita_transactions_count'] = $transactionsQuery->successful_remita_transactions;
        $data['pending_remita_transactions_total'] = $transactionsQuery->pending_remita_transactions_total;
        $data['pending_remita_transactions_count'] = $transactionsQuery->pending_remita_transactions;
        $data['failed_remita_transactions_total'] = $data['remita_transactions_total'] - ($data['successful_remita_transactions_total'] + $data['pending_remita_transactions_total']);
        $data['failed_remita_transactions_count'] = $data['remita_transactions_count'] - ($data['successful_remita_transactions_count'] + $data['pending_remita_transactions_count']);

        $data['google_pay_transactions_count'] = $transactionsQuery->total_google_pay_transactions_count;
        $data['google_pay_transactions_total'] = $transactionsQuery->total_google_pay_transactions_amount;
        $data['successful_google_pay_transactions_total'] = $transactionsQuery->successful_google_pay_transactions_total;
        $data['successful_google_pay_transactions_count'] = $transactionsQuery->successful_google_pay_transactions;
        $data['pending_google_pay_transactions_total'] = $transactionsQuery->pending_google_pay_transactions_total;
        $data['pending_google_pay_transactions_count'] = $transactionsQuery->pending_google_pay_transactions;
        $data['failed_google_pay_transactions_total'] = $data['google_pay_transactions_total'] - ($data['successful_google_pay_transactions_total'] + $data['pending_google_pay_transactions_total']);
        $data['failed_google_pay_transactions_count'] = $data['google_pay_transactions_count'] - ($data['successful_google_pay_transactions_count'] + $data['pending_google_pay_transactions_count']);

        $data['apple_pay_transactions_count'] = $transactionsQuery->total_apple_pay_transactions_count;
        $data['apple_pay_transactions_total'] = $transactionsQuery->total_apple_pay_transactions_amount;
        $data['successful_apple_pay_transactions_total'] = $transactionsQuery->successful_apple_pay_transactions_total;
        $data['successful_apple_pay_transactions_count'] = $transactionsQuery->successful_apple_pay_transactions;
        $data['pending_apple_pay_transactions_total'] = $transactionsQuery->pending_apple_pay_transactions_total;
        $data['pending_apple_pay_transactions_count'] = $transactionsQuery->pending_apple_pay_transactions;
        $data['failed_apple_pay_transactions_total'] = $data['apple_pay_transactions_total'] - ($data['successful_apple_pay_transactions_total'] + $data['pending_apple_pay_transactions_total']);
        $data['failed_apple_pay_transactions_count'] = $data['apple_pay_transactions_count'] - ($data['successful_apple_pay_transactions_count'] + $data['pending_apple_pay_transactions_count']);


//
//        $created_at = \request()->created_at;
//        $end_date = \request()->end_date;
//        if ($year === "month"){
//            //created_at=2021-06-13&end_date=
//            $result = $transactionsQuery->whereMonth('transactions.created_at', date('m'))->first();
//            $urlExtra = "&created_at=".date("Y-m-01")."&end_date=".date("Y-m-31");
//
//        }elseif($year === "year"){
//            $result = $transactionsQuery->whereYear('transactions.created_at', date('Y'))->first();
//            $urlExtra = "&created_at=".date("Y-01-01")."&end_date=".date("Y-12-31");
//
//        }elseif((isset($created_at) and !empty($created_at) ) and (isset($end_date) and !empty($end_date))) {
//            $result = $transactionsQuery->whereBetween("transactions.created_at", [$created_at, $end_date . " 23:59:59.999",])->first();
//            $urlExtra = "&created_at=" . $created_at . "&end_date=" . $end_date;
//            $year = $created_at . " - " . $end_date;
//        }elseif(isset($created_at) and !empty($created_at) ) {
//            $result = $transactionsQuery->whereDate("transactions.created_at", $created_at)->first();
//            $urlExtra = "&created_at=" . $created_at ;
//            $year = $created_at . " - " . date("Y-m-j");
//
//        }elseif(isset($end_date) and !empty($end_date) ) {
//            $result = $transactionsQuery->whereDate("transactions.created_at", $end_date)->first();
//            $urlExtra = "&end_date=" . $end_date ;
//            $year = $end_date ;
//
//        }else{
//            $result = $transactionsQuery->whereDate('transactions.created_at',date('Y-m-j'))->first();
//            $urlExtra = "&created_at=".date("Y-m-j")."&end_date=".date("Y-m-j");
//
//        }

        return view('admin.admin_dashboard', $data);

    }




}
