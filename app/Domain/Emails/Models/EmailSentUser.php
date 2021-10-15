<?php

namespace App\Domain\Tags\Models;

use App\Domain\Users\Models\User;
use App\Domain\Tags\Models\EmailsSent;
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