<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

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

    public function gateways()
    {
        return $this->user->usergateway;

    }
}
