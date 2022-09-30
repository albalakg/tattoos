<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\CouponService;
use App\Domain\Content\Requests\GetCouponRequest;
use App\Domain\Content\Requests\CreateCouponRequest;
use App\Domain\Content\Requests\UpdateCouponRequest;
use App\Domain\Content\Services\CourseLessonService;
use App\Domain\Content\Requests\UpdateCouponStatusRequest;

class CouponController extends Controller
{
  /**
   * @var CouponService
  */
  private $coupon_service;
  
  public function __construct()
  {
    $this->coupon_service = new CouponService(
      new CourseLessonService
    );
  }

  public function getAll()
  {
    try {
      $coupons = $this->coupon_service->getAll();
      return $this->successResponse('Coupons fetched', $coupons);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
  
  public function getCoupon(GetCouponRequest $request)
  {
    try {
      $coupon = $this->coupon_service->getByCode($request->input('code'));
      unset($coupon->id);
      return $this->successResponse('Coupon fetched', $coupon);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function update(UpdateCouponRequest $request)
  {
    try {
      $coupon = $this->coupon_service->update($request->validated(), Auth::user()->id);
      return $this->successResponse('Coupon created', $coupon);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateCouponRequest $request)
  {
    try {
      $coupon = $this->coupon_service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Coupon created', $coupon);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function updateStatus(UpdateCouponStatusRequest $request)
  {
    try {
      $response = $this->coupon_service->updateStatus($request->id, $request->status, Auth::user()->id);
      return $this->successResponse('Coupon updated', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function delete(DeleteRequest $request)
  {
    try {
      $response = $this->coupon_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Coupons deleted', $response);
    } catch (Exception $ex) {
      return $this->errorResponse(
        $ex,
      );
    }
  }
}