<?php

namespace App\Domain\Helpers;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{    
  const DEFAULT_DISK = 'pub';

  /**
   * Create a file
   *
   * @param UploadedFile $file
   * @param string $path
   * @param string $disk
   * @param string $name
   * @return string
  */
  static public function create(UploadedFile $file, string $path, string $disk = self::DEFAULT_DISK, string $name = '') :string
  {
    try {
      if($name) {
        return Storage::disk($disk)->putFileAs($path, $file, $name);
      } else {
        return Storage::disk($disk)->putFile($path, $file);
      }
    } catch(Exception $ex) {
      return '';
    }
  }
  
  /**
   * Delete a file
   *
   * @param string $path
   * @param string $disk
   * @return bool
   */
  static public function delete(string $path, string $disk = self::DEFAULT_DISK) :bool
  {
    try {
      if( !Storage::disk($disk)->exists($path) ) {
        throw new Exception("File $path not found");
      }
      
      Storage::disk($disk)->delete($path);
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
   * @param string $disk
   * @return bool
   */
  static public function move(string $path_from, string $path_to, string $disk = self::DEFAULT_DISK):bool
  {
    try {
      if( !Storage::disk($disk)->exists($path_from) ) {
        throw new Exception("File $path_from not found");
      }
      
      Storage::disk($disk)->move($path_from, $path_to);
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
   * @param string $disk
   * @return bool
   */
  static public function copy(string $path_from, string $path_to, string $disk = self::DEFAULT_DISK) :bool
  {
    try {
      if( !Storage::disk($disk)->exists($path_from) ) {
        throw new Exception("File $path_from not found");
      }
      
      Storage::disk($disk)->copy($path_from, $path_to);
      return true;
    } catch(Exception $ex) {
      return false;
    }
  }
  
  /**
   * Get the content of a file
   *
   * @param string $path
   * @param string $disk
   * @return string
   */
  static public function get(string $path, string $disk = self::DEFAULT_DISK) :string
  {
    try {
      if( !Storage::disk($disk)->exists($path) ) {
        throw new Exception("File $path not found");
      } 

      return Storage::disk($disk)->get($path);
    } catch(Exception $ex) {
      return '';
    }
  }
}