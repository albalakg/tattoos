<?php

namespace App\Domain\Orders\Controllers;

use App\Domain\Content\Services\ContentService;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Services\MarketingTokenService;
use App\Domain\Orders\Requests\CreateMarketingTokenRequest;
use App\Domain\Orders\Requests\DeleteMarketingTokenRequest;
use App\Domain\Orders\Requests\UpdateMarketingTokenRequest;

class MarketingTokenController extends Controller
{  
  /**
   * @var MarketingTokenService
  */
  public $service;
  
  public function __construct()
  {
    $this->service = new MarketingTokenService(
      new OrderService(),
      new ContentService()
    );
  }

  public function getAll()
  {
    try {
      $response = $this->service->getAll();
      return $this->successResponse('Marketing links fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function update(UpdateMarketingTokenRequest $request)
  {
    try {
      $response = $this->service->update($request->validated());
      return $this->successResponse('Marketing link has been updated', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateMarketingTokenRequest $request)
  {
    try {
      $response = $this->service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Marketing link has been created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function delete(DeleteMarketingTokenRequest $request)
  {
    try {
      $response = $this->service->delete($request->input('ids'));
      return $this->successResponse('Marketing link has been deleted', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function forceDelete(DeleteMarketingTokenRequest $request)
  {
    try {
      $response = $this->service->forceDelete($request->input('id'));
      return $this->successResponse('Marketing link has been forced deleted', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}