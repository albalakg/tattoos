<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trainer extends Model
{
    use SoftDeletes;
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $appends = ['image'];

    public function getImageAttribute()
    {
        return config('app.url') . '/' . 'files/images/' . $this->image_path;  
    }

    public function courseAreas()
    {
        return $this->hasOne(CourseArea::class, 'trainer_id', 'id');
    }
}