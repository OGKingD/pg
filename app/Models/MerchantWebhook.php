<?php

namespace App\Models;


class MerchantWebhook extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);

    }
}
