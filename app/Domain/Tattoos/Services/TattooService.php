<?php

namespace App\Domain\Tattoos\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\BaseService;
use App\Domain\Helpers\FileService;
use App\Domain\Helpers\StatusService;
use App\Domain\Tattoos\Models\Tattoo;
use App\Domain\Tattoos\Models\TattooTag;
use App\Domain\Helpers\PaginationService;
use App\Domain\Tattoos\Models\TattooLike;
use App\Domain\Tattoos\Models\TattooSave;
use App\Domain\Tattoos\Models\TattooImage;
use App\Domain\Tattoos\Models\TattooWatch;

class TattooService extends BaseService
{  
  public function __construct()
  {
    $this->setLogFile('tattoos');
  }

  /**
   * Get tattoos
   *
   * @param int $status
   * @param int $records
   * @return object|null
   */
  public function getTattoos(int $status = StatusService::ACTIVE, int $records = PaginationService::SMALL)
  {
    try {
      return Tattoo::where('status', $status)
                   ->orderBy('id', 'desc')
                   ->select('id', 'image')
                   ->simplePaginate($records);

    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return null;
    }
  }

  /**
   * Get tattoos by user
   *
   * @param int $user_id
   * @param int $status
   * @param int $records
   * @return object|null
   */
  public function getTattoosByUser(int $user_id, int $status = StatusService::ACTIVE, int $records = PaginationService::SMALL)
  {
    try {
      return Tattoo::where('created_by', $user_id)
                    ->where('creator_type', Tattoo::CREATOR_TYPE_USER)
                    ->where('status', $status)
                    ->orderBy('id', 'desc')
                    ->select('id', 'image')
                    ->simplePaginate($records);

    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return null;
    }
  }
    
  /**
   * Get tattoos by studio
   *
   * @param int $studio_id
   * @param int $status
   * @param int $records
   * @return object|null
   */
  public function getTattoosByStudio(int $studio_id, int $status = StatusService::ACTIVE, int $records = PaginationService::SMALL)
  {
    try {
      return Tattoo::where('created_by', $studio_id)
                    ->where('creator_type', Tattoo::CREATOR_TYPE_STUDIO)
                    ->where('status', $status)
                    ->orderBy('id', 'desc')
                    ->select('id', 'image')
                    ->simplePaginate($records);

    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return null;
    }
  }

  /**
   * Show Tattoo
   *
   * @param int $tattoo_id
   * @param int $user_id
   * @param App\Domain\Users\Services\UserService $userService
   * @param mixed $tattooComment
   * @return object|null
   */
  public function showTattoo(int $tattoo_id, int $user_id, mixed $userService, mixed $tattooComment)
  {
    try {
      if(!$this->isTattooExists($tattoo_id)) {
        throw new Exception('Tattoo not found');
      }
  
      $this->watchTattoo($tattoo_id, $user_id, $userService);
  
      $tattoo = Tattoo::where('tattoo_id', $tattoo_id)
                      ->with('tags', 'user', 'images')
                      ->select('id', 'image', 'title', 'likes')
                      ->first();

      $tattoo->comments = $tattooComment->getTattooComments($tattoo_id);

      return $tattoo;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return null;
    }
  }  
  
  /**
   * Checks if the tattoo exists
   *
   * @param int $tattoo_id
   * @return bool
  */
  public function isTattooExists(int $tattoo_id)
  {
    return Tattoo::where('id', $tattoo_id)->exists();
  }
    
  /**
   * Get a tattoo by a dynamic field
   *
   * @param string $type
   * @param string $value
   * @return object|null
  */
  public function getTattooByField(string $type, string $value)
  {
    return Tattoo::where($type, $value)->first();
  }

  /**
   * Update the tattoo's status
   *
   * @param int $tattoo_id
   * @param int $status
   * @return bool
  */
  private function updateTattooStatus(int $tattoo_id, int $status)
  {
    try {
      if(!$this->isTattooExists($tattoo_id)) {
        throw new Exception('Tattoo not found');
      }
      
      Tattoo::where('id', $tattoo_id)
          ->update([
            'status' => $status
          ]);

      LogService::info("Tattoo $tattoo_id has updated the status", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Add an image to the tattoo
   *
   * @param int $tattoo_id
   * @param mixed $file
   * @return bool
  */
  public function addImageToTattoo(int $tattoo_id, mixed $file)
  {
    try {
      if(!$this->isTattooExists($tattoo_id)) {
        throw new Exception('Tattoo not found');
      }
      
      $fileSerivce = new FileService();
      if(!$file_path = $fileSerivce->create($tattoo->image, 'tattoos')) {
        throw new Exception('Failed to store the tattoo image');
      }

      TattooImage::create([
        'tattoo_id' => $tattoo_id,
        'image' => $file_path
      ]);

      LogService::info("Added to tattoo $tattoo_id the image $file_path", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Delete images from tattoo
   *
   * @param array $tattoo_images_id
   * @return bool
  */
  public function deleteImagesFromTattoo(array $tattoo_images)
  {
    try {
 
      foreach($tattoo_images AS $tattoo_image_id) {
        $this->deleteImageTattoo($tattoo_image_id);
      }

      LogService::info('Deleted ' . count($tattoo_images) . ' tattoo images', $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Delete the image from the tattoo
   *
   * @param int $tattoo_image_id
   * @return bool
  */
  private function deleteImageTattoo(int $tattoo_image_id)
  {
    try {
      if( !$tattoo_image = TattooImage::find($tattoo_images_id)) {
        throw new Exception('Failed to find the tattoo image');
      }

      $fileSerivce = new FileService();
      if(!$file_path = $fileSerivce->delete($tattoo_image->image)) {
        throw new Exception('Failed to delete the tattoo image');
      }

      $tattoo_images->delete();

      LogService::info("Tattoo image $tattoo_image_id deleted", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Create a new tattoo
   *
   * @param object $tattoo
   * @param int $user_id
   * @return object|null
  */
  public function createTattoo(object $tattoo, int $user_id)
  {
    try {
      $fileSerivce = new FileService();
      if(!$file_path = $fileSerivce->create($tattoo->image, 'tattoos')) {
        throw new Exception('Failed to store the tattoo image');
      }

      $new_tattoo = new Tattoo;
      $new_tattoo->title = $tattoo->title;
      $new_tattoo->description = $tattoo->description;
      $new_tattoo->image = $file_path;
      $new_tattoo->save();

      foreach($tattoo->images AS $image) {
        $this->addImageToTattoo($new_tattoo->id, $image);
      }
      
      $this->saveTattooTags($new_tattoo->id, $tattoo->tags);

      LogService::info("Tattoo $new_tattoo->id created by user $user_id", $this->log_file);
      return $new_tattoo;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      $this->deleteTattoo($new_tattoo->id);
      return null;
    }
  }
  
  /**
   * Delete the tattoo
   *
   * @param int $tattoo_id
   * @param int $user_id
   * @return bool
   */
  public function deleteTattoo(int $tattoo_id, int $user_id)
  {
    try {
      if(!$tattoo = $this->getTattooByField('id', $tattoo_id)) {
        throw new Exception('Tattoo not found');
      }
      
      $tattoo->delete();

      LogService::info("Tattoo $tattoo_id has been deleted by $user_id", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Update the tattoo
   *
   * @param object $tattoo
   * @param int $user_id
   * @return bool
  */
  public function updateTattoo(object $tattoo, int $user_id)
  {
    try {
      if(!$tattoo = $this->getTattooByField('id', $tattoo->id)) {
        throw new Exception('Tattoo not found');
      }
      
      $fileSerivce = new FileService();
      if(!$file_path = $fileSerivce->create($tattoo->image, 'tattoos')) {
        throw new Exception('Failed to store the tattoo image');
      }

      $tattoo->title = $tattoo->title;
      $tattoo->description = $tattoo->description;
      $tattoo->image = $file_path;
      $tattoo->save();

      $this->saveTattooTags($tattoo->id, $tattoo->tags);

      LogService::info("Tattoo $tattoo_id has been updated by $user_id", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Save tags for the tattoo
   *
   * @param int $tattoo_id
   * @param array $tags
   * @return bool
   */
  private function saveTattooTags(int $tattoo_id, array $tags)
  {
    try {
      if(!$this->isTattooExists($tattoo_id)) {
        return false;
      }

      $tags_data = [];
      foreach($tags AS $tag) {
        $tags_data[] = [
          'tag_id' => $tag,
          'tattoo_id' => $tattoo_id,
          'created_at' => now()
        ];
      }
      
      TattooTag::insert($tags_data);

      LogService::info("Tattoo $tattoo_id has saved new tags " . json_encode($tags), $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Write a record when a user watches a tattoo for the first time
   *
   * @param int $tattoo_id
   * @param int $user_id
   * @param App\Domain\Users\Services\UserService $userService
   * @return bool
   */
  private function watchTattoo(int $tattoo_id, int $user_id, mixed $userService)
  { 
    try {
      if($userService->isUserWatchedTattoo($user_id, $tattoo_id)) {
        return false;;
      }
      
      if(!$userService->isUserExists($user_id)) {
        return false('User not found');
      }
      
      TattooWatch::create([
        'tattoo_id' => $tattoo_id,
        'user_id' => $user_id,
        'created_at' => now()
      ]);

      Tattoo::where('id', $tattoo_id)->increment('watched');

      LogService::info("Tattoo $tattoo_id watched by user $user_id", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Like or unlike a tattoo
   *
   * @param int $tattoo_id
   * @param int $user_id
   * @param App\Domain\Users\Services\UserService $userService
   * @return bool
   */
  public function likeTattooToggle(int $tattoo_id, int $user_id, mixed $userService)
  {
    try {
      if(!$this->isTattooExists($tattoo_id)) {
        throw new Exception('Tattoo not found');
      }

      if(!$userService->isUserExists($user_id)) {
        throw new Exception('User not found');
      }

      if($userService->isUserLikedTattoo($user_id, $tattoo_id)) {
        $actionSucceed = $this->deleteTattooLike($tattoo_id, $user_id);
      } else {
        $actionSucceed = $this->addTattooLike($tattoo_id, $user_id);
      }

      if(!$actionSucceed) {
        throw new Exception('Failed to toggle tattoo like');
      }
      
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Unlike the tattoo
   *
   * @param int $tattoo_id
   * @param int $user_id
   * @return bool
   */
  private function deleteTattooLike(int $tattoo_id, int $user_id)
  {
    try {
      TattooLike::where('tattoo_id', $tattoo_id)
                  ->where('user_id', $user_id)
                  ->delete();

      Tattoo::where('id', $tattoo_id)->decrement('likes');

      LogService::info("Tattoo $tattoo_id unliked by user $user_id", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Like the tattoo
   *
   * @param int $tattoo_id
   * @param int $user_id
   * @return bool
   */
  private function addTattooLike(int $tattoo_id, int $user_id)
  {
    try {
      TattooLike::create([
        'tattoo_id' => $tattoo_id,
        'user_id' => $user_id,
        'created_at' => now()
      ]);

      Tattoo::where('id', $tattoo_id)->increment('likes');

      LogService::info("Tattoo $tattoo_id like by user $user_id", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * save or unsave a tattoo
   *
   * @param int $tattoo_id
   * @param int $user_id
   * @param App\Domain\Users\Services\UserService $userService
   * @return bool
   */
  public function saveTattooToggle(int $tattoo_id, int $user_id, mixed $userService)
  {
    try {
      if(!$this->isTattooExists($tattoo_id)) {
        throw new Exception('Tattoo not found');
      }

      if(!$userService->isUserExists($user_id)) {
        throw new Exception('User not found');
      }

      if($userService->isUserSavedTattoo($user_id, $tattoo_id)) {
        $actionSucceed = $this->deleteTattooSave($tattoo_id, $user_id);
      } else {
        $actionSucceed = $this->addTattooSave($tattoo_id, $user_id);
      }
      
      if(!$actionSucceed) {
        throw new Exception('Failed to toggle save like');
      }

      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Unsave the tattoo
   *
   * @param int $tattoo_id
   * @param int $user_id
   * @return bool
   */
  private function deleteTattooSave(int $tattoo_id, int $user_id)
  {
    try {
      TattooSave::where('tattoo_id', $tattoo_id)
                ->where('user_id', $user_id)
                ->delete();

      Tattoo::where('id', $tattoo_id)->decrement('saves');

      LogService::info("Tattoo $tattoo_id unsaved by user $user_id", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Save the tattoo
   *
   * @param int $tattoo_id
   * @param int $user_id
   * @return bool
   */
  private function addTattooSave(int $tattoo_id, int $user_id)
  {
    try {
      TattooSave::create([
        'tattoo_id' => $tattoo_id,
        'user_id' => $user_id,
        'created_at' => now()
      ]);

      Tattoo::where('id', $tattoo_id)->increment('saves');

      LogService::info("Tattoo $tattoo_id saved by user $user_id", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
}