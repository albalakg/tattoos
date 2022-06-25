<?php

namespace App\Domain\Orders\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Users\Services\UserService;
use App\Domain\Orders\Services\PoliciesService;
use App\Domain\Orders\Requests\CreateTermsAndConditionsRequest;

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
      return $this->successResponse('Terms and Conditions fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function getCurrent()
  {
    try {
      $response = $this->service->getCurrentTermsAndConditions();
      return $this->successResponse('Terms and Conditions fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateTermsAndConditionsRequest $request)
  {
    try {
      $response = $this->service->createTermsAndConditions($request->validated(), Auth::user()->id);
      return $this->successResponse('Terms and Conditions created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function verify()
  {
    try {

      $response = $this->service->verifyTermsAndConditions(Auth::user()->id);
      return $this->successResponse('Terms and Conditions created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}