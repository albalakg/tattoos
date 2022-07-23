<?php

namespace App\Domain\Support\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Users\Services\UserService;
use App\Domain\Support\Services\SupportService;
use App\Domain\Support\Requests\CreateSupportTicketRequest;
use App\Domain\Support\Services\SupportCategoryService;
use App\Domain\Support\Requests\UpdateSupportTicketStatusRequest;
use App\Domain\Support\Requests\CreateSupportTicketMessageRequest;

class SupportController extends Controller
{  
  const LOG_FILE = 'support';
  
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
      return $this->successResponse('Support fetched', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateSupportTicketRequest $request)
  {
    try {
      $created_support_ticket = $this->service->createSupportTicket($request->validated(), Auth::user());
      return $this->successResponse('Support created successfully', $created_support_ticket);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function updateStatus(UpdateSupportTicketStatusRequest $request)
  {
    try {
      $response = $this->service->updateStatus($request->id, $request->status, Auth::user()->id);
      return $this->successResponse('Support\'s status updated', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function createSupportTicketMessage(CreateSupportTicketMessageRequest $request)
  {
    try {
      $response = $this->service->createSupportTicketMessage($request->validated(), Auth::user()->id);
      return $this->successResponse('Message created', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }
}