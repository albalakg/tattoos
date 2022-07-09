<?php
namespace App\Domain\Tags\Services;

use App\Domain\Tags\Models\Tag;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\BaseService;
use App\Domain\Helpers\StatusService;

class TagService extends BaseService
{
  public function __construct()
  {
    $this->setLogFile('tags');
  }
  
  /**
   * Create a tag
   *
   * @param string $tag_name
   * @param int $created_by
   * @return object
  */
  public function createTag(string $tag_name, int $created_by) :object
  {
    try {
      if($this->isTagExists($tag_name)) {
        return $this->error('Tag already exists');
      }

      if( !$this->tagNameIsValid($tag_name) ) {
        throw new Exception("Tag $tag_name is invalid");
      }

      $tag = Tag::create([
        'name' => $tag_name,
        'status' => StatusService::ACTIVE,
        'created_by' => $created_by
      ]);


      LogService::info("Created $tag_name successfully by $created_by", $this->log_file);
      return $this->success('Created tag', $tag);
    } catch(Exception $ex) {
      LogService::error($ex->__toString(), $this->log_file);
      return $this->error("Tag $tag failed to be created by user $created_by");
    }
  }
  
  /**
   * Checks if the tag is valid
   *
   * @return bool
   */
  private function tagNameIsValid(string $tag_id) :bool
  {
    try {
      // TODO: check if tag is valid

      return true;
    } catch(Exception $ex) {
      return false;
    }
  }
  
  /**
   * Update the tag status
   *
   * @param int $tag_id
   * @param int $status
   * @param int $created_by
   * @return object
  */
  public function updateTagStatus(int $tag_id, int $status, int $created_by) :object
  {
    try {
      if(!$this->isTagExists($tag_name)) {
        return $this->notFound('Tag not found');
      }

      Tag::where('id', $tag_id)
          ->update([
            'status' => $status
          ]);

      LogService::info("Tag $tag_name status updated successfully to $status by $created_by", $this->log_file);
      return $this->success('Updated tag status');
    } catch(Exception $ex) {
      LogService::error($ex->__toString(), $this->log_file);
      return $this->error("Tag $tag, failed to be updated to status $status");
    }
  }
  
  /**
   * Delete the tag
   *
   * @param int $tag_id
   * @param int $created_by
   * @return object
  */
  public function deleteTag(int $tag_id, int $created_by) :object
  {
    try {
      if(!$tag = Tag::find($tag_id)) {
        return $this->notFound('Tag not found');
      }

      $tag->delete();

      LogService::info("Tag $tag->name deleted by $created_by", $this->log_file);
      return $this->success('Deleted the tag');
    } catch(Exception $ex) {
      LogService::error($ex->__toString(), $this->log_file);
      return $this->error("Tag $tag->name, failed to be deleted");
    }
  }
  
  /**
   * Checks if the tag exists by the content
   *
   * @param string $tag_name
   * @return bool
   */
  public function isTagExists(string $tag_name) :bool
  {
    return Tag::where('name', $tag_name)->exists();
  }   
}