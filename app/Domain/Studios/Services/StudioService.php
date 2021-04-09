<?php

namespace App\Domain\Studios\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\BaseService;
use App\Domain\Helpers\FileService;
use App\Domain\Helpers\StatusService;
use App\Domain\Studios\Models\Studio;
use App\Domain\Studios\Models\StudioTag;
use App\Domain\Helpers\PaginationService;
use App\Domain\Studios\Models\StudioStar;
use App\Domain\Studios\Models\StudioUser;
use App\Domain\Studios\Models\StudioPoint;
use App\Domain\Studios\Models\StudioWatch;
use App\Domain\Studios\Services\StudioCommentService;

class StudioService extends BaseService
{  
  public function __construct()
  {
    $this->setLogFile('studios');
  }

  /**
   * Get studios
   *
   * @param int $status
   * @param int $records
   * @return object|null
   */
  public function getStudios(int $status = StatusService::ACTIVE, int $records = PaginationService::SMALL) :object
  {
    try {
      return Studio::where('status', $status)
                   ->orderBy('id', 'points')
                   ->orderBy('id', 'desc')
                   ->select('id', 'name', 'description', 'image', 'stars', 'watched', 'country', 'city', 'street', 'number')
                   ->simplePaginate($records);

    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return null;
    }
  }

  /**
   * Show Studio
   *
   * @param int $studio_id
   * @param int $user_id
   * @param App\Domain\Users\Services\UserService $userService
   * @param mixed $studioComment
   * @return object|null
   */
  public function showStudio(int $studio_id, int $user_id, mixed $userService, mixed $studioComment)
  {
    try {
      if(!$this->isStudioExists($studio_id)) {
        throw new Exception('Studio not found');
      }

      $this->watchStudio($studio_id, $user_id, $userService);
  
      $studio = Studio::where('studio_id', $studio_id)
                      ->select('id', 'name', 'description', 'image', 'stars', 'watched', 'country', 'city', 'street', 'number')
                      ->first();

      $studio->comments = $studioComment->getStudioComments($studio_id);

      return $studio;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return null;
    }
  }  
  
  /**
   * Checks if the studio exists
   *
   * @param int $studio_id
   * @return bool
  */
  public function isStudioExists(int $studio_id)
  {
    return Studio::where('id', $studio_id)->exists();
  }
    
  /**
   * Get a studio by a dynamic field
   *
   * @param string $type
   * @param string $value
   * @return object|null
  */
  public function getStudioByField(string $type, string $value)
  {
    return Studio::where($type, $value)->first();
  }

  /**
   * Update the studio's status
   *
   * @param int $studio_id
   * @param int $status
   * @return bool
  */
  private function updateStudioStatus(int $studio_id, int $status)
  {
    try {
      if(!$this->isStudioExists($studio_id)) {
        throw new Exception('Studio not found');
      }
      
      Studio::where('id', $studio_id)
            ->update([
              'status' => $status
            ]);

      LogService::info("Studio $studio_id has updated the status", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Create a new studio
   *
   * @param object $studio
   * @param int $user_id
   * @return object|null
  */
  public function createStudio(object $studio, int $user_id)
  {
    try {
      $fileSerivce = new FileService();
      if(!$file_path = $fileSerivce->create($studio->image, 'studios')) {
        throw new Exception('Failed to store the studio image');
      }

      $new_studio = new Studio;
      $new_studio->name = $studio->name;
      $new_studio->description = $studio->description;
      $new_studio->image = $file_path;
      $new_studio->country = $studio->country;
      $new_studio->city = $studio->city;
      $new_studio->street = $studio->street;
      $new_studio->number = $studio->number;
      $new_studio->save();

      if(!$this->saveStudioTags($new_studio->id, $studio->tags)) {
        $this->deleteStudio($new_studio->id, $user_id);
        throw new Exception('Failed to create studio');
      }

      LogService::info("Studio $new_studio->id created by user $user_id", $this->log_file);
      return $new_studio;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return null;
    }
  }
  
  /**
   * Delete the studio
   *
   * @param int $studio_id
   * @param int $user_id
   * @return bool
   */
  public function deleteStudio(int $studio_id, int $user_id)
  {
    try {
      if(!$studio = $this->getStudioByField('id', $studio_id)) {
        throw new Exception('Studio not found');
      }
      
      $studio->delete();
      $this->deleteStudioMetaData($studio_id);
      $studioCommentService = new StudioCommentService;
      $studioCommentService->deleteStudioCommentByStudio($studio_id, $user_id);
      
      
      LogService::info("Studio $studio_id has been deleted by $user_id", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error("Failed to delete studio: $studio_id" . $ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Delete all the studio meta data
   *
   * @param int $studio_id
   * @return bool
   */
  private function deleteStudioMetaData(int $studio_id)
  {
    try {
      StudioPoint::where('studio_id', $studio_id)->delete();
      StudioStar::where('studio_id', $studio_id)->delete();
      StudioTag::where('studio_id', $studio_id)->delete();
      StudioUser::where('studio_id', $studio_id)->delete();
      StudioWatch::where('studio_id', $studio_id)->delete();
      
      LogService::info("Studio $studio_id meta data deleted sucessfully", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error("Failed to delete meta data of studio: $studio_id" . $ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Update the studio
   *
   * @param object $studio
   * @param int $user_id
   * @return object|null
  */
  public function updateStudio(object $studio, int $user_id)
  {
    try {
      if(!$studio = $this->getStudioByField('id', $studio->id)) {
        throw new Exception('Studio not found');
      }
      
      $fileSerivce = new FileService();
      if(!$file_path = $fileSerivce->create($studio->image, 'studios')) {
        throw new Exception('Failed to store the studio image');
      }

      $studio->name = $studio->name;
      $studio->description = $studio->description;
      $studio->image = $file_path;
      $studio->country = $studio->country;
      $studio->city = $studio->city;
      $studio->street = $studio->street;
      $studio->number = $studio->number;
      $studio->save();

      $this->saveStudioTags($studio->id, $studio->tags);

      LogService::info("Studio $studio->id has been updated by $user_id", $this->log_file);
      return $studio;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return null;
    }
  }
  
  /**
   * Save tags for the studio
   *
   * @param int $studio_id
   * @param array $tags
   * @return bool
   */
  private function saveStudioTags(int $studio_id, array $tags) :bool
  {
    try {
      if(!$this->isStudioExists($studio_id)) {
        return false;
      }

      $tags_data = [];
      foreach($tags AS $tag) {
        $tags_data[] = [
          'tag_id' => $tag,
          'studio_id' => $studio_id,
          'created_at' => now()
        ];
      }
      
      StudioTag::insert($tags_data);

      LogService::info("Studio $studio_id has saved new tags " . json_encode($tags), $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Write a record when a user watches a studio for the first time
   *
   * @param int $studio_id
   * @param int $user_id
   * @param App\Domain\Users\Services\UserService $userService
   * @return bool
   */
  private function watchStudio(int $studio_id, int $user_id, mixed $userService) :bool
  { 
    try {
      if($userService->isUserWatchedStudio($user_id, $studio_id)) {
        return false;
      }
     
      if(!$userService->isUserExists($user_id)) {
        throw new Exception('User not found');
      }
      
      StudioWatch::create([
        'studio_id' => $studio_id,
        'user_id' => $user_id,
        'created_at' => now()
      ]);

      Studio::where('id', $studio_id)->increment('watched');

      LogService::info("Studio $studio_id watched by user $user_id", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Star a studio
   *
   * @param int $studio_id
   * @param int $user_id
   * @param float $stars
   * @param App\Domain\Users\Services\UserService $userService
   * @return object|null
   */
  public function starStudio(int $studio_id, int $user_id, float $stars, mixed $userService)
  {
    try {
      if(!$this->isStudioExists($studio_id)) {
        throw new Exception('Studio not found');
      }

      if(!$userService->isUserExists($user_id)) {
        throw new Exception('User not found');
      }

      $studio_star = $this->getStudioStarsByUser($studio_id, $user_id);
      if(!$studio_star) {
        $studio_star = new StudioStar;
        $studio_star->studio_id = $studio_id;
        $studio_star->user_id = $user_id;
      } else {
        $studio_star->updated_at = now();
      }

      $studio_star->stars = $stars;
      $studio_star->save();

      $this->sumStudioStars($studio_id);

      return $studio_star;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return null;
    }
  }
  
  /**
   * Get the studio star by a user
   *
   * @param int $studio_id
   * @param int $user_id
   * @return object|null
  */
  private function getStudioStarsByUser(int $studio_id, int $user_id)
  {
    try {
      return StudioStar::where('studio_id', $studio_id)
                        ->where('user_id', $user_id)
                        ->first();
      
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return null;
    }
  }

  /**
   * Delete studio star rank
   *
   * @param int $studio_id
   * @param int $user_id
   * @param int $deleted_by
   * @return bool
  */
  private function deleteStudioStar(int $studio_id, int $user_id, int $deleted_by)
  {
    try {
      if(!$studio_star = $this->getStudioStarsByUser($studio_id, $user_id)) {
        throw new Exception('Studio star not found');
      }

      $studio_star->delete();

      $this->sumStudioStars($studio_id);

      LogService::info("Studio $studio_id star by $user_id is deleted by $deleted_by", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Sum the studios stars
   * This is heavy process which runs on queue in the background 
   * TODO: enter to queue
   *
   * @param int $studio_id
   * @return bool
  */
  private function sumStudioStars(int $studio_id)
  {
    try {
      $stars_sum = StudioStar::where('studio_id', $studio_id)
                             ->sum('stars');

      Studio::where('studio_id', $studio_id)
            ->update([
              'stars' => $stars_sum
            ]);

      LogService::info("Studio $studio_id stars is summed to $stars_sum", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
}