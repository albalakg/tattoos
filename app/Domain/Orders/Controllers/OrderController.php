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
use App\Domain\Orders\Requests\OrderCallbackRequest;
use App\Domain\Orders\Services\MarketingTokenService;
use App\Domain\Content\Requests\CourseCoupon\GetSuccessOrderRequest;

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
      return $this->successResponse('Order has been fetched successfully', $response);
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

  public function getSuccessOrder(GetSuccessOrderRequest $request)
  {
    try {
      $coupon = $this->service->getOrderByToken($request->input('token'), ['order_number']);
      unset($coupon->id);
      return $this->successResponse('Order has been fetched successfully', $coupon);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function callback(Request $request)
  {
    try {
      $data = [
        'page_request_uid'  => $request->input('page_request_uid'),
        'approval_num'      => $request->input('approval_num'),
        'status'            => $request->input('status'),
        'user_agent'        => $request->header('user-agent')
      ];
      dd($data, $request->fullUrl());
      $response = $this->service->orderCompleted($data);
      return $this->successResponse('Order\'s status updated successfully to completed', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}