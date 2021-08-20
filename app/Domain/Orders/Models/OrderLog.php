<?php

namespace App\Domain\Tags\Models;

use App\Domain\Tags\Models\Order;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}