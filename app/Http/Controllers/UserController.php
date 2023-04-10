<?php

namespace App\Http\Controllers;


use App\Models\Gateway;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{

    public function index()
    {
        $data['title'] = 'Clients';
        return view('admin.users', $data);

    }

    public function dashboard()
    {
        //check if user is an admin or normal user
        /** @var User $user */
        $user = auth()->user();
        $data['title'] = "Dashboard";
        $data = $this->dashboardData($user);
        if (!is_null($user)) {
            if ($user->isAdmin()) {
                //return to admin dashboard;
                return view('admin.admin_dashboard', $data);

            }
        }
        return view('dashboard', $data);
    }

    /**
     * @param User $user
     * @return array
     */
    public function dashboardData(User $user): array
    {


        /** @var MySqlConnection $db */
        $db = DB::connection();

        $data['title'] = "Merchant Dashboard";

        $data['greeting'] = " Good Evening!🌝 ";
        $data['time_of_day'] = "Night";


        if ((int)Carbon::now()->format('G') < 12) {
            $data['greeting'] = "👋 ️ Good Morning!⛅ ";
        }
        if ((int)Carbon::now()->format('G') >= 12 and (int)Carbon::now()->format('G') < 17) {
            $data['greeting'] = "👋  Good Afternoon!☀️ ";
        }
        $data['greeting'] .= $user->first_name;

        $transactionsTable = $db->table('transactions');

        $data['gateways'] = Gateway::select(['name','id'])->get();

        $transactionsQuery = $transactionsTable
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
            ->selectRaw("SUM( CASE WHEN flag = 'credit' AND  transaction_ref LIKE 'fee_credit_%' AND status = 'successful' THEN total END) as total_successful_fees_charge")
            //count of all the fees;
            ->selectRaw("COUNT( CASE WHEN flag = 'credit' AND  transaction_ref LIKE 'fee_credit_%' THEN total END) as total_fees_count");

        //GATEWAYS SECTION;
        foreach ($data['gateways'] as $gateway) {
            $channel = strtolower(str_replace(" ", "_", $gateway->name));
            $transactionsQuery
                ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  gateway_id = $gateway->id THEN total END) as total_{$channel}_transactions_count")
                ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  gateway_id = $gateway->id THEN total END) as total_{$channel}_transactions_amount")
                ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  status = 'successful' AND gateway_id = $gateway->id THEN total END) as successful_{$channel}_transactions")
                ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'successful' AND gateway_id = $gateway->id THEN total END) as successful_{$channel}_transactions_total")
                ->selectRaw("COUNT( CASE WHEN flag = 'debit' AND  status = 'pending' AND gateway_id = $gateway->id THEN total END) as pending_{$channel}_transactions")
                ->selectRaw("SUM( CASE WHEN flag = 'debit' AND  status = 'pending' AND gateway_id = $gateway->id THEN total END) as pending_{$channel}_transactions_total");
        }

        $transactionsQuery->selectRaw("SUM( CASE WHEN type = 'Withdrawal' AND status = 'successful' THEN total END) as withdrawal");

        if (!$user->isAdmin()) {
            $transactionsQuery = $transactionsQuery
                ->where("user_id", $user->id)->first();
        }


        if ($user->isAdmin()) {
            $data['title'] = "Administrative Dashboard";

            $walletTable = $db->table('wallets');
            /** @var object $walletQuery */
            $walletQuery = $walletTable
                ->selectRaw("SUM(balance) as total_wallet_balance")
                ->selectRaw("COUNT(wallets.id) as wallet_count")->first();

            //set Result for Wallet
            $data['wallet_count'] = $walletQuery->wallet_count;
            $data['total_wallet_balance'] = $walletQuery->total_wallet_balance;

            $userTable = $db->table('users');
            /** @var object $userQuery */
            $userQuery = $userTable
                ->selectRaw("COUNT( CASE WHEN type = 5 THEN true END) as total_merchants")
                ->selectRaw("COUNT( CASE WHEN status = 0 and type = 5 THEN true END) as total_active_merchants")
                ->selectRaw("COUNT( CASE WHEN status = 1 and type = 5 THEN true END) as total_inactive_merchants")->first();

            //set Result for Users
            $data['total_merchants'] = $userQuery->total_merchants;
            $data['total_active_merchants'] = $userQuery->total_active_merchants;
            $data['total_inactive_merchants'] = $userQuery->total_inactive_merchants;
            $data['total_api_merchants'] = PersonalAccessToken::count();

            $transactionsQuery = $transactionsQuery->first();

        }


        $data['latest_transactions'] = $transactionsTable->select(['merchant_transaction_ref', 'updated_at', 'total', 'flag'])->latest()->limit(10)->get();

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
        $data['total_successful_fees_charge'] = $transactionsQuery->total_successful_fees_charge;
        $data['total_fees_charge_count'] = $transactionsQuery->total_fees_count;


        //GATEWAYS SECTION;
        foreach ($data['gateways'] as $gateway) {
            $channel = strtolower(str_replace(" ", "_", $gateway->name));
            $data[$channel.'_transactions_count'] = $transactionsQuery->{"total_".$channel."_transactions_count"};
            $data[$channel.'_transactions_total'] = $transactionsQuery->{"total_".$channel."_transactions_amount"};
            $data['successful_'.$channel.'_transactions_total'] = $transactionsQuery->{"successful_".$channel."_transactions_total"};
            $data['successful_'.$channel.'_transactions_count'] = $transactionsQuery->{"successful_".$channel."_transactions"};
            $data['pending_'.$channel.'_transactions_total'] = $transactionsQuery->{"pending_".$channel."_transactions_total"};
            $data['pending_'.$channel.'_transactions_count'] = $transactionsQuery->{"pending_".$channel."_transactions"};
            $data['failed_'.$channel.'_transactions_total'] = $data[$channel.'_transactions_total'] - ($data['successful_'.$channel.'_transactions_total'] + $data['pending_'.$channel.'_transactions_total']);
            $data['failed_'.$channel.'_transactions_count'] = $data[$channel.'_transactions_count'] - ($data['successful_'.$channel.'_transactions_count'] + $data['pending_'.$channel.'_transactions_count']);

        }

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

        return $data;

    }

    //
}
