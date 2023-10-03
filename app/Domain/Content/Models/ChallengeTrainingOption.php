<?php

namespace App\Domain\Content\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTrainingOption extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    public function getExpiredAtAttribute()
    {
        return Carbon::parse($this->expired_at)->diffForHumans;
    }
    
    public function challenge()
    {
        return $this->hasOne(Challenge::class, 'id', 'challenge_id');
    }
    
    public function trainingOption()
    {
        return $this->hasOne(TrainingOption::class, 'id', 'training_option_id');
    }
}