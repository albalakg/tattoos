<?php

namespace App\Domain\Users\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Users\Models\UserCourseLesson;

class UserCourseLessonWatch extends Model
{
  protected $casts = [
    'created_at'  => 'datetime:Y-m-d H:i:s',
  ];

  public $timestamps = false;

  protected $guarded = [];

  public function userCourse()
  {
    return $this->hasOne(UserCourseLesson::class, 'id', 'user_course_lesson_id')
                ->select('id', 'course_lesson_id');
  }
}