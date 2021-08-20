<?php

namespace App\Domain\Tags\Models;

use App\Domain\General\Models\LuContentType;
use App\Domain\Tags\Models\OrderLog;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function logs()
    {
        return $this->hasOne(OrderLog::class, 'order_id', 'id');
    }
    
    public function contentType()
    {
        return $this->hasOne(LuContentType::class, 'id', 'content_type_id');
    }
}