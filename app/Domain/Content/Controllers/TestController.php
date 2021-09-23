<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Services\TestService;
use App\Domain\Content\Requests\CreateTestRequest;
use App\Domain\Content\Requests\UpdateTestRequest;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\CourseLessonService;
use App\Domain\Users\Services\UserCourseService;

class TestController extends Controller
{
  /**
   * @var TestService
  */
  private $test_service;
  
  public function __construct()
  {
    $this->test_service = new TestService(
      new UserCourseService
    );
  }

  public function getAll()
  {
    try {
      $response = $this->test_service->getAll();
      return $this->successResponse('Tests fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function create(CreateTestRequest $request)
  {
    try {
      $response = $this->test_service->create((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('Test created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function update(UpdateTestRequest $request)
  {
    try {
      $response = $this->test_service->update((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('Test updated successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

public function delete(DeleteRequest $request)
  {
    try {
      $test_service = new TestService(new CourseLessonService);
      $response = $test_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Tests deleted successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }
}