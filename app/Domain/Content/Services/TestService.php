<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Content\Models\Test;
use App\Domain\Helpers\FileService;
use Illuminate\Pagination\Paginator;
use App\Domain\Helpers\StatusService;
use App\Domain\Interfaces\IContentService;
use App\Domain\Users\Services\UserCourseService;

class TestService implements IContentService
{
  const FILES_PATH = 'content/tests';

  /**
   * @var LogService
  */
  private $log_service;

  /**
   * @var UserCourseService
  */
  private $user_course_service;
  
  public function __construct(UserCourseService $user_course_service = null)
  {
    $this->user_course_service = $user_course_service;
    $this->log_service = new LogService('tests');
  }
    
  /**
   * @return Paginator
  */
  public function getAll(): Paginator
  {
    return $this->user_course_service->getAllTests();
  }
    
  /**
   * @param object $testData
   * @param int $created_by
   * @return Test
  */
  public function create(object $testData, int $created_by): ?Test
  {
    $test               = new Test;
    $test->name         = $testData->name;
    $test->description  = $testData->description;
    $test->status       = StatusService::ACTIVE;
    $test->test_path   = FileService::create($testData->file, self::FILES_PATH);
    $test->created_by   = $created_by;
    $test->save();

    return $test;
  }
    
  /**
   * @param object $testData
   * @param int $updated_by
   * @return Test
  */
  public function update(object $testData, int $updated_by): ?Test
  {
    if(!$test = Test::find($testData->id)) {
      throw new Exception('Test not found');
    }

    $test->name         = $testData->name;
    $test->description  = $testData->description;
    $test->status       = $testData->status;

    if(!empty($testData->file)) {
      FileService::delete($test->test_path);
      $test->test_path   = FileService::create($testData->file, self::FILES_PATH);
    }

    $test->save();
    return $test;
  }
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $test_id) {
      if($error = $this->delete($test_id, $deleted_by)) {
        return $error;
      }
    }
  } 
  
  /**
   * @param int $test_id
   * @param int $deleted_by
   * @return void
  */
  public function delete(int $test_id, int $deleted_by)
  {
    try {
      if(!$test = Test::find($test_id)) {
        throw new Exception('Test not found');
      }

      if($this->isTestInUsed($test_id)) {
        throw new Exception('Cannot delete test that is being used');
      }
  
      FileService::delete($test->test_path);
      $test->delete();
      
    } catch(Exception $ex) {
      $this->log_service->error($ex);
      return $this->course_lesson_service->getLessonsWithTest($test_id);
    }
  }
  
  /**
   * @param int $test_id
   * @return bool
  */
  private function isTestInUsed($test_id): bool
  {
    return $this->course_lesson_service->isTestInUsed($test_id);
  }
}