<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Services\CourseLessonService;
use App\Domain\Content\Requests\CreateVideoRequest;

class CourseLessonController extends Controller
{
  public function getAll(CourseLessonService $lesson_service)
  {
    try {
      $response = $lesson_service->getAll();
      return $this->successResponse('Videos fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function create(CreateCourseLessonRequest $request, CourseLessonService $lesson_service)
  {
    try {
      $response = $lesson_service->create((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('Video created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function update(UpdateVideoRequest $request, CourseLessonService $lesson_service)
  {
    try {
      $response = $lesson_service->update((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('Video updated successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

public function delete(DeleteVideosRequest $request)
  {
    try {
      $lesson_service = new CourseLessonService();
      $response = $lesson_service->delete($request->ids, Auth::user()->id);
      return $this->successResponse('Video deleted successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }
}