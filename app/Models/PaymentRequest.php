<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentRequest extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = ['details' => 'json'];
}
