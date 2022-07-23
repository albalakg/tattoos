<?php
namespace App\Domain\Policies\Services;

use App\Domain\Helpers\LogService;
use App\Domain\Users\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Policies\Models\PolicyUserVerification;
use App\Domain\Policies\Models\PolicyTermsAndCondition;

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
   * @return null|PolicyTermsAndCondition
  */
  public function getCurrentTermsAndConditions(): ?PolicyTermsAndCondition
  {
    return PolicyTermsAndCondition::orderBy('id', 'desc')
                ->first();
  }
  
  /**
   * @param int $user_id
   * @return Collection
  */
  public function getUsersVerifications(int $user_id): Collection
  {
    return PolicyUserVerification::where('user_id', $user_id)
                ->orderBy('id', 'desc')
                ->get();
  }

  public function create(array $data, int $created_by): PolicyTermsAndCondition
  {
    $tnc              = new PolicyTermsAndCondition();
    $tnc->content     = $data['content'];
    $tnc->created_by  = $created_by;
    $tnc->save();

    return $tnc;
  }
  
  /**
   * @param int $user_id
   * @param int $tnc_id
   * @return void
  */
  public function verifyTermsAndConditions(int $user_id, int $tnc_id)
  {
    $tnc_verification             = new PolicyUserVerification();
    $tnc_verification->user_id    = $user_id;
    $tnc_verification->tnc_id     = $tnc_id;
    $tnc_verification->created_at = now();
    $tnc_verification->save();
  }
}