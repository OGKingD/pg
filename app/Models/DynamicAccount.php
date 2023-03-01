<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DynamicAccount extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class,'invoice_no','invoice_no');

    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class,'invoice_no','invoice_no');
    }
}
