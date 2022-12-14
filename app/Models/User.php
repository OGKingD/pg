<?php

namespace App\Models;

use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function gateways()
    {
        return $this->hasOne(UserGateway::class,'user_id');

    }


    public function addGateway()
    {
        //charge is amount to charge the gateway; charge_factor is if charge should be percentage (1) or flat (0) , status is 1/0 active/inactive
        //"payment_gateway_id" =>[ "charge" => 5 , "charge_factor" => , "status" => ]
        $payment_gateways = Gateway::select('name','id','status')->get();
        $config_details = [];
        foreach ($payment_gateways as $gateway){
            $config_details[$gateway->id] = [
                "status" => $gateway->status,
                "charge" => 0,
                "charge_factor" => 0,
                "name" => $gateway->name,
            ];
        }

        return $this->gateways()->updateOrCreate(['user_id' => $this->id],['config_details' => $config_details]);

    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function addWallet()
    {
        return $this->wallet()->updateOrCreate(['user_id' => $this->id], ['id'=> Str::orderedUuid(),'currency' => 'NGN', 'balance' => 0]);

    }

    public function getNameAttribute()
    {
        return $this->first_name. " ".$this->last_name;

    }

    public function userType()
    {
        return $this->hasOne(UserTypes::class,'id', 'type')->select('id','name');

    }

    public function otp()
    {
        return $this->hasOne(Otp::class);
    }

    public function generateOtp()
    {
        return $this->otp()->updateOrCreate(['user_id'=> $this->id],['otp' => Otp::generate(), 'updated_at' => now()]);

    }

    public function send2fa()
    {
        $otp = $this->generateOtp();
        $message = "Your secure verification code is <br> <br>  <b class='  text-primary text-gradient text-bolder display-2'>{$otp->otp}</b> <br> <br> <b class='text-danger'>THIS CODE EXPIRES IN 15MINS!</b>";
        $extras = ["url" => "/#", "buttonMessage" => "{$otp->otp}"];
        send_email($this->email, $this->getNameAttribute(), "Verification Code", $message, $extras,"info" );

    }

    public function isAdmin()
    {
        return $this->userType->id === 1;

    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail($this));

    }

    public function invoice()
    {
        return $this->hasMany(Invoice::class);
    }


    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }
}
