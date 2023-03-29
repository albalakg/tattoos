<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Services\VideoService;
use App\Domain\Content\Requests\Video\CreateVideoRequest;
use App\Domain\Content\Requests\Video\UpdateVideoRequest;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\CourseLessonService;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
  /**
   * @var VideoService
  */
  private $video_service;
  
  public function __construct()
  {
    $this->video_service = new VideoService;
  }

  public function getAll()
  {
    try {
      $response = $this->video_service->getAll();
      return $this->successResponse('Videos fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateVideoRequest $request)
  {
    try {
      $response = $this->video_service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Video created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function update(UpdateVideoRequest $request)
  {
    try {
      $response = $this->video_service->update($request->validated(), Auth::user()->id);
      return $this->successResponse('Video updated', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

public function delete(DeleteRequest $request)
  {
    try {
      $video_service = new VideoService(new CourseLessonService);
      $response = $video_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Videos deleted', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}