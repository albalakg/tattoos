<?php

namespace App\Domain\Orders\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TermsAndConditionsController extends Controller
{  
  /**
   * @var OrderService
  */
  public $service;
  
  public function __construct()
  {
    $this->service = new OrderService(
      new UserService
    );
  }

  public function getAll()
  {
    try {
      $response = $this->service->getAll();
      return $this->successResponse('Orders fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function updateStatus(UpdateOrderStatusRequest $request)
  {
    try {
      $response = $this->service->updateStatus($request->id, $request->status, Auth::user()->id);
      return $this->successResponse('Order\'s status updated successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }
}