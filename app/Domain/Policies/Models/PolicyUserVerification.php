<?php

namespace App\Domain\Orders\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyUserVerification extends Model
{
    protected $table = 'policies_users_verifications';

    public $timestamps = false;

    public function tnc()
    {
        return $this->hasOne(PolicyTermsAndCondition::class, 'id', 'tnc_id');
    }
}