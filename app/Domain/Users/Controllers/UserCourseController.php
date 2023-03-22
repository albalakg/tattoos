<?php

namespace App\Domain\Users\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Users\Services\UserService;
use App\Domain\Content\Services\CourseService;
use App\Domain\Users\Services\UserCourseService;
use App\Domain\Users\Requests\CreateUserCourseRequest;
use App\Domain\Users\Requests\DeleteUserCourseRequest;
use App\Domain\Users\Requests\UpdateTestStatusRequest;
use App\Domain\Users\Requests\CreateTestCommentRequest;

class UserCourseController extends Controller
{  
  private UserCourseService $user_course_service;
  
  public function __construct()
  {
    $this->user_course_service = new UserCourseService(
      new CourseService,
      new UserService,
    );
  }
  
  public function getAllTests()
  {
    try {
      $response = $this->user_course_service->getAllTests();
      return $this->successResponse('Tests fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  } 
  
  public function updateTestStatus(UpdateTestStatusRequest $request)
  {
    try {
      $response = $this->user_course_service->updateTestStatus($request->id, $request->status, Auth::user()->id);
      return $this->successResponse('Tests fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  } 
  
  public function createTestComment(CreateTestCommentRequest $request)
  {
    try {
      $response = $this->user_course_service->createComment($request->user_course_submission_id, $request->comment, Auth::user()->id);
      return $this->successResponse('Tests fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  } 
  
  public function getAll()
  {
    try {
      $response = $this->user_course_service->getAll();
      return $this->successResponse('Users Courses fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  } 
  
  public function getUserCourseProgress($user_course_id)
  {
    try {
      $response = $this->user_course_service->getUserCourseProgress($user_course_id);
      return $this->successResponse('User Course progress fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  } 
  
  public function create(CreateUserCourseRequest $request)
  {
    try {
      $response = $this->user_course_service->createByAdmin($request, Auth::user()->id);
      return $this->successResponse('User Course created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  } 
  
  public function delete(DeleteUserCourseRequest $request)
  {
    try {
      $response = $this->user_course_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('User Course created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  } 
}