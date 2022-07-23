<?php

namespace App\Domain\Policies\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyUserVerification extends Model
{
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    protected $table = 'policies_users_verifications';

    public $timestamps = false;

    public function tnc()
    {
        return $this->hasOne(PolicyTermsAndCondition::class, 'id', 'tnc_id');
    }
}