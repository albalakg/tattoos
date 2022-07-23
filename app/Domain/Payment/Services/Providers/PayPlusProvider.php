<?php

namespace App\Domain\Payment\Services\Providers;

use Exception;
use App\Domain\Orders\Models\Order;
use Illuminate\Support\Facades\Http;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Interfaces\IPaymentProvider;

class PayPlusProvider implements IPaymentProvider
{
    const ID = 1;
    
    /**
     * The payload that will be sent to the provider
     *
     * @var array
    */
    private $payment_load = [
        'currency' => 'NIS',
        'quantity' => 1
    ];
    
    /**
     * @return int
    */
    public function getProviderID(): int
    {
        return self::ID;
    }
    
    /**
     * @param int $price
     * @return self
    */
    public function setPrice(int $price): self
    {
        $this->payment_load['price'] = $price;
        return $this;
    }
    
    /**
     * @param int $quantity
     * @return self
    */
    public function setQuantity(int $quantity): self
    {
        $this->payment_load['quantity'] = $quantity;
        return $this;
    }
    
    /**
     * @param string $currency
     * @return self
    */
    public function setCurrency(string $currency): self
    {
        $this->payment_load['currency'] = $currency;
        return $this;
    }
    
    /**
     * @param Order $order
     * @return void
    */
    public function buildPayment(Order $order)
    {
        $this->setPrice($order->price);
        return $this;
    }
    
    /**
     * @return void
    */
    public function startTransaction()
    {
        // return Http::withHeaders([
        //     'Authorization' => config('payment.payplus.token')
        // ])->post(config('payment.payplus.address'), $this->payment_load);        
    }
    
    /**
     * check if the payment is finished successfully
     *
     * @return bool
    */
    public function isValid(): bool
    {
        // add logic for validating..
        return true;
    }
    
    /**
     * @param string $log
     * @return void
    */
    private function info(string $log)
    {
    }
}