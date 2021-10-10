<?php

namespace App\Domain\Support\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class SupportTicketLog extends Model
{
    public $timestamps = false;
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}