<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Model extends Eloquent
{
    use HasFactory;
    protected $dateFormat = "Y-m-d H:i:s.u";
    protected $guarded = ['id'];

}
