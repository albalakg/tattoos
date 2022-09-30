<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domain\Content\Models\CourseLessonSkill;

class Skill extends Model
{
    use SoftDeletes;
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    protected $appends = ['skillSrc'];

    public function getSkillSrcAttribute()
    {
        return config('app.url') . '/' . 'files/' . $this->video_path;  
    }

    public function lessons()
    {
        return $this->hasMany(CourseLessonSkill::class, 'term_id', 'id')
                    ->with('lesson');
    }
}