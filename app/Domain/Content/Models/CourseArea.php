<?php

namespace App\Domain\Content\Models;

use App\Domain\Content\Models\Course;
use App\Domain\Helpers\StatusService;
use App\Domain\Content\Models\Trainer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseArea extends Model
{
    use SoftDeletes;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    protected $appends = ['imageSrc', 'trailerSrc'];

    public function getImageSrcAttribute()
    {
        return config('app.url') . '/' . 'files/' . $this->image;  
    }

    public function getTrailerSrcAttribute()
    {
        return config('app.url') . '/' . 'files/' . $this->trailer;  
    }

    public function course()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }

    public function trainer()
    {
        return $this->hasOne(Trainer::class, 'id', 'trainer_id')
                    ->select('id', 'name', 'title', 'image', 'description');
    }
    
    public function category()
    {
        return $this->hasOne(Course::class, 'id', 'course_id')
                    ->join('course_categories', 'course_categories.id', 'courses.category_id')
                    ->select('courses.id', 'course_categories.name');
    }
    
    public function lessons()
    {
        return $this->hasMany(CourseLesson::class, 'course_area_id', 'id');
    }
    
    public function guestActiveLessons()
    {
        return $this->hasMany(CourseLesson::class, 'course_area_id', 'id')
                    ->where('status', StatusService::ACTIVE)
                    ->with('guestVideo')
                    ->select('id', 'course_id', 'course_area_id', 'video_id', 'name', 'content', 'image');
    }
    
    public function activeLessons()
    {
        return $this->hasMany(CourseLesson::class, 'course_area_id', 'id')
                    ->where('status', StatusService::ACTIVE)
                    ->with('video', 'terms', 'skills', 'equipment', 'trainingOptions')
                    ->select('id', 'course_id', 'course_area_id', 'video_id', 'view_order', 'name', 'description', 'content', 'image');
    }
    
    public function inactiveLessons()
    {
        return $this->hasMany(CourseLesson::class, 'course_area_id', 'id')
                    ->where('status', StatusService::INACTIVE);
    }
    
    public function comingSoonLessons()
    {
        return $this->hasMany(CourseLesson::class, 'course_area_id', 'id')
                    ->where('status', StatusService::PENDING);
    }
}