<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    protected $guarded = [];
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Runs a callback function before attempting to debit the user's wallet
     * @param $amount
     * @param callable $callback
     * @param null $before
     * @param null $after
     * @param bool $allow_negative_balance
     * @return boolean|mixed
     * @throws Exception
     * @throws \Throwable
     */
    public function debit($amount, callable $callback = null, &$before = null, &$after = null, $allow_negative_balance = false){
        if(!$allow_negative_balance){
            $this->ensureAmountGreaterThanZero($amount);
        }
        return DB::transaction(function() use($amount, $callback, &$before, &$after, $allow_negative_balance){
            /** @var Connection $connection */
            /** @var Wallet $wallet */
            //$user_id = $this->id;
            $wallet = $this->fresh()->where('id', $this->id)->lockForUpdate()->first();
            if(!$allow_negative_balance && $wallet->balance < $amount) {
                throw new Exception("Insufficient balance");
            }

            $result = is_callable($callback) ? $callback() : null;
            $before = $wallet->balance;
            $newBalance = $wallet->balance-$amount;
            $wallet->balance = $newBalance;
            if($wallet->update()){
                $after = $newBalance;
                return $result?:true;
            }

            return false;

        });
    }

    /**
     * @param $amount
     * @param callable|null $callback
     * @param null $before
     * @param null $after
     * @return mixed
     * @throws Exception
     * @throws \Throwable
     */

    public function credit($amount, callable $callback = null, &$before = null, &$after = null){
        $this->ensureAmountGreaterThanZero($amount);
        //var_dump($amount); exit;
        return DB::transaction(function() use($amount, $callback, &$before, &$after){
            /** @var Connection $connection */
            /** @var Wallet $wallet */
            $wallet = $this->fresh()->where('id' ,$this->id)->lockForUpdate()->first();

            $result = is_callable($callback) ? $callback() : null;
            $before = $wallet->balance;
            $newBalance = $wallet->balance+$amount;
            $wallet->balance = $newBalance;

            if($wallet->update()){
                $after = $newBalance;
                return $result?:true;
            }

            return false;
        });
    }

    /**
     * @param $amount
     * @throws Exception
     */
    private function ensureAmountGreaterThanZero($amount){
        if($amount <= 0){
            throw new Exception("Amount must be greater than 0");
        }
    }

    public function getWalletById($uuid)
    {
        return $this->Where("id",$uuid)->get();

    }

    public function getWalletByOtherFields($field)
    {
        $user = User::where("email",$field)->orWhere('phone_number',$field)->get()->first();

        return isset($user)? $user->wallets : "No Result";
    }

    public function scopeCriteria($query,$criteria)
    {
        //user_id	bigint unsigned
        //currency	varchar(3)
        //balance	decimal(10,2) [0.00]
        //monnify_account_reference	varchar(255) NULL
        //monnify_reservation_reference	varchar(255) NULL
        //bank_name	varchar(255) NULL
        //account_name	varchar(255) NULL
        //account_number	varchar(255) NULL
        //deleted_at	timestamp NULL
        //created_at
        $queryArray = [];
        if (array_key_exists('currency', $criteria) && !empty($criteria['currency']) ) {
            $queryArray[] = ['currency', '=', (string)($criteria['currency'])];
        }
        if (array_key_exists('balance', $criteria) && !empty($criteria['balance'])) {
            $queryArray[] = ['balance', '=', (int)($criteria['balance'])];
        }
        if (array_key_exists('monnify_account_reference', $criteria) && !empty($criteria['monnify_account_reference'])) {
            $queryArray[]= ['monnify_account_reference', 'like', "%".$criteria['monnify_account_reference']."%"];
        }
        if (array_key_exists('account_name', $criteria) && !empty($criteria['account_name'])) {
            $queryArray[]= ['account_name', 'like', "%".$criteria['account_name']."%"];
        }
        if (array_key_exists('bank_name', $criteria) && !empty($criteria['bank_name'])) {
            $queryArray[]= ['bank_name', 'like', "%".$criteria['bank_name']."%"];
        }
        if (array_key_exists('account_number', $criteria)&& !empty($criteria['account_number'])) {
            $queryArray[]= ['account_number', '=', (string)($criteria['account_number'])];
        }
        if (array_key_exists('created_at', $criteria)&& !empty($criteria['created_at'])) {
            $queryArray[] = ['created_at', '=', $criteria['created_at']];
        }
        if (array_key_exists('user_id', $criteria)&& !empty($criteria['user_id'])) {
            $queryArray[] = ['user_id', '=', $criteria['user_id']];
        }

        if (array_key_exists('email', $criteria)&& !empty($criteria['email'])) {
            $user = User::where("email",$criteria['email'])->first();
            $queryArray[] = isset($user) ? ['user_id', '=', $user->id] : ['user_id', '=', 0];
        }

        $query->where($queryArray)->orderBy('user_id','desc');



    }

}
