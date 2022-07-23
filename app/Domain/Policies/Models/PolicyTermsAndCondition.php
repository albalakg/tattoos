<?php

namespace App\Domain\Policies\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyTermsAndCondition extends Model
{
    protected $table = 'policies_terms_and_conditions';
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
}