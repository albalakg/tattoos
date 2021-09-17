<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Services\CourseService;
use App\Domain\Content\Requests\CreateCourseRequest;

class CourseController extends Controller
{
  public function __construct()
  {
    $this->service = new CourseService;
  }

  public function create(CreateCourseRequest $request, CourseService $course_service)
  {
    try {
      $response = $course_service->createCourse((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('Course created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function getAll(CourseService $course_service)
  {
    try {
      $response = $course_service->getAll();
      return $this->successResponse('Courses fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }
}