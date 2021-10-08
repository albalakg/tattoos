<?php

namespace App\Domain\Orders\Models;

use App\Domain\Orders\Models\Order;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}