<?php
namespace App\Domain\Orders\Services;

use Exception;
use Illuminate\Support\Str;
use Ramsey\Collection\Collection;
use App\Domain\Helpers\LogService;
use App\Domain\Users\Services\UserService;
use App\Domain\Orders\Models\MarketingToken;
use App\Domain\Orders\Services\OrderService;


class MarketingTokenService
{
  /**
   * @var LogService
  */
  private $log_service;

  /**
   * @var OrderService|null
  */
  private $order_service;

  public function __construct(OrderService $order_service = null)
  {
    $this->order_service    = $order_service;
    $this->log_service      = new LogService('marketingToken');
  }
  
  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return MarketingToken::orderBy('id', 'desc')
                ->with('orders')
                ->get();
  }

  /**
   * @param string|null $token
   * @return MarketingToken|null
  */
  public function getMarketingTokenByToken(?string $token): ?MarketingToken
  {
    if(strlen($token) !== MarketingToken::TOKEN_LENGTH) {
      return null;
    }

    return MarketingToken::where('token', $token)->first();
  }
    
  /**
   * @param array $data
   * @param int $created_by
   * @return void
  */
  public function create(array $data, int $created_by)
  {
    $marketing_token              = new MarketingToken();
    $marketing_token->email       = $data['email'];
    $marketing_token->discount    = $data['discount'];
    $marketing_token->token       = $this->generateToken();
    $marketing_token->created_by  = $created_by;
    $marketing_token->save();

    $this->log_service->info('Marketing token has been created: ' . json_encode($marketing_token));
    return $marketing_token;
  }
   
  /**
   * @param array $data
   * @return void
  */
  public function update(array $data)
  {
    $marketing_token              = MarketingToken::find($data['id']);
    $marketing_token->discount    = $data['discount'];
    $marketing_token->email       = $data['email'];
    $marketing_token->save();

    $this->log_service->info('Marketing token has been updated: ' . json_encode($marketing_token));
    return $marketing_token;
  }
  
  /**
   * Soft delete the item 
   * @param int $id
   * @return bool
  */
  public function delete(int $id): bool
  {
    $result = MarketingToken::where('id', $id)->delete();
    $this->log_service->info('Marketing Token ' . $id . ' has been deleted');
    return $result;
  }
  
  /**
   * @param int $id
   * @return bool
  */
  public function forceDelete(int $id): bool
  {
    $result = MarketingToken::where('id', $id)->forceDelete();
    $this->log_service->info('Marketing Token ' . $id . ' has been forced deleted');
    return $result;
  }
  
  /**
   * @return string
  */
  private function generateToken(): ?string
  {
    return Str::random(MarketingToken::TOKEN_LENGTH);
  }
}