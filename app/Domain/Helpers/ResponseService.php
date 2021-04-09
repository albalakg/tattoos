<?php

namespace App\Domain\Helpers;

class ResponseService
{
  const SUCCESS      = 200,
        ERROR        = 400,
        FORBIDDEN    = 403,
        UNAUTHORIZED = 401,
        NOT_FOUND    = 404,
        VALIDATION   = 422,
        STATUS_SUCCESS = 'Succeed',
        STATUS_FAIL = 'Failed';
  
  /**
   * Returns a success response
   * When the action completed successfully
   *
   * @param string $message
   * @param mixed $data
   * @param int $status
   * @return array
  */
  public function success(string $message = 'Action finished successfully', mixed $data = null, int $status = self::SUCCESS) :array
  {
    return [
      'http_status' => $status,
      'response' => [
        'status' => self::STATUS_SUCCESS,
        'message' => $message,
        'data' => $data
      ]
    ];
  }

  /**
   * Returns an error response
   * When the action failed to run completely
   *
   * @param string $message
   * @param mixed $data
   * @param int $status
   * @return array
  */
  public function error(string $message = 'Action failed', mixed $data = null, int $status = self::ERROR) :array
  {
    return [
      'http_status' => $status,
      'response' => [
        'status' => self::STATUS_FAIL,
        'message' => $message,
        'data' => $data
      ]
    ];
  }

  /**
   * Returns a forbidden response
   * When a user is forbidden
   *
   * @param mixed $data
   * @param string $message
   * @return array
  */
  public function forbidden(mixed $data = null, string $message = 'Action forbidden') :array
  {
    return [
      'http_status' => self::FORBIDDEN,
      'response' => [
        'status' => self::STATUS_FAIL,
        'message' => $message,
        'data' => $data
      ]
    ];
  }

  /**
   * Returns a unauthorized response
   * When a user is not authorized to do a specefic action
   *
   * @param mixed $data
   * @param string $message
   * @return array
  */
  public function unauthorized(mixed $data = null, string $message = 'Action forbidden') :array
  {
    return [
      'http_status' => self::UNAUTHORIZED,
      'response' => [
        'status' => self::STATUS_FAIL,
        'message' => $message,
        'data' => $data
      ]
    ];
  }

  /**
   * Returns a notfound response
   * Incase of a record was not found
   *
   * @param mixed $data
   * @param string $message
   * @return array
  */
  public function notFound(string $message = 'Data not found', mixed $data = null) :array
  {
    return [
      'http_status' => self::NOT_FOUND,
      'response' => [
        'status' => self::STATUS_FAIL,
        'message' => $message,
        'data' => $data
      ]
    ];
  }

  /**
   * Returns a validation error response
   * Incase that found an error from the user request
   *
   * @param mixed $data
   * @param string $message
   * @return array
  */
  public function validation(string $message = 'Validation error', mixed $data = null) :array
  {
    return [
      'http_status' => self::VALIDATION,
      'response' => [
        'status' => self::STATUS_FAIL,
        'message' => $message,
        'data' => $data
      ]
    ];
  }
  
  /**
   * Checks if the response is valid
   *
   * @param array $response
   * @return bool
   */
  protected function responseIsSuccess(array $response) :bool
  {
    if( isset($response['response']) && isset($response['response']['status']) ) {
      return $response['response']['status'] === self::STATUS_SUCCESS;
    }
    return false;
  }

}