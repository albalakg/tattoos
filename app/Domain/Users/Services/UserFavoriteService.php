<?php

namespace App\Domain\Users\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Users\Models\UserFavorite;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\General\Models\LuContentType;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Content\Services\ContentService;

class UserFavoriteService
{
  /**
   * @var LogService
  */
  private $log_service;

  /**
   * @var ContentService|null
  */
  private $content_service;

  /**
   * @var UserService|null
  */
  private $user_service;

  public function __construct(ContentService $content_service = null, UserService $user_service = null)
  {
    $this->content_service = $content_service;
    $this->user_service = $user_service;
    $this->log_service = new LogService('userfavorites');
  }
  
  /**
   * @param int $content_id
   * @param int $user_id
   * @param int $content_type_id
   * @return bool
  */
  public function addToFavorite(int $content_id, int $user_id, int $content_type_id = LuContentType::LESSON): bool
  {
    if($this->isFavoriteContentExists($content_id, $user_id, $content_type_id)) {
      $content_name = LuContentType::CONTENT_TYPES_NAME[$content_type_id];
      throw new Exception("The $content_name is already in the user's favorite list");
    }

    $user_favorite                    = new UserFavorite();
    $user_favorite->user_id           = $user_id;
    $user_favorite->content_id        = $content_id;
    $user_favorite->content_type_id   = $content_type_id;
    $user_favorite->created_at        = now();
    $user_favorite->save();
                                
    return true;
  }

  /**
   * @param int $content_id
   * @param int $user_id
   * @param int $content_type_id
   * @return bool
  */
  public function removeFromFavorite(int $content_id, int $user_id, int $content_type_id = LuContentType::LESSON): bool
  {
    if(!$this->isFavoriteContentExists($content_id, $user_id, $content_type_id)) {
      $content_name = LuContentType::CONTENT_TYPES_NAME[$content_type_id];
      throw new Exception("The $content_name was not found in the user's favorite list");
    }

    $this->baseQuery($content_id, $user_id, $content_type_id)
        ->delete();
                                
    return true;
  }
  
  /**
   * @param Object $user
   * @return Collection
  */
  public function getUserFavoriteContent(Object $user): Collection
  {
    $favorite_content_ids = UserFavorite::where('user_id', $user->id)->pluck('content_id')->toArray();
    return $this->content_service->getLessonsByIds($favorite_content_ids);
  }
  
  /**
   * @param int $content_id
   * @param int $user_id
   * @param int $content_type_id
   * @return bool
  */
  private function isFavoriteContentExists(int $content_id, int $user_id, int $content_type_id = LuContentType::LESSON): bool
  {
    return $this->baseQuery($content_id, $user_id, $content_type_id)->exists();
  }
  
  /**
   * @param int $content_id
   * @param int $user_id
   * @param int $content_type_id
   * @return Builder
  */
  private function baseQuery(int $content_id, int $user_id, int $content_type_id = LuContentType::LESSON): Builder
  {
    return UserFavorite::where('user_id', $user_id)
                      ->where('content_id', $content_id)
                      ->where('content_type_id', $content_type_id);
  }

}