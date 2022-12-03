<?php

namespace App\Domain\Content\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Domain\Content\Services\TermService;
use App\Domain\Content\Services\SkillService;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\EquipmentService;
use App\Domain\Content\Services\CourseAreaService;
use App\Domain\Content\Requests\OrderContentRequest;
use App\Domain\Content\Services\CourseLessonService;
use App\Domain\Content\Services\TrainingOptionService;
use App\Domain\Content\Requests\CourseLesson\CreateCourseLessonRequest;
use App\Domain\Content\Requests\CourseLesson\UpdateCourseLessonRequest;

class CourseLessonController extends Controller
{  
  /**
   * @var CourseLessonService
  */
  private $lesson_service;
  
  public function __construct()
  {
    $this->lesson_service = new CourseLessonService(
      new CourseAreaService,
      new SkillService,
      new TermService,
      new EquipmentService,
      new TrainingOptionService
    );
  }

  public function getAll()
  {
    try {
      $response = $this->lesson_service->getAll();
      return $this->successResponse('Lessons fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function getRandomActiveLessons()
  {
    try {
      return response()->json(['data' => json_decode(Storage::get('test.json')), 'test' => 1]);
      $response = $this->lesson_service->getRandomActiveLessons();
      return $this->successResponse('Lessons fetched', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateCourseLessonRequest $request)
  {
    try {
      $response = $this->lesson_service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Lesson created', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function update(UpdateCourseLessonRequest $request)
  {
    try {
      $response = $this->lesson_service->update($request->validated(), Auth::user()->id);
      return $this->successResponse('Lesson updated', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function delete(DeleteRequest $request)
  {
    try {
      $response = $this->lesson_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Lessons deleted', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function order(OrderContentRequest $request)
  {
    try {
      $response = $this->lesson_service->updateOrder($request->content);
      return $this->successResponse('Lessons order updated successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}