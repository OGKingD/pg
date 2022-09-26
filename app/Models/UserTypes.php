<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserTypes extends Model
{
    use HasFactory;
    protected $table = 'user_types';

    public function User()
    {
        return $this->hasOne(User::class);

    }
}
