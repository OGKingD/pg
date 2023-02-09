<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    protected $guarded = ['id'];


    public function transactions()
    {
        return $this->hasMany(Transaction::class);

    }

    public function invoice()
    {
        //Gateway = Transaction = Invoice;
        return  $this->hasManyThrough(Invoice::class,Transaction::class,'gateway_id','invoice_no','id','invoice_no');


    }

}
