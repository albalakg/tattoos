<?php

namespace App\Domain\Orders\Controllers;

use App\Domain\Content\Services\ContentService;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Users\Services\UserService;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Requests\CreateOrderRequest;
use App\Domain\Orders\Requests\OrderCompletedRequest;
use App\Domain\Orders\Requests\UpdateOrderStatusRequest;

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
      new ContentService
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
      return $this->successResponse('Order\'s status updated', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function completed(OrderCompletedRequest $request)
  {
    try {
      $response = $this->service->completed($request->input('token'));
      return $this->successResponse('Order\'s status updated', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}