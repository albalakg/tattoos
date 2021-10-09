<?php

namespace App\Domain\Support\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Users\Services\UserService;
use App\Domain\Support\Services\SupportService;
use App\Domain\Support\Services\SupportCategoryService;
use App\Domain\Support\Requests\UpdateSupportTicketStatusRequest;
use App\Domain\Support\Requests\CreateSupportTicketMessageRequest;

class SupportController extends Controller
{  
  /**
   * @var SupportService
  */
  public $service;
  
  public function __construct()
  {
    $this->service = new SupportService(
      new SupportCategoryService,
      new UserService
    );
  }

  public function getAll()
  {
    try {
      $response = $this->service->getAll();
      return $this->successResponse('Support fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function updateStatus(UpdateSupportTicketStatusRequest $request)
  {
    try {
      $response = $this->service->updateStatus($request->id, $request->status, Auth::user()->id);
      return $this->successResponse('Support fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function createSupportTicketMessage(CreateSupportTicketMessageRequest $request)
  {
    try {
      $response = $this->service->createSupportTicketMessage($request->validated(), Auth::user()->id);
      return $this->successResponse('Message created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }
}