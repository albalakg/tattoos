<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use SoftDeletes;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    protected $appends = ['imageSrc'];

    public function getImageSrcAttribute()
    {
        return config('content.path') . '/' . $this->image;  
    }

    // protected $appends = ['timeTillExpiration'];

    // public function getTimeTillExpirationAttribute()
    // {
    //     return gettype($this->expired_at) === 'object' ? $this->expired_at->diffForHumans() : Carbon::parse($this->expired_at)->diffForHumans();
    // }

    public function video()
    {
        return $this->hasOne(Video::class, 'id', 'video_id');
    }

    public function trainingOptions()
    {
        return $this->hasMany(ChallengeTrainingOption::class, 'challenge_id', 'id')
                    ->join('training_options', 'training_options.id', 'challenge_training_options.training_option_id')
                    ->select('challenge_id', 'training_option_id', 'training_options.name', 'value');
    }
}