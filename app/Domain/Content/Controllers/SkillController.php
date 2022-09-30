<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Services\SkillService;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\CourseLessonService;
use App\Domain\Content\Requests\Skill\UpdateSkillRequest;
use App\Domain\Content\Requests\Skill\CreateSkillRequest;

class SkillController extends Controller
{
  /**
   * @var SkillService
  */
  private $skill_service;
  
  public function __construct()
  {
    $this->skill_service = new SkillService;
  }

  public function getAll()
  {
    try {
      $response = $this->skill_service->getAll();
      return $this->successResponse('Skills fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateSkillRequest $request)
  {
    try {
      $response = $this->skill_service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Skill created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function update(UpdateSkillRequest $request)
  {
    try {
      $response = $this->skill_service->update($request->validated(), Auth::user()->id);
      return $this->successResponse('Skill updated', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

public function delete(DeleteRequest $request)
  {
    try {
      $skill_service = new SkillService(new CourseLessonService);
      $response = $skill_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Skills deleted', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}