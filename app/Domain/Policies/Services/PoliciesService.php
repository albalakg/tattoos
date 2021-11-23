<?php
namespace App\Domain\Orders\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Orders\Models\PolicyTermsAndCondition;
use App\Domain\Users\Services\UserService;
use Illuminate\Database\Eloquent\Collection;

class PoliciesService
{
  /**
   * @var LogService
  */
  private $log_service;

  /**
   * @var UserService|null
  */
  private $user_service;

  public function __construct(UserService $user_service = null)
  {
    $this->user_service = $user_service;
    $this->log_service = new LogService('policies');
  }
  
  /**
   * @return Collection
  */
  public function getTermsAndConditions(): Collection
  {
    return PolicyTermsAndCondition::orderBy('id', 'desc')
                ->get();
  }
  
  /**
   * @return Collection
  */
  public function getUsersVerifications(): Collection
  {
    return PolicyTermsAndCondition::orderBy('id', 'desc')
                ->get();
  }
}