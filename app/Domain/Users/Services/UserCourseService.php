<?php

namespace App\Domain\Users\Services;

use Exception;
use Illuminate\Pagination\Paginator;
use App\Domain\Users\Models\UserCourseSubmission;

class UserCourseService
{
  public function __construct()
  {
   
  }
  
  /**
   * @return Paginator
  */
  public function getAllTests(): Paginator
  {
    return UserCourseSubmission::query()
            ->with('metaData', 'comments')
            ->select(
              'id',
              'user_course_id',
              'video',
              'status',
              'comment',
              'created_at',
              'updated_at'
            )
            ->orderBy('created_at', 'desc')
            ->simplePaginate(1000);
  }
}