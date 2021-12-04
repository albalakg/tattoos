<?php

namespace App\Domain\Payment\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $guarded = [];
    
}