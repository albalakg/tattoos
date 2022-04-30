<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\UserCourse;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\CourseLesson;

class UserCourseLesson extends Model
{
  const LESSON_COMPLETED = 100;
  
  protected $casts = [
    'created_at'  => 'datetime:Y-m-d H:i:s',
  ];

  protected $guarded = [];

  public $timestamps = false;

  public function userCourse()
  {
    return $this->hasOne(UserCourse::class, 'id', 'user_course_id');
  }

  public function lesson()
  {
    return $this->hasOne(CourseLesson::class, 'id', 'course_lesson_id');
  }

  public function updates()
  {
    return $this->hasMany(UserCourseLessonWatch::class, 'user_course_lesson_id', 'id');
  }

  public function lastUpdate()
  {
    return $this->hasOne(UserCourseLessonWatch::class, 'user_course_lesson_id', 'id')
                ->orderBy('id', 'desc');
  }

  public function isCompleted()
  {
    return $this->progress === self::LESSON_COMPLETED;
  }
}