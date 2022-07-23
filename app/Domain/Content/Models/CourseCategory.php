<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseCategory extends Model
{
    use SoftDeletes;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    protected $appends = ['imageSrc'];

    public function getImageSrcAttribute()
    {
        return config('app.url') . '/' . 'files/' . $this->image;  
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'category_id', 'id');
    }
}