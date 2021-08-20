<?php

namespace App\Domain\Tags\Models;

use App\Domain\Tags\Models\EmailsSent;
use Illuminate\Database\Eloquent\Model;

class LuEmailType extends Model
{
    public function emails()
    {
        return $this->hasMany(EmailsSent::class, 'email_type_id', 'id');
    }
}