<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserCourse;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Users\Models\UserCourseSubmission;

class UserCourseSubmissionComment extends Model
{
  public function userCourseSubmission()
  {
    return $this->hasOne(UserCourseSubmission::class, 'id', 'user_course_submission_id');
  }

  public function checker()
  {
    return $this->hasOne(User::class, 'id', 'created_by');
  }
}