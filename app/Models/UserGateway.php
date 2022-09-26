<?php


namespace App\Models;


class UserGateway extends Model
{
    protected $table = "user_gateways";
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at'];
    protected $casts = ['config_details' => 'json'];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
