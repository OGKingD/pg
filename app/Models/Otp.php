<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Otp extends Model
{
    protected $guarded = [''];
    protected $table = "otps";
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class );

    }


    public static function generate()
    {
        return Str::random(6);

    }
}
