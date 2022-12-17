<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Content\Models\Term;
use App\Domain\Helpers\StatusService;
use App\Domain\Interfaces\IContentService;
use Illuminate\Database\Eloquent\Collection;

class TermService implements IContentService
{
  private Term|null $term;

  private LogService $log_service;

  private CourseLessonService|null $course_lesson_service;
  
  public function __construct(CourseLessonService $course_lesson_service = null)
  {
    $this->course_lesson_service = $course_lesson_service;
    $this->log_service = new LogService('terms');
  }
    
  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return Term::select(
                'id',
                'name',
                'status',
                'description',
                'created_at',
              )
              ->orderBy('id', 'desc')
              ->get();
  }
    
  /**
   * @return null|Term
  */
  public function getRandomTerm(): ?Term
  {
    return Term::inRandomOrder()->first();
  }
          
  /**
   * @param int $limit the amount of terms to fetch
   * @return Collection
  */
  public function getRandomTerms(int $limit = 1): Collection
  {
    return Term::inRandomOrder()->limit($limit)->get();
  }

  /**
   * Fully deletes all of the content
   *
   * @return void
  */
  public function truncate()
  {
    $terms_ids = Term::withTrashed()->pluck('id');
    foreach($terms_ids AS $term_id) {
      $this->forceDelete($term_id, 0);
    }
    Term::truncate();
  }

  /**
   * @param array $data
   * @param int $created_by
   * @return Term
  */
  public function create(array $data, int $created_by): ?Term
  {
    $term                = new Term;
    $term->name          = $data['name'];
    $term->description   = $data['description'];
    $term->status        = StatusService::ACTIVE;
    $term->created_by    = $created_by;
    $term->status        = $data['status'] ?? StatusService::PENDING;

    $term->save();

    $this->log_service->info('Term has been created: ' . json_encode($term));

    return $term;
  }
    
  /**
   * @param array $data
   * @param int $updated_by
   * @return Term
  */
  public function update(array $data, int $updated_by): ?Term
  {
    if(!$term = Term::find($data['id'])) {
      throw new Exception('Term not found');
    }

    $term->name         = $data['name'];
    $term->description  = $data['description'];
    $term->status       = $data['status'];
    $term->save();
    
    $this->log_service->info('Term has been updated: ' . json_encode($term));

    return $term;
  }
    
  /**
   * @param array $ids
   * @return true
  */
  public function termsExist(array $ids): bool
  {
    return Term::whereIn('id', $ids)->exists();    
  } 
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $term_id) {
      $this->delete($term_id, $deleted_by);
    }
  } 
  
  /**
   * Soft delete the item
   * @param int $term_id
   * @param int $deleted_by
   * @return bool
  */
  public function delete(int $term_id, int $deleted_by): bool
  {
    $this->validateIfCanDelete($term_id);

    $result = $this->term->delete();
    $this->log_service->info('Term ' . $term_id . ' has been deleted');
    return $result;
  }
  
  /**
   * @param int $term_id
   * @param int $deleted_by
   * @return bool
  */
  public function forceDelete(int $term_id, int $deleted_by): bool
  {
    $this->validateIfCanDelete($term_id);

    $result = $this->term->forceDelete();
    $this->log_service->info('Term ' . $term_id . ' has been forced deleted');
    return $result;
  }
  
  /**
   * Throws an error if failed the validation and cannot delete
   * If it can be deleted, stores the content in the class state
   * @param int $term_id
   * @return void
  */
  private function validateIfCanDelete(int $term_id)
  {
    if(!$term = Term::withTrashed()->find($term_id)) {
      throw new Exception('Term not found');
    }

    if($this->isTermInUsed($term_id)) {
      throw new Exception('Cannot delete term that is being used');
    }

    $this->term = $term;
  }

  /**
   * @param int $term_id
   * @return bool
  */
  private function isTermInUsed($term_id): bool
  {
    return $this->course_lesson_service->isTermInUsed($term_id);
  }
}