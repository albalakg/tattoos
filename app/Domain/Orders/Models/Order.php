<?php

namespace App\Domain\Orders\Models;

use App\Domain\Users\Models\User;
use App\Domain\Orders\Models\OrderLog;
use Illuminate\Database\Eloquent\Model;
use App\Domain\General\Models\LuContentType;

class Order extends Model
{
    protected $guarded = [];
    public function logs()
    {
        return $this->hasOne(OrderLog::class, 'order_id', 'id');
    }
    
    public function contentType()
    {
        return $this->hasOne(LuContentType::class, 'id', 'content_type_id');
    }
        
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')
                    ->join('user_details', 'user_details.user_id', 'users.id')
                    ->select(
                        'users.email',
                        'users.id',
                        'user_details.first_name',
                        'user_details.last_name',
                    );
    }
}