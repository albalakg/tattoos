<?php

namespace App\Domain\Content\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use SoftDeletes;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    protected $appends = ['expiredAt'];

    public function getExpiredAtAttribute()
    {
        return Carbon::parse($this->expired_at)->diffForHumans;
    }

    public function video()
    {
        return $this->hasOne(Video::class, 'id', 'video_id');
    }
}