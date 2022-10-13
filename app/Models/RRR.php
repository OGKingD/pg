<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class RRR extends Model
{
    use HasFactory;
    protected $table = 'rrrs';
    protected $guarded = ['id'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
