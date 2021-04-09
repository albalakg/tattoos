<?php

namespace App\Domain\Tattoos\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\BaseService;
use App\Domain\Helpers\StatusService;
use App\Domain\Tattoos\Models\Tattoo;
use App\Domain\Tattoos\Models\TattooComment;

class TattooCommentService extends BaseService
{  
  public function __construct()
  {
    $this->setLogFile('tattoo_comments');
  }
  
  /**
   * Checks if the tattoo comment exists
   *
   * @param int $comment_id
   * @return bool
  */
  public function isTattooCommentExists(int $comment_id)
  {
    return TattooComment::where('id', $comment_id)->exists();
  }

  /**
   * Get the tattoo comments
   *
   * @param int $tattoo_id
   * @param int $records
   * @return object|null
  */
  public function getTattooComments(int $tattoo_id, int $records)
  {
    try {
      return TattooComment::where('tattoo_id', $tattoo_id)
                          ->with('user')
                          ->limit($records)
                          ->select('id', 'content', 'user_id')
                          ->simplePaginate();
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return null;
    }
  }

  /**
   * Create a tattoo comment
   *
   * @param int $tattoo_id
   * @param string $content
   * @param int $user_id
   * @param App\Domain\Tattoos\Services\TattooService $tattooService
   * @return object|null
  */
  public function createTattooComment(int $tattoo_id, string $content, int $user_id, App\Domain\Tattoos\Services\TattooService $tattooService)
  {
    try {
      if(!$tattooService->isTattooExists($tattoo_id)) {
        throw new Exception('Tattoo not found');
      }

      $comment = TattooComment::create([
        'tattoo_id' => $tattoo_id,
        'user_id' => $user_id,
        'content' => $content,
        'status' => StatusService::ACTIVE,
      ]);
      
      LogService::info("Comment $comment->id created succesfully to tattoo $tattoo_id by $user_id", $this->log_file);
      return $comment;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return null;
    }
  }
  
  /**
   * Update the status of the comment
   *
   * @param int $comment_id
   * @param int $status
   * @param int $updated_by
   * @return bool
  */
  public function updateTattooCommentStatus(int $comment_id, int $status, int $updated_by)
  {
    try {
      if(!$this->isTattooCommentExists($comment_id)) {
        throw new Exception('Tattoo comment not found');
      }

      TattooComment::where('id', $comment_id)
                    ->update([
                      'status' => $status
                    ]);
      
      LogService::info("Comment $comment_id status updated by user $updated_by", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Update the comment content
   *
   * @param int $comment_id
   * @param string $content
   * @param int $updated_by
   * @return bool
  */
  public function updateTattooCommentContent(int $comment_id, string $content, int $updated_by)
  {
    try {
      if(!$this->isTattooCommentExists($comment_id)) {
        throw new Exception('Tattoo comment not found');
      }

      TattooComment::where('id', $comment_id)->update([
        'content' => $content
      ]);
      
      LogService::info("Comment $comment_id content updated by user $updated_by", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Delete the comment
   *
   * @param int $comment_id
   * @param string $content
   * @param int $deleted_by
   * @return bool
  */
  public function deleteTattooComment(int $comment_id, int $deleted_by)
  {
    try {
      if(!$this->isTattooCommentExists($comment_id)) {
        throw new Exception('Tattoo comment not found');
      }

      TattooComment::where('id', $comment_id)->delete();
      
      LogService::info("Comment $comment_id deleted by user $deleted_by", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
}