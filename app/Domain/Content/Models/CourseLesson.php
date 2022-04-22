<?php

namespace App\Domain\Content\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\CourseArea;
use App\Domain\Users\Models\UserCourseLesson;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domain\Content\Models\CourseLessonTag;
use App\Domain\Content\Models\CourseLessonRank;
use App\Domain\Content\Models\CourseLessonComment;

class CourseLesson extends Model
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
    
    public function progress()
    {
        return $this->hasOne(UserCourseLesson::class, 'course_lesson_id', 'id')
                    ->where('user_id', Auth::user()->id)
                    ->select('course_lesson_id', 'progress');
    }
    
    public function area()
    {
        return $this->hasOne(CourseArea::class, 'id', 'course_area_id');
    }
    
    public function video()
    {
        return $this->hasOne(Video::class, 'id', 'video_id')
                    ->select('id', 'video_path');
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