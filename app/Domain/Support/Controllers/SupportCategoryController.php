<?php

namespace App\Domain\Support\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Users\Services\UserService;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Support\Services\SupportService;
use App\Domain\Support\Services\SupportCategoryService;
use App\Domain\Users\Requests\UpdateOrderStatusRequest;
use App\Domain\Support\Requests\CreateSupportCategoryRequest;
use App\Domain\Support\Requests\DeleteSupportCategoryRequest;
use App\Domain\Support\Requests\UpdateSupportCategoryStatusRequest;

class SupportCategoryController extends Controller
{  
  /**
   * @var SupportCategoryService
  */
  public $service;
  
  public function __construct()
  {
    $this->service = new SupportCategoryService();
  }

  public function getAll()
  {
    try {
      $response = $this->service->getAll();
      return $this->successResponse('Support Categories fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateSupportCategoryRequest $request)
  {
    try {
      $response = $this->service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Support Categories created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function multipleDelete(DeleteRequest $request)
  {
    try {
      $response = $this->service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Support Categories deleted successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
  
  public function updateStatus(UpdateSupportCategoryStatusRequest $request)
  {
    try {
      $response = $this->service->updateStatus($request->id, $request->status, Auth::user()->id);
      return $this->successResponse('Support Categories status updated successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}