<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\CourseService;
use App\Domain\Content\Services\CourseCategoryService;
use App\Domain\Content\Requests\CreateCourseCategoryRequest;
use App\Domain\Content\Requests\UpdateCourseCategoryRequest;

class CourseCategoryController extends Controller
{
 
  /**
   * @var CourseCategoryService
  */
  private $course_category_service;
  
  public function __construct()
  {
    $this->course_category_service = new CourseCategoryService(
      new CourseService
    );
  }

  public function getAll()
  {
    try {
      $response = $this->course_category_service->getAll();
      return $this->successResponse('Course Categories fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
  
  public function getActive()
  {
    try {
      $response = $this->course_category_service->getActive();
      return $this->successResponse('Course Categories fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateCourseCategoryRequest $request)
  {
    try {
      $response = $this->course_category_service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Course Category created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function update(UpdateCourseCategoryRequest $request)
  {
    try {
      $response = $this->course_category_service->update($request->validated(), Auth::user()->id);
      return $this->successResponse('Course Category updated', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

public function delete(DeleteRequest $request)
  {
    try {
      $response = $this->course_category_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Course Categories deleted', $response);
    } catch (Exception $ex) {
      return $this->errorResponse(
        $ex,
        $this->course_category_service->error_data
      );
    }
  }
}