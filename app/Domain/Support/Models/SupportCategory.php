<?php

namespace App\Domain\Support\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Support\Models\SupportTicket;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportCategory extends Model
{
    use SoftDeletes;

    protected $guarded = [];
        
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    public function tickets()
    {
        return $this->hasMany(SupportTicket::class, 'support_ticket_id', 'id');
    }
}