<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Services\EquipmentService;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\CourseLessonService;
use App\Domain\Content\Requests\Equipment\UpdateEquipmentRequest;
use App\Domain\Content\Requests\Equipment\CreateEquipmentRequest;

class EquipmentController extends Controller
{
  /**
   * @var EquipmentService
  */
  private $equipment_service;
  
  public function __construct()
  {
    $this->equipment_service = new EquipmentService;
  }

  public function getAll()
  {
    try {
      $response = $this->equipment_service->getAll();
      return $this->successResponse('Equipment fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateEquipmentRequest $request)
  {
    try {
      $response = $this->equipment_service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Equipment created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function update(UpdateEquipmentRequest $request)
  {
    try {
      $response = $this->equipment_service->update($request->validated(), Auth::user()->id);
      return $this->successResponse('Equipment updated', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

public function delete(DeleteRequest $request)
  {
    try {
      $equipment_service = new EquipmentService(new CourseLessonService);
      $response = $equipment_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Equipment deleted', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}