<?php

namespace App\Domain\Support\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Support\Models\SupportTicket;

class SupportTicketMessage extends Model
{
    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function ticket()
    {
        return $this->hasOne(SupportTicket::class, 'id', 'support_ticket_id');
    }
    
    public function customer()
    {
        return $this->hasOne(SupportTicket::class, 'id', 'support_ticket_id')
                    ->join('users', 'users.id', 'support_tickets.user_id')
                    ->join('user_details', 'user_details.user_id', 'users.id')
                    ->select(
                        'support_tickets.id',
                        'users.id AS user_id',
                        'users.email',
                        'user_details.first_name',
                        'user_details.last_name',
                    );
    }
    
    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}