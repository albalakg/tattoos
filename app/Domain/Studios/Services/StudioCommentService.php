<?php

namespace App\Domain\Studios\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\BaseService;
use App\Domain\Studios\Models\StudioComment;


class StudioCommentService extends BaseService
{  
  public function __construct()
  {
    $this->setLogFile('studio_comments');
  }
  
  /**
   * Checks if the studio comment exists
   *
   * @param int $comment_id
   * @return bool
  */
  public function isStudioCommentExists(int $comment_id)
  {
    return StudioComment::where('id', $comment_id)->exists();
  }

  /**
   * Get comment by a studio
   *
   * @param int $studio_id
   * @param int $records
   * @return object|null
  */
  public function getStudioComments(int $studio_id, int $records)
  {
    try {
      return StudioComment::where('studio_id', $studio_id)
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
   * Create a studio comment
   *
   * @param int $studio_id
   * @param string $content
   * @param int $user_id
   * @param mixed $studioService
   * @return object|null
  */
  public function createStudioComment(int $studio_id, string $content, int $user_id, mixed $studioService)
  {
    try {
      if(!$studioService->isStudioExists($studio_id)) {
        throw new Exception('Studio not found');
      }

      $comment = StudioComment::create([
        'studio_id' => $studio_id,
        'user_id' => $user_id,
        'content' => $content,
        'status' => StatusService::ACTIVE,
      ]);
      
      LogService::info("Comment $comment->id created succesfully to studio $studio_id by $user_id", $this->log_file);
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
  public function updateStudioCommentStatus(int $comment_id, int $status, int $updated_by)
  {
    try {
      if(!$this->isStudioCommentExists($comment_id)) {
        throw new Exception('Studio comment not found');
      }

      StudioComment::where('id', $comment_id)
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
  public function updateStudioCommentContent(int $comment_id, string $content, int $updated_by)
  {
    try {
      if(!$this->isStudioCommentExists($comment_id)) {
        throw new Exception('Studio comment not found');
      }

      StudioComment::where('id', $comment_id)->update([
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
  public function deleteStudioComment(int $comment_id, int $deleted_by)
  {
    try {
      if(!$this->isStudioCommentExists($comment_id)) {
        throw new Exception('Studio comment not found');
      }

      StudioComment::where('id', $comment_id)->delete();
      
      LogService::info("Comment $comment_id deleted by user $deleted_by", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
      
  /**
   * Delete all the comments of the studio when a studio is deleted
   *
   * @param int $studio_id
   * @param int $deleted_by
   * @return bool
  */
  public function deleteStudioCommentByStudio(int $studio_id, int $deleted_by)
  {
    try {
      StudioComment::where('studio_id', $studio_id)->delete();
      
      LogService::info("Comment of studio $studio_id deleted by user $deleted_by", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error("Failed to delete comments of studio: $studio_id" . $ex->getMessage(), $this->log_file);
      return false;
    }
  }
}