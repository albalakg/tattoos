<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domain\Content\Models\CourseLessonSkill;

class Skill extends Model
{
    use SoftDeletes;

    const REQUIRES_TYPE = 0;
    const LEARNS_TYPE   = 1;
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    protected $appends = ['imageSrc'];

    public function getImageSrcAttribute()
    {
        return config('app.url') . '/' . 'files/' . $this->image;  
    }

    public function lessons()
    {
        return $this->hasMany(CourseLessonSkill::class, 'term_id', 'id')
                    ->with('lesson');
    }
}