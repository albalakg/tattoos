<?php

namespace App\Domain\Tags\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Support\Models\SupportTicket;

class SupportTicketMessage extends Model
{
    public function ticket()
    {
        return $this->hasOne(SupportTicket::class, 'id', 'support_ticket_id');
    }
    
    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}