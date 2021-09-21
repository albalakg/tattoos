<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Services\CourseService;
use App\Domain\Content\Requests\CreateCourseRequest;

class CourseController extends Controller
{  
  /**
   * @var CourseService
  */
  private $course_service;
  
  public function __construct()
  {
    $this->course_service = new CourseService;
  }

  public function create(CreateCourseRequest $request)
  {
    try {
      $response = $this->course_service->create((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('Course created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function getAll()
  {
    try {
      $response = $this->course_service->getAll();
      return $this->successResponse('Courses fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }
}