<?php

namespace App\Domain\Content\Models;

use Illuminate\Support\Facades\Auth;
use App\Domain\Helpers\StatusService;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\CourseArea;
use App\Domain\Users\Models\UserCourseLesson;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domain\Content\Models\CourseLessonTag;
use App\Domain\Content\Models\CourseLessonRank;
use App\Domain\Content\Models\CourseLessonTerm;
use App\Domain\Content\Models\CourseLessonSkill;
use App\Domain\Content\Models\CourseLessonComment;
use App\Domain\Content\Models\CourseScheduleLesson;
use App\Domain\Content\Models\CourseLessonEquipment;

class CourseLesson extends Model
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
                    ->select('id', 'video_path', 'video_length');
    }
    
    public function guestVideo()
    {
        return $this->hasOne(Video::class, 'id', 'video_id')
                    ->select('id', 'video_length', 'video_path');
    }
    
    public function schedule()
    {
        return $this->hasOne(CourseScheduleLesson::class, 'course_lesson_id', 'id')
                    ->select('course_lesson_id', 'course_id', 'date');
    }

    public function ranks()
    {
        return $this->hasMany(CourseLessonRank::class, 'course_lesson_id', 'id');
    }

    public function terms()
    {
        return $this->hasMany(CourseLessonTerm::class, 'course_lesson_id', 'id')
                    ->join('terms', 'terms.id', 'course_lesson_terms.term_id')
                    ->where('terms.status', StatusService::ACTIVE)
                    ->select('terms.id', 'terms.name', 'terms.description', 'course_lesson_id');
    }

    public function skills()
    {
        return $this->hasMany(CourseLessonSkill::class, 'course_lesson_id', 'id')
                    ->join('skills', 'skills.id', 'course_lesson_skills.skill_id')
                    ->where('skills.status', StatusService::ACTIVE)
                    ->select('skills.id', 'skills.name', 'skills.description', 'course_lesson_id');
    }

    public function equipment()
    {
        return $this->hasMany(CourseLessonEquipment::class, 'course_lesson_id', 'id')
                    ->join('equipment', 'equipment.id', 'course_lesson_equipment.equipment_id')
                    ->where('equipment.status', StatusService::ACTIVE)
                    ->select('equipment.id', 'equipment.name', 'equipment.description', 'course_lesson_id');
    }

    public function trainingOptions()
    {
        return $this->hasMany(CourseLessonTrainingOption::class, 'course_lesson_id', 'id')
                    ->join('training_options', 'training_options.id', 'course_lesson_training_options.training_option_id')
                    ->select('training_options.id', 'training_options.name', 'course_lesson_training_options.value', 'course_lesson_id');
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