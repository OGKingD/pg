<?php

namespace App\Models;


class UserSettings extends Model
{
    protected $casts = ['values' => 'json'];
    public function user()
    {
        return $this->belongsTo(User::class);

    }
}
