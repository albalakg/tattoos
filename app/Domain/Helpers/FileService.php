<?php

namespace App\Domain\Helpers;

use Exception;
use Illuminate\Http\UploadedFile;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileService
{    
  const DEFAULT_DISK  = 'pub';
  const S3_DISK       = 's3';

  /**
   * Create a file
   *
   * @param string|UploadedFile $file
   * @param string $path
   * @param string $disk
   * @param string $name
   * @return string
  */
  static public function create($file, string $path, string $disk = self::DEFAULT_DISK) :string
  {
    try {
      if(!$file) {
        throw new Exception('File is invalid');
      }

      if(is_string($file)) {
        return self::copy($file, $path, 'local');
      } else {
        return Storage::disk($disk)->putFile($path, $file);
      }

    } catch(Exception $ex) {
      self::writeErrorLog($ex);
      return '';
    }
  }

  /**
   * Create a file
   *
   * @param string|UploadedFile $file
   * @param string $path
   * @param string $disk
   * @param string $name
   * @return string
  */
  static public function createWithName(mixed $file, string $path, string $name, string $disk = self::DEFAULT_DISK) :string
  {
    try {
      return Storage::disk($disk)->putFileAs($path, $file, $name);
    } catch(Exception $ex) {
      self::writeErrorLog($ex);
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
      self::writeErrorLog($ex);
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
      self::writeErrorLog($ex);
      return false;
    }
  }

  /**
   * Copy the file
   *
   * @param string $path_from
   * @param string $path_to
   * @param string $from_disk
   * @param string $to_disk
   * @return string
   */
  static public function copy(string $path_from, string $path_to, string $from_disk = self::DEFAULT_DISK, string $to_disk = self::DEFAULT_DISK) :string
  {
    try {
      if( !Storage::disk($from_disk)->exists($path_from) ) {
        throw new Exception("File $path_from not found");
      }

      $file = Storage::disk($from_disk)->path($path_from);
      return Storage::disk($to_disk)->putFile($path_to , $file);
    } catch(Exception $ex) {
      self::writeErrorLog($ex);
      return '';
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
      self::writeErrorLog($ex);
      return '';
    }
  }
  
  /**
   * Check if the file exists
   *
   * @param string $path
   * @param string $disk
   * @return bool
   */
  static public function exists(string $path, string $disk = self::DEFAULT_DISK): bool
  {
    try {
      return Storage::disk($disk)->exists($path);
    } catch(Exception $ex) {
      self::writeErrorLog($ex);
      return false;
    }
  }
  
  /**
   * Extract the file extension from the uploaded file
   *
   * @param object $file
   * @return string
  */
  static public function getUploadedFileExtension(object $file): string
  {
    $file_name          = $file->getClientOriginalName();
    $file_name_array    = explode('.', $file_name);
    return $file_name_array[count($file_name_array) - 1];
  }
  
  /**
   * Get the basename of the file from the path
   *
   * @param string $file_path
   * @return string
  */
  static public function getLogFileName(string $file_path): string
  {
      return basename($file_path);
  }

  static private function writeErrorLog(Exception $ex)
  {
    $logger_service = new LogService('files');
    $logger_service->critical($ex);
  }

}