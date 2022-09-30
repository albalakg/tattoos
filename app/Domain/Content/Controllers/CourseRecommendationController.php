<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\CourseService;
use App\Domain\Content\Requests\UpdateCourseRequest;
use App\Domain\Content\Services\CourseRecommendationService;
use App\Domain\Content\Requests\CreateCourseRecommendationsRequest;

class CourseRecommendationController extends Controller
{  
  /**
   * @var CourseRecommendationService
  */
  private $course_recommendation_service;
  
  public function __construct()
  {
    $this->course_recommendation_service = new CourseRecommendationService(
      new CourseService()
    );
  }

  public function create(CreateCourseRecommendationsRequest $request)
  {
    try {
      $response = $this->course_recommendation_service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Course recommendations created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function update(UpdateCourseRequest $request)
  {
    try {
      $response = $this->course_recommendation_service->update($request->validated(), Auth::user()->id);
      return $this->successResponse('Course recommendation updated', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function delete(DeleteRequest $request)
  {
    try {
      $response = $this->course_recommendation_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Course recommendations deleted', $response);
    } catch (Exception $ex) {
      return $this->errorResponse(
        $ex,
      );
    }
  }

  public function getAll()
  {
    try {
      $response = $this->course_recommendation_service->getAll();
      return $this->successResponse('Course recommendations fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}