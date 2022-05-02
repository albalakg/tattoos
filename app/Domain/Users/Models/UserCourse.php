<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use App\Domain\Content\Models\Course;
use App\Domain\Content\Models\CourseLesson;
use App\Domain\Helpers\StatusService;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Users\Models\UserCourseLesson;

class UserCourse extends Model
{
  const DONE = 100;
  
  protected $casts = [
    'created_at'  => 'datetime:Y-m-d H:i:s',
    'end_at'      => 'datetime:Y-m-d H:i:s',
  ];

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }

  public function course()
  {
    return $this->hasOne(Course::class, 'id', 'course_id')
                ->select('id', 'name', 'description', 'view_order', 'image', 'trailer');
  }

  public function fullCourse()
  {
    return $this->hasOne(Course::class, 'id', 'course_id')
                ->with('activeAreasWithActiveLessons')
                ->select('id', 'name', 'description', 'view_order');
  }

  public function lessonsProgress()
  {
    return $this->hasMany(UserCourseLesson::class, 'user_course_id', 'id')
                ->select('id', 'user_course_id', 'course_lesson_id', 'progress', 'finished_at', 'progress');
  }

  public function finishedLessons()
  {
    return $this->hasMany(UserCourseLesson::class, 'user_course_id', 'id')
                ->where('status', StatusService::ACTIVE);
  }

  public function lessons()
  {
    return $this->hasMany(UserCourseLesson::class, 'user_course_id', 'id');
  }
}