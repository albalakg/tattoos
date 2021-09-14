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

      if($name) {
        return Storage::disk($this->disk)->putFileAs($path, $file, $name);
      } else {
        return Storage::disk($this->disk)->putFile($path, $file);
      }
    } catch(Exception $ex) {
      return '';
    }
  }
  
  /**
   * Delete a file
   *
   * @param string $path
   * @return bool
   */
  public function delete(string $path) :bool
  {
    try {

      if( !Storage::disk($this->disk)->exists($path) ) {
        throw new Exception("File $path not found");
      }
      
      Storage::disk($this->disk)->delete($path);
      return true;
    } catch(Exception $ex) {
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

      if( !Storage::disk($this->disk)->exists($path_from) ) {
        throw new Exception("File $path_from not found");
      }
      
      Storage::disk($this->disk)->move($path_from, $path_to);
      return true;
    } catch(Exception $ex) {
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
      if( !Storage::disk($this->disk)->exists($path_from) ) {
        throw new Exception("File $path_from not found");
      }
      
      Storage::disk($this->disk)->copy($path_from, $path_to);
      return true;
    } catch(Exception $ex) {
      return false;
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
      if( !Storage::disk($this->disk)->exists($path) ) {
        throw new Exception("File $path not found");
      } 

      return Storage::disk($this->disk)->get($path);
    } catch(Exception $ex) {
      return '';
    }
  }
}