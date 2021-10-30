<?php

namespace App\Domain\Orders\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Orders\Services\PoliciesService;
use App\Domain\Users\Services\UserService;

class TermsAndConditionsController extends Controller
{  
  /**
   * @var PoliciesService
  */
  public $service;
  
  public function __construct()
  {
    $this->service = new PoliciesService(
      new UserService
    );
  }

  public function getAll()
  {
    try {
      $response = $this->service->getTermsAndConditions();
      return $this->successResponse('Terms and Conditions fetched successfully', $response);
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