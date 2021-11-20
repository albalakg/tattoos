<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\CouponService;
use App\Domain\Content\Requests\CreateCouponRequest;
use App\Domain\Content\Services\CourseLessonService;
use App\Domain\Content\Requests\UpdateCouponsRequest;
use App\Domain\Content\Requests\UpdateCouponStatusRequest;

class CouponController extends Controller
{
  /**
   * @var CouponService
  */
  private $course_area_service;
  
  public function __construct()
  {
    $this->course_area_service = new CouponService(
      new CourseLessonService
    );
  }

  public function getAll()
  {
    try {
      $response = $this->course_area_service->getAll();
      return $this->successResponse('Coupons fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateCouponRequest $request)
  {
    try {
      $response = $this->course_area_service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Coupon created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function updateStatus(UpdateCouponStatusRequest $request)
  {
    try {
      $response = $this->course_area_service->updateStatus($request->id, $request->status, Auth::user()->id);
      return $this->successResponse('Coupon updated', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function delete(DeleteRequest $request)
  {
    try {
      $response = $this->course_area_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Coupons deleted', $response);
    } catch (Exception $ex) {
      return $this->errorResponse(
        $ex,
        $this->course_area_service->error_data
      );
    }
  }
}