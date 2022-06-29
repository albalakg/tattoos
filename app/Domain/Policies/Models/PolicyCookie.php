<?php

namespace App\Domain\Policies\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyCookie extends Model
{
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    protected $table = 'policies_cookies';
}