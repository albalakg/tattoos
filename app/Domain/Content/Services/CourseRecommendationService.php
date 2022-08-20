<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Content\Models\Course;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Interfaces\IContentService;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Content\Services\CourseService;
use App\Domain\Content\Models\CourseRecommendation;

class CourseRecommendationService implements IContentService
{
  /**
   * @var LogService
  */
  private $log_service;

  /**
   * @var CourseService|null
  */
  private $course_service;
  
  /**
   * Contain the error data
   *
   * @var mixed
  */
  public $error_data;
  
  public function __construct(CourseService $course_service = null)
  {
    $this->course_area_service = $course_service;
    $this->log_service = new LogService('courseRecommendations');
  }
  
  /**
   * @param int $course_recommendation_id
   * @return CourseRecommendation|null
  */
  public function getCourseRecommendation(int $course_recommendation_id): ?CourseRecommendation
  {
    return CourseRecommendation::find($course_recommendation_id);
  } 
  
  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return CourseRecommendation::all();
  } 
  
  /**
   * Fully deletes all of the content
   *
   * @return void
  */
  public function truncate()
  {
    $course_recommendation_ids = CourseRecommendation::withTrashed()->pluck('id');
    foreach($course_recommendation_ids AS $course_recommendation_id) {
      $this->forceDelete($course_recommendation_id, 0);
    }
    CourseRecommendation::truncate();
  }
    
  /**
   * @param array $data
   * @param int $created_by
   * @return array
  */
  public function create(array $data, int $created_by): array
  {
    $recommendations = [];
    foreach($data['recommendations'] AS $recommendation) {
      $recommendations[] = $this->createSingleRecommendation($recommendation, $data['course_id'], $created_by);
    }
    
    return $recommendations;
  }
  
  /**
   * @param array $data
   * @param int $course_id
   * @param int $created_by
   * @return CourseRecommendation
   */
  public function createSingleRecommendation(array $data, int $course_id, int $created_by): CourseRecommendation
  {
    $course_recommendation              = new CourseRecommendation();
    $course_recommendation->course_id   = $course_id;
    $course_recommendation->name        = $data['name'];
    $course_recommendation->content     = $data['content'];
    $course_recommendation->created_by  = $created_by;
    $course_recommendation->save();
    
    $this->log_service->info('Course Recommendation ' . $course_recommendation->id . ' has been created: ' . json_encode($course_recommendation));
    return $course_recommendation;
  }

  /**
   * @param array $data
   * @param int $updated_by
   * @return CourseRecommendation|null
  */
  public function update(array $data, int $updated_by): ?CourseRecommendation
  {
    if(!$course_recommendation = CourseRecommendation::find($data['id'])) {
      throw new Exception('Course Recommendation not found');
    };

    $course_recommendation->name         = $data['name'];
    $course_recommendation->content      = $data['content'];
    
    $this->log_service->info('Course Recommendation ' . $course_recommendation->id . ' has been updated: ' . json_encode($course_recommendation));

    $course_recommendation->save();
    return $course_recommendation;
  }
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $course_recommendation_id) {
      $this->delete($course_recommendation_id, $deleted_by);
    }
  } 
  
  /**
   * Soft delete the item 
   * @param int $course_recommendation_id
   * @param int $deleted_by
   * @return bool
  */
  public function delete(int $course_recommendation_id, int $deleted_by): bool
  {
    $result = CourseRecommendation::where('id', $course_recommendation_id)->delete();
    $this->log_service->info('Course Recommendation ' . $course_recommendation_id . ' has been deleted');
    return $result;
  }
  
  /**
   * @param int $course_recommendation_id
   * @param int $deleted_by
   * @return bool
  */
  public function forceDelete(int $course_recommendation_id, int $deleted_by): bool
  {
    $result = CourseRecommendation::where('id', $course_recommendation_id)->forceDelete();
    $this->log_service->info('Course Recommendation ' . $course_recommendation_id . ' has been forced deleted');
    return $result;
  }
}