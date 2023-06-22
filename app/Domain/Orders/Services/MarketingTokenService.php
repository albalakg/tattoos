<?php
namespace App\Domain\Orders\Services;

use Illuminate\Support\Str;
use App\Domain\Helpers\LogService;
use App\Domain\Orders\Models\MarketingToken;
use App\Domain\Orders\Services\OrderService;
use Exception;
use Illuminate\Database\Eloquent\Collection;


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
                ->select(
                  'id',
                  'name',
                  'email',
                  'phone',
                  'token',
                  'discount',
                  'created_at'
                )
                ->get();
  }
  
  /**
   * @param string $token
   * @return ?MarketingToken
  */
  public function getByToken(string $token): ?MarketingToken
  {
    return MarketingToken::where('token', $token)
                        ->select(
                          'discount',
                        )
                        ->first();
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
    $marketing_token->discount    = $data['discount'];
    $marketing_token->name        = $data['name'];
    $marketing_token->email       = $data['email'];
    $marketing_token->phone       = $data['phone'];
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
    $marketing_token->name        = $data['name'];
    $marketing_token->email       = $data['email'];
    $marketing_token->phone       = $data['phone'];
    $marketing_token->save();

    $this->log_service->info('Marketing token has been updated: ' . json_encode($marketing_token));
    return $marketing_token;
  }
  
  /**
   * Soft delete the item 
   * @param array $ids
   * @return bool
  */
  public function delete(array $ids): bool
  {
    foreach ($ids as $id) {
      try {
        $this->log_service->info('Marketing Token ' . $id . ' has been deleted');
        MarketingToken::where('id', $id)->delete();
      } catch(Exception $ex) {
        $this->log_service->error($ex);
      }
    }
    return true;
  }
  
  /**
   * @param array $ids
   * @return bool
  */
  public function forceDelete(array $ids): bool
  {
    foreach ($ids as $id) {
      try {
        $result = MarketingToken::where('id', $id)->forceDelete();
        $this->log_service->info('Marketing Token ' . $id . ' has been forced deleted');
      } catch(Exception $ex) {
        $this->log_service->error($ex);
      }
    }
    return true;
  }
  
  /**
   * @return string
  */
  private function generateToken(): ?string
  {
    return Str::random(MarketingToken::TOKEN_LENGTH);
  }
}