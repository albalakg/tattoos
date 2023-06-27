<?php
namespace App\Domain\Orders\Services;

use Exception;
use Illuminate\Support\Str;
use App\Domain\Helpers\LogService;
use App\Domain\Orders\Models\MarketingToken;
use App\Domain\Orders\Services\OrderService;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Content\Services\ContentService;
use App\Domain\Helpers\StatusService;

class MarketingTokenService
{
  private LogService $log_service;

  private ?OrderService $order_service;

  private ?ContentService $content_service;

  public function __construct(
    OrderService $order_service = null,
    ContentService $content_service = null
    )
  {
    $this->order_service    = $order_service;
    $this->content_service  = $content_service;
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
                  'course_id',
                  'name',
                  'email',
                  'phone',
                  'token',
                  'fee',
                  'status',
                  'created_at'
                )
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
    $marketing_token->course_id   = $data['course_id'];
    $marketing_token->fee         = $data['fee'];
    $marketing_token->name        = $data['name'];
    $marketing_token->email       = $data['email'];
    $marketing_token->phone       = $data['phone'];
    $marketing_token->status      = StatusService::PENDING;
    $marketing_token->token       = $this->generateToken();
    $marketing_token->created_by  = $created_by;
    $marketing_token->save();

  $this->log_service->info('Marketing link has been created', $marketing_token->toArray());
    return $marketing_token;
  }
   
  /**
   * @param array $data
   * @return void
  */
  public function update(array $data)
  {
    $marketing_token = MarketingToken::find($data['id']);
    if(!$marketing_token) {
      throw new Exception('Marketing link not found');
    }

    $marketing_token->name      = $data['name'];
    $marketing_token->email     = $data['email'];
    $marketing_token->phone     = $data['phone'];
    $marketing_token->status    = $data['status'];
    $marketing_token->save();

    $this->log_service->info('Marketing link has been updated', $marketing_token->toArray());
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
        $this->log_service->info('Marketing link has been deleted', ['id' => $id]);
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
        $this->log_service->info('Marketing link has been forced deleted', ['id' => $id]);
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