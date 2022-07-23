<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserCourse;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Users\Models\UserCourseSubmission;

class UserCourseSubmissionComment extends Model
{
  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
  ];

  public $timestamps = false;

  public function userCourseSubmission()
  {
    return $this->hasOne(UserCourseSubmission::class, 'id', 'user_course_submission_id');
  }

  public function checker()
  {
    return $this->hasOne(User::class, 'id', 'created_by');
  }

  public function user()
  {
    return $this->hasOne(UserCourseSubmission::class, 'id', 'user_course_submission_id')
                ->join('user_courses', 'user_courses.id', 'user_course_id')
                ->join('users', 'users.id', 'user_courses.user_id')
                ->join('user_details', 'user_details.user_id', 'user_courses.user_id')
                ->select(
                  'user_course_submissions.id',
                  'user_courses.user_id',
                  'users.email',
                  'user_details.first_name',
                  'user_details.last_name',
                );
  }
}