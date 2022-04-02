<?php

namespace App\Domain\Payment\Services\Providers;

use App\Domain\Helpers\LogService;
use App\Domain\Helpers\StatusService;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Interfaces\IPaymentProvider;
use Exception;

class CreditGuard implements IPaymentProvider
{
    const ID = 1;
    
    /**
     * The payment data
     *
     * @var object
    */
    private $payment;
    
    /**
     * @return int
    */
    public function getProviderID(): int
    {
        return self::ID;
    }
    
    /**
     * @return Payment
    */
    public function getPayment(): Payment
    {
        return $this->payment;
    }
    
    /**
     * @param int $price
     * @return self
    */
    public function setPrice(int $price): self
    {
        return $this;
    }
    
    /**
     * @param int $quantity
     * @return self
    */
    public function setQuantity(int $quantity = 1): self
    {
        return $this;
    }
    
    /**
     * @param string $currency
     * @return self
    */
    public function setCurrency(string $currency = 'NIS'): self
    {
        return $this;
    }
    
    /**
     * @return void
    */
    public function pay()
    {
    }
    
    /**
     * @return bool
    */
    public function isValid(): bool
    {
        return true;
    }
    
    /**
     * @param string $log
     * @return void
    */
    private function info(string $log)
    {
        $this->log_service->info('Payment ID: ' . $this->payment->id . $log . ' | ');
    }
}