<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\UserCourse;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Users\Models\UserCourseSubmissionComment;

class UserCourseSubmission extends Model
{
  public function userCourse()
  {
    return $this->hasOne(UserCourse::class, 'id', 'user_course_id');
  }

  public function comments()
  {
    return $this->hasMany(UserCourseSubmissionComment::class, 'user_course_submission_id', 'id');
  }
}