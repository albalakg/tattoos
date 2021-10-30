<?php

namespace App\Domain\Emails\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Emails\Models\EmailSentUser;

class EmailsSent extends Model
{
    public function type()
    {
        return $this->hasOne(LuEmailType::class, 'id', 'email_type_id');
    }

    public function receivers()
    {
        return $this->hasOne(EmailSentUser::class, 'id', 'user_id')
                    ->with('user');
    }
}