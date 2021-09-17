<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use App\Domain\Content\Services\CourseCategoryService;

class CourseCategoryController extends Controller
{
  public function __construct()
  {
    $this->service = new CourseCategoryService;
  }

  public function getAll(CourseCategoryService $course_service)
  {
    try {
      $response = $course_service->getAll();
      return $this->successResponse('Course Categories fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }
}