<?php

namespace App\Domain\Support\Models;

use App\Domain\Users\Models\User;
use App\Domain\Support\Models\OrderLog;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Support\Models\SupportTicketLog;
use App\Domain\Support\Models\SupportTicketMessage;

class SupportTicket extends Model
{
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    public function messages()
    {
        return $this->hasMany(SupportTicketMessage::class, 'support_ticket_id', 'id')
                    ->orderBy('id', 'desc');
    }
    
    public function logs()
    {
        return $this->hasMany(SupportTicketLog::class, 'support_ticket_id', 'id')
                    ->orderBy('id', 'desc');
    }
    
    public function category()
    {
        return $this->hasOne(SupportCategory::class, 'id', 'support_category_id')
                    ->select('id', 'name', 'description');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}