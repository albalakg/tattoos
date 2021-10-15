<?php

namespace App\Domain\Content\Controllers;

use App\Domain\Content\Models\CourseArea;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Services\CourseService;
use App\Domain\Content\Requests\CreateCourseRequest;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Requests\UpdateCourseRequest;
use App\Domain\Content\Services\CourseAreaService;

class CourseController extends Controller
{  
  /**
   * @var CourseService
  */
  private $course_service;
  
  public function __construct()
  {
    $this->course_service = new CourseService(
      new CourseAreaService()
    );
  }

  public function create(CreateCourseRequest $request)
  {
    try {
      $response = $this->course_service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Course created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function update(UpdateCourseRequest $request)
  {
    try {
      $response = $this->course_service->update($request->validated(), Auth::user()->id);
      return $this->successResponse('Course updated successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function delete(DeleteRequest $request)
  {
    try {
      $response = $this->course_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Courses deleted successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse(
        $ex->getMessage(),
        $this->course_service->error_data
      );
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