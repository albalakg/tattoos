<?php

namespace App\Domain\Policies\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Users\Services\UserService;
use App\Domain\Policies\Services\PoliciesService;
use App\Domain\Policies\Requests\VerifyTermsAndConditionRequest;
use App\Domain\Policies\Requests\CreateTermsAndConditionsRequest;

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
      $response = $this->service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Terms and Conditions created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function verify(VerifyTermsAndConditionRequest $request)
  {
    try {
      $response = $this->service->verifyTermsAndConditions(Auth::user()->id, $request->tnc_id);
      return $this->successResponse('Terms and Conditions verified successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}