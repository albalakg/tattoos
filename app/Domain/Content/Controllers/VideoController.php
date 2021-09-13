<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Services\VideoService;
use App\Domain\Content\Requests\CreateVideoRequest;

class VideoController extends Controller
{
  public function __construct()
  {
    $this->service = new VideoService;
  }

  public function create(CreateVideoRequest $request, VideoService $video_service)
  {
    try {
      $response = $video_service->createVideo((object) $request->validated(), Auth::video()->id);
      return $this->successResponse('Video created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function update(UpdateVideoRequest $request, VideoService $video_service)
  {
    try {
      $response = $video_service->updateVideo((object) $request->validated(), Auth::video()->id);
      return $this->successResponse('Video updated successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function delete(DeleteVideosRequest $request, VideoService $video_service)
  {
    try {
      $response = $video_service->deleteVideos($request->ids, Auth::video()->id);
      return $this->successResponse('Videos deleted successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function getAll(VideoService $video_service)
  {
    try {
      $response = $video_service->getAll();
      return $this->successResponse('Videos fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }
}