<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\CourseAreaService;
use App\Domain\Content\Services\CourseLessonService;
use App\Domain\Content\Requests\CreateCourseAreaRequest;
use App\Domain\Content\Requests\UpdateCourseAreasRequest;

class CourseAreaController extends Controller
{
  public function getAll(CourseAreaService $course_area_service)
  {
    try {
      $response = $course_area_service->getAll();
      return $this->successResponse('Course Areas fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function create(CreateCourseAreaRequest $request, CourseAreaService $course_area_service)
  {
    try {
      $response = $course_area_service->create((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('Course Areas created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function update(UpdateCourseAreasRequest $request, CourseAreaService $course_area_service)
  {
    try {
      $response = $course_area_service->update((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('Course Areas updated successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

public function delete(DeleteRequest $request)
  {
    try {
      $course_area_service = new CourseAreaService(new CourseLessonService);
      $response = $course_area_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Course Areas deleted successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }
}