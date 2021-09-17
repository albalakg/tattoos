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
      $response = $video_service->createVideo((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('Video created successfully', $response);
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