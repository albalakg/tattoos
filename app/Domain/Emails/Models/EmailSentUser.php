<?php

namespace App\Domain\Emails\Models;

use App\Domain\Users\Models\User;
use App\Domain\Emails\Models\EmailsSent;
use Illuminate\Database\Eloquent\Model;

class EmailSentUser extends Model
{
    protected $guarded = [];
    
    public function email()
    {
        return $this->hasOne(EmailsSent::class, 'id', 'email_sent_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')
                    ->with('details');
    }
}