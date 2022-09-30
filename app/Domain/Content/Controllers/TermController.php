<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Services\TermService;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\CourseLessonService;
use App\Domain\Content\Requests\Term\UpdateTermRequest;
use App\Domain\Content\Requests\Term\CreateTermRequest;

class TermController extends Controller
{
  /**
   * @var TermService
  */
  private $term_service;
  
  public function __construct()
  {
    $this->term_service = new TermService;
  }

  public function getAll()
  {
    try {
      $response = $this->term_service->getAll();
      return $this->successResponse('Terms fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateTermRequest $request)
  {
    try {
      $response = $this->term_service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Term created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function update(UpdateTermRequest $request)
  {
    try {
      $response = $this->term_service->update($request->validated(), Auth::user()->id);
      return $this->successResponse('Term updated', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

public function delete(DeleteRequest $request)
  {
    try {
      $term_service = new TermService(new CourseLessonService);
      $response = $term_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Terms deleted', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}