<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Services\TrainingOptionService;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\CourseLessonService;
use App\Domain\Content\Requests\TrainingOption\UpdateTrainingOptionRequest;
use App\Domain\Content\Requests\TrainingOption\CreateTrainingOptionRequest;

class TrainingOptionController extends Controller
{
  /**
   * @var TrainingOptionService
  */
  private $training_option_service;
  
  public function __construct()
  {
    $this->training_option_service = new TrainingOptionService;
  }

  public function getAll()
  {
    try {
      $response = $this->training_option_service->getAll();
      return $this->successResponse('Training Options fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateTrainingOptionRequest $request)
  {
    try {
      $response = $this->training_option_service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Training Option created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function update(UpdateTrainingOptionRequest $request)
  {
    try {
      $response = $this->training_option_service->update($request->validated(), Auth::user()->id);
      return $this->successResponse('Training Option updated', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

public function delete(DeleteRequest $request)
  {
    try {
      $training_option_service = new TrainingOptionService(new CourseLessonService);
      $response = $training_option_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Training Options deleted', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}