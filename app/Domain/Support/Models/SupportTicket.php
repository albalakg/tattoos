<?php

namespace App\Domain\Support\Models;

use App\Domain\Users\Models\User;
use App\Domain\Support\Models\OrderLog;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Support\Models\SupportTicketMessage;

class SupportTicket extends Model
{
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    public function messages()
    {
        return $this->hasMany(SupportTicketMessage::class, 'support_ticket_id', 'id');
    }
    
    public function category()
    {
        return $this->hasOne(OrderLog::class, 'id', 'support_category_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}