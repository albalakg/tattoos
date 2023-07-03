<?php

namespace App\Domain\Orders\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Users\Services\UserService;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Content\Services\ContentService;
use App\Domain\Orders\Requests\CreateOrderRequest;
use App\Domain\Orders\Requests\OrderCompletedRequest;
use App\Domain\Orders\Services\MarketingTokenService;

class OrderController extends Controller
{  
  /**
   * @var OrderService
  */
  public $service;
  
  public function __construct()
  {
    $this->service = new OrderService(
      new UserService,
      new ContentService,
      new MarketingTokenService
    );
  }

  public function getAll()
  {
    try {
      $response = $this->service->getAll();
      return $this->successResponse('Orders fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateOrderRequest $request)
  {
    try {
      $response = $this->service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Order has been created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function success(Request $request)
  {
    try {
      $response = $this->service->completed($request, 'success');
      return $this->successResponse('Order\'s status updated successfully to completed', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function failure(Request $request)
  {
    try {
      $response = $this->service->completed($request, 'failure');
      return $this->successResponse('Order\'s status updated successfully to completed', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function callback(Request $request)
  {
    try {
      $response = $this->service->completed($request, 'callback');
      return $this->successResponse('Order\'s status updated successfully to completed', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}