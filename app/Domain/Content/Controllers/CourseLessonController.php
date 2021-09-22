<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\CourseAreaService;
use App\Domain\Content\Services\CourseLessonService;
use App\Domain\Content\Requests\CreateCourseLessonRequest;
use App\Domain\Content\Requests\UpdateCourseLessonRequest;

class CourseLessonController extends Controller
{  
  /**
   * @var CourseLessonService
  */
  private $lesson_service;
  
  public function __construct()
  {
    $this->lesson_service = new CourseLessonService(
      new CourseAreaService
    );
  }

  public function getAll()
  {
    try {
      $response = $this->lesson_service->getAll();
      return $this->successResponse('Lessons fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function create(CreateCourseLessonRequest $request)
  {
    try {
      $response = $this->lesson_service->create((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('Lesson created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function update(UpdateCourseLessonRequest $request)
  {
    try {
      $response = $this->lesson_service->update((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('Lesson updated successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

public function delete(DeleteRequest $request)
  {
    try {
      $response = $this->lesson_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Lessons deleted successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }
}