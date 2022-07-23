<?php

namespace App\Domain\Orders\Models;

use App\Domain\Users\Models\User;
use App\Domain\Orders\Models\OrderLog;
use Illuminate\Database\Eloquent\Model;
use App\Domain\General\Models\LuContentType;
use App\Domain\Orders\Models\MarketingToken;

class Order extends Model
{
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $guarded = [];
    
    public function logs()
    {
        return $this->hasMany(OrderLog::class, 'order_id', 'id');
    }

    public function marketingToken()
    {
        return $this->hasOne(MarketingToken::class, 'id', 'marketing_token_id');
    }
    
    public function contentType()
    {
        return $this->hasOne(LuContentType::class, 'id', 'content_type_id');
    }
}