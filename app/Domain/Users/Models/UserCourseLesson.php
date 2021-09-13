<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\UserCourse;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\CourseLesson;

class UserCourseLesson extends Model
{
  public function userCourse()
  {
    return $this->hasOne(UserCourse::class, 'id', 'user_course_id');
  }

  public function lesson()
  {
    return $this->hasOne(CourseLesson::class, 'id', 'course_lesson_id');
  }
}