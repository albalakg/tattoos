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
      return $this->successResponse('Course Categories fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }
  
  public function create(CreateCourseCategoryRequest $request)
  {
    try {
      $response = $this->course_category_service->create((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('Course Category created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function update(UpdateCourseCategoryRequest $request)
  {
    try {
      $response = $this->course_category_service->update((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('Course Category updated successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

public function delete(DeleteRequest $request)
  {
    try {
      $response = $this->course_category_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Course Categories deleted successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse(
        $ex->getMessage(),
        $this->course_category_service->error_data
      );
    }
  }
}