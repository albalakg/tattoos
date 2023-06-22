<?php

namespace App\Domain\Orders\Models;

use App\Domain\Orders\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingToken extends Model
{
    use SoftDeletes;

    const TOKEN_LENGTH = 50;
    
    protected $appends = ['link'];

    public function getLinkAttribute()
    {
        return config('app.client_url') . '/orders?';  
    }   

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(Order::class, 'marketing_token_id', 'id');
    }
}