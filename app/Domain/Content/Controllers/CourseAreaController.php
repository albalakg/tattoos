<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\CourseAreaService;
use App\Domain\Content\Requests\OrderContentRequest;
use App\Domain\Content\Services\CourseLessonService;
use App\Domain\Content\Requests\CreateCourseAreaRequest;
use App\Domain\Content\Requests\UpdateCourseAreasRequest;

class CourseAreaController extends Controller
{
  /**
   * @var CourseAreaService
  */
  private $course_area_service;
  
  public function __construct()
  {
    $this->course_area_service = new CourseAreaService(
      new CourseLessonService
    );
  }

  public function getAll()
  {
    try {
      $response = $this->course_area_service->getAll();
      return $this->successResponse('Course Areas fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateCourseAreaRequest $request)
  {
    try {
      $response = $this->course_area_service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Course Area created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function update(UpdateCourseAreasRequest $request)
  {
    try {
      $response = $this->course_area_service->update($request->validated(), Auth::user()->id);
      return $this->successResponse('Course Area updated', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

public function delete(DeleteRequest $request)
  {
    try {
      $response = $this->course_area_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Course Areas deleted', $response);
    } catch (Exception $ex) {
      return $this->errorResponse(
        $ex,
      );
    }
  }
  
  public function order(OrderContentRequest $request)
  {
    try {
      $response = $this->course_area_service->updateOrder($request->content);
      return $this->successResponse('Course Areas order updated successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}