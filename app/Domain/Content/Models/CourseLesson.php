<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\CourseArea;
use App\Domain\Content\Models\CourseLessonTag;
use App\Domain\Content\Models\CourseLessonRank;
use App\Domain\Content\Models\CourseLessonComment;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseLesson extends Model
{
    use SoftDeletes;
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    public function area()
    {
        return $this->hasOne(CourseArea::class, 'id', 'course_area_id');
    }

    public function ranks()
    {
        return $this->hasMany(CourseLessonRank::class, 'course_lesson_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany(CourseLessonTag::class, 'course_lesson_tag_id', 'id')
                    ->with('tag');
    }

    public function comments()
    {
        return $this->hasMany(CourseLessonComment::class, 'course_lesson_tag_id', 'id');
    }
}