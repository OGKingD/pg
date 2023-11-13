<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function reportQuery($searchQuery)
    {
        $queryArray = [];
        $columns_to_select = [
            "invoice_no",
            'name',
            'customer_email',
            'quantity',
            'due_date',
            'amount',
            'user_id',
            'status',
            'created_at'];

        if (isset($searchQuery['invoice_no'])) {
            $queryArray[] = ['invoice_no', '=', (string)($searchQuery['invoice_no'])];
        }

        if (isset($searchQuery['name'])) {
            $queryArray[] = ['name', 'like', "%" . $searchQuery['name'] . "%"];
        }

        if (isset($searchQuery['customer_email'])) {
            $queryArray[] = ['customer_email', '=', ($searchQuery['customer_email'])];
        }

        if (isset($searchQuery['amount'])) {
            $queryArray[] = ['amount', '=', (string)($searchQuery['amount'])];
        }

        if (isset($searchQuery['status'])) {
            $queryArray[] = ['status', '=', (string)($searchQuery['status'])];
        }

        if (isset($searchQuery['user_id'])) {
            $queryArray[] = ['user_id', '=', (string)($searchQuery['user_id'])];
        }

        $builder = self::with('transaction')->select($columns_to_select)->where($queryArray);

        info("Invoices Report Query parameters is :", $queryArray);

        return queryWithDateRange($searchQuery, $builder);
    }

    public function user()
    {
        return $this->belongsTo(User::class);

    }

    public function rrr()
    {
        return $this->hasOne(RRR::class,'invoice_no','invoice_no');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class,'invoice_no','invoice_no');

    }

    public function gateway()
    {
        return  $this->hasOneThrough(Gateway::class,Transaction::class,'invoice_no','id','invoice_no','gateway_id');

    }

    public function dynamicAccount()
    {
        return $this->hasOne(DynamicAccount::class,'invoice_no','invoice_no');

    }

    public function statusOnUI()
    {
        //check if the invoice has expired on UI;
        $url = "https://pgcollegeui.com/payment/saana/payment_status.php";
        $data['type'] = $this->transaction->type;
        if (strtolower(str_replace(" ", "", $data['type'])) === "undergraduatetranscript"){
            $url = "http://academic.ui.edu.ng/payment/saana/payment_status.php";
        }
        if (strtolower(str_replace(" ", "", $data['type'])) === "cmd_applicationfee"){
            $url = "http://registration.cmdportals.com/payment/saana/payment_status.php";
            $data['type'] = str_replace("CMD_", "", $data['type']);
        }
        $data['invoiceno'] = $this->transaction->merchant_transaction_ref;
        return \Http::withoutVerifying()->get($url,$data)->json();

    }
}
