<?php

namespace App\Domain\Tags\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Tags\Models\SupportTicket;

class SupportCategory extends Model
{
    public function tickets()
    {
        return $this->hasMany(SupportTicket::class, 'support_ticket_id', 'id');
    }
}