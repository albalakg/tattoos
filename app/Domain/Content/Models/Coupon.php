<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    const LIST_OF_TYPES     = [1, 2];
    const TYPE_PERCENTAGE   = 1;
    const TYPE_COINS        = 2;

    use SoftDeletes;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
}