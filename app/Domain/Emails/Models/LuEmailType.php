<?php

namespace App\Domain\Emails\Models;

use App\Domain\Emails\Models\EmailsSent;
use Illuminate\Database\Eloquent\Model;

class LuEmailType extends Model
{
    const SUPPORT_TICKET_MESSAGE_EMAIL  = 1;
    const SUPPORT_TICKET_EMAIL          = 2;
    const FORGOT_PASSWORD_EMAIL         = 3;

    public function emails()
    {
        return $this->hasMany(EmailsSent::class, 'email_type_id', 'id');
    }
}