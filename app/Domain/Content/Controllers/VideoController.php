<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Services\VideoService;
use App\Domain\Content\Requests\CreateVideoRequest;
use App\Domain\Content\Requests\UpdateVideoRequest;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\CourseLessonService;

class VideoController extends Controller
{
  public function getAll(VideoService $video_service)
  {
    try {
      $response = $video_service->getAll();
      return $this->successResponse('Videos fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function create(CreateVideoRequest $request, VideoService $video_service)
  {
    try {
      $response = $video_service->create((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('Video created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function update(UpdateVideoRequest $request, VideoService $video_service)
  {
    try {
      $response = $video_service->update((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('Video updated successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

public function delete(DeleteRequest $request)
  {
    try {
      $video_service = new VideoService(new CourseLessonService);
      $response = $video_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Video deleted successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }
}