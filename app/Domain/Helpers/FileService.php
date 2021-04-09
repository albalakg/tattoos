<?php

namespace App\Domain\Helpers;

use Exception;
use App\Domain\Helpers\BaseService;
use Illuminate\Support\Facades\Storage;

class FileService extends BaseService
{    
  /**
   * The file disk
   *
   * @var string
   */
  private $disk;
  
  /**
   * Set the file service variables
   *
   * @param string $disk
   * @return void
   */
  public function __construct(string $disk = '')
  {
    $this->log_file = 'files';
    $this->disk = $disk ?? 'public';
  }
  /**
   * Create a file
   *
   * @param mixed $file
   * @param string $path
   * @param string $name
   * @return string
   */
  public function create(mixed $file, string $path, string $name = '') :string
  {
    try {
      LogService::info("Attempting to create a file in path \"$path\", with name \"$name\"", $this->log_file);

      if($name) {
        return Storage::disk($this->disk)->putFileAs($path, $file, $name);
      } else {
        return Storage::disk($this->disk)->putFile($path, $file);
      }
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return '';
    }
  }
  
  /**
   * Delete a file
   *
   * @param string $path
   * @return bool
   */
  private function delete(string $path) :bool
  {
    try {
      LogService::info("Attempting to delete a file in path \"$path\"", $this->log_file);

      if( !Storage::disk($this->disk)->exists($path) ) {
        throw new Exception("File $path not found");
      }
      
      Storage::disk($this->disk)->delete($path);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Move a file to a new location
   *
   * @param string $path_from
   * @param string $path_to
   * @return bool
   */
  public function move(string $path_from, string $path_to):bool
  {
    try {
      LogService::info("Attempting to move a file from \"$path\" to \"$path_to\"", $this->log_file);

      if( !Storage::disk($this->disk)->exists($path_from) ) {
        throw new Exception("File $path_from not found");
      }
      
      Storage::disk($this->disk)->move($path_from, $path_to);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Copy the file
   *
   * @param string $path_from
   * @param string $path_to
   * @return bool
   */
  public function copy(string $path_from, string $path_to) :bool
  {
    try {
      LogService::info("Attempting to copy a file from \"$path\" to \"$path_to\"", $this->log_file);
      if( !Storage::disk($this->disk)->exists($path_from) ) {
        throw new Exception("File $path_from not found");
      }
      
      Storage::disk($this->disk)->copy($path_from, $path_to);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Rename a file
   *
   * @param string $path
   * @param string $new_name
   * @return bool
   */
  public function rename(string $path, string $new_name) :bool
  {
    try {
      LogService::info("Attempting to rename the file \"$path\" to \"$new_name\"", $this->log_file);
      if( !Storage::disk($this->disk)->exists($path) ) {
        throw new Exception("File $path not found");
      } 
      
      $new_path = $this->changeFileName($path, $new_name);
      if(!$new_path) {
        throw new Exception("Failed to rename the file from: $path, to the new name: $new_name");
      }

      Storage::disk($this->disk)->move($path, $new_path);
      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Change the file name in the full path to the new name
   *
   * @param string $path
   * @param string $new_name
   * @return string
   */
  private function changeFileName(string $path, string $new_name) :string
  {
    try {
      $path_array = explode('/', $path);
      $last_element_index = count($path_array) - 1;
      $path_array[$last_element_index] = $new_name;
      return implode('/', $path);
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return '';
    }
  }
   
  /**
   * Get the content of a file
   *
   * @param string $path
   * @return string
   */
  public function get(string $path) :string
  {
    try {
      LogService::info("Attempting to get the file \"$path\"", $this->log_file);
      if( !Storage::disk($this->disk)->exists($path) ) {
        throw new Exception("File $path not found");
      } 

      return Storage::disk($this->disk)->get($path);
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), $this->log_file);
      return '';
    }
  }
}