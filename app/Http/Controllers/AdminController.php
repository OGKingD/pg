<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\Wallet;
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

        $wallet = $db->table('wallets');
        /** @var object $walletQuery  */
        $walletQuery = $wallet->selectRaw("SUM(balance) as total_wallet_balance")
            ->selectRaw("COUNT(wallets.id) as wallet_count")->first();

        //set Result for Wallet
        $data['wallet_count'] = $walletQuery->wallet_count;
        $data['total_wallet_balance'] = $walletQuery->total_wallet_balance;


        $user = $db->table('users');
        /** @var object $userQuery */
        $userQuery = $user->selectRaw("COUNT( CASE WHEN type = 5 THEN true END) as total_merchants")
        ->selectRaw("COUNT( CASE WHEN status = 0 and type = 5 THEN true END) as total_active_merchants")
        ->selectRaw("COUNT( CASE WHEN status = 1 and type = 5 THEN true END) as total_inactive_merchants")->first();

        //set Result for Users
        $data['total_merchants'] = $userQuery->total_merchants;
        $data['total_active_merchants'] = $userQuery->total_active_merchants;
        $data['total_inactive_merchants'] = $userQuery->total_inactive_merchants;


//        $builder = $db->table('transactions');
//
//        $transactionsQuery = $builder
//            ->selectRaw("COUNT(transactions.id) AS transactions_count")
//            ->selectRaw("COUNT(CASE WHEN status = 'successful' THEN 1 end) as paid_bills")
//            ->selectRaw("COUNT(CASE WHEN status = 'pending' THEN 1 end) as pending_bills")
//            ->selectRaw("COUNT(CASE WHEN status = 'failed' THEN 1 end) as failed_bills")
//            ->selectRaw("SUM(total) as transactions_total")
//            ->selectRaw("SUM( CASE WHEN flag = 'credit' AND status = 'successful' THEN total END) as total_credit")
//            ->selectRaw("SUM( CASE WHEN type = 'Withdrawal' AND status = 'successful' THEN total END) as withdrawal")
//            ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'successful' THEN total END) as total_debit")
//            ->selectRaw("SUM( CASE WHEN status = 'successful' THEN total END) as total_paid")
////            ->selectRaw("SUM( CASE WHEN flag = 'credit' AND type = 'Commission' AND payment_method = 'Commission' THEN total END) as all_commissions")
//            ->selectRaw("SUM( CASE WHEN type = 'Commission Transfer'  THEN total END) as all_commissions_paid_out");
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

//        return view('admin.admin_home',[
//            "paidBills" => $result->paid_bills,
//            "pendingBills" => $result->pending_bills,
//            "failedBills" => $result->failed_bills,
//            "totalRevenue" => number_format($result->transactions_total),
//            "totalPaid" => number_format($result->total_paid),
//            'users_count' => $usersCount,
//            'currencies_count' => Currency::count(),
//            'transactions_count' => $result->transactions_count,
//            'api_clients_count' => PersonalAccessToken::count(),
//            'wallets_count' => $walletCount,
//            "totalWalletBalance" => $total_wallet_balance,
//            'totalDebits' => $result->total_debit,
//            'totalCredits' => $result->total_credit,
//            "withdrawals" => number_format($result->withdrawal),
//            "userBalance" =>  property_exists($user->wallets,'balance')? $user->wallets->balance:0,
//            "year" => $year,
//            "urlExtra" => $urlExtra,
//        ]);

        return view('admin.admin_dashboard', $data);

    }




}
