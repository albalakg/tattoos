<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\TrainerService;
use App\Domain\Content\Requests\Trainer\CreateTrainerRequest;
use App\Domain\Content\Services\CourseAreaService;
use App\Domain\Content\Requests\Trainer\UpdateTrainerRequest;

class TrainerController extends Controller
{
  /**
   * @var TrainerService
  */
  private $trainer_service;
  
  public function __construct()
  {
    $this->trainer_service = new TrainerService(
      new CourseAreaService()
    );
  }

  public function getAll()
  {
    try {
      $response = $this->trainer_service->getAll();
      return $this->successResponse('Trainers fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function getTrainersForApp()
  {
    try {
      $response = $this->trainer_service->getTrainersForApp();
      return $this->successResponse('Trainers fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateTrainerRequest $request)
  {
    try {
      $response = $this->trainer_service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Trainer created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function update(UpdateTrainerRequest $request)
  {
    try {
      $response = $this->trainer_service->update($request->validated(), Auth::user()->id);
      return $this->successResponse('Trainer created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function delete(DeleteRequest $request)
  {
    try {
      $response = $this->trainer_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Trainers deleted', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}