<?php

namespace App\Domain\Tags\Models;

use App\Domain\Users\Models\User;
use App\Domain\Tags\Models\OrderLog;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Tags\Models\SupportTicketMessage;

class SupportTicket extends Model
{
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