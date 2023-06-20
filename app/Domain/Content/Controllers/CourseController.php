<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\CourseService;
use App\Domain\Content\Services\CourseAreaService;
use App\Domain\Content\Requests\Course\CreateCourseRequest;
use App\Domain\Content\Requests\Course\UpdateCourseRequest;
use App\Domain\Content\Requests\Course\ScheduleCourseRequest;
use App\Domain\Users\Services\UserCourseService;

class CourseController extends Controller
{
  /**
   * @var CourseService
   */
  private $course_service;

  public function __construct()
  {
    $this->course_service = new CourseService(
      new CourseAreaService(),
      new UserCourseService()
    );
  }

  public function getGuestActiveCourses()
  {
    try {
      $response = $this->course_service->getGuestActiveCourses();
      return $this->successResponse('Course created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function getCourse($id)
  {
    try {
      $response = $this->course_service->getGuestOnlyCourse();
      // $response = $this->course_service->getGuestCourseById($id);
      return $this->successResponse('Course created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateCourseRequest $request)
  {
    try {
      $response = $this->course_service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Course created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function update(UpdateCourseRequest $request)
  {
    try {
      $response = $this->course_service->update($request->validated(), Auth::user()->id);
      return $this->successResponse('Course updated', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function delete(DeleteRequest $request)
  {
    try {
      $response = $this->course_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Courses deleted', $response);
    } catch (Exception $ex) {
      return $this->errorResponse(
        $ex,
      );
    }
  }

  public function schedule(ScheduleCourseRequest $request)
  {
    try {
      $response = $this->course_service->createSchedule($request->id, $request->lessonsId, Auth::user()->id);
      return $this->successResponse('Courses schedule created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function getAll()
  {
    try {
      $response = $this->course_service->getAll();
      return $this->successResponse('Courses fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}
