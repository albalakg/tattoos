<?php

namespace App\Domain\Orders\Models;

use App\Domain\Users\Models\User;
use App\Domain\Orders\Models\OrderLog;
use Illuminate\Database\Eloquent\Model;
use App\Domain\General\Models\LuContentType;

class PolicyTermsAndCondition extends Model
{
    protected $table = 'policies_terms_and_conditions';
}