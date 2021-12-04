<?php

namespace App\Domain\Payment\Models;

use Illuminate\Database\Eloquent\Model;

class LuSupplierType extends Model
{
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $guarded = [];
    
    const CONTENT_GUARD = 1;
    const PAYPAL        = 2;
}