<?php

namespace App\Domain\Payment\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Orders\Models\Order;
use App\Domain\Helpers\StatusService;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Services\Providers\PayPlus;
use App\Domain\Payment\Interfaces\IPaymentProvider;

class PaymentService
{
    const PROVIDERS = [
        'visa' => PayPlus::class
    ];

    const PAYMENT_METHODS = [
        'visa'
    ];

    /**
     * @var Payment
    */
    private $payment;

    /**
     * @var Order
    */
    private $order;
    
    /**
     * @var LogService
    */
    private $log_service;
        
    /**
     * @var IPaymentProvider
    */
    private $payment_provider;
    
    /**
     * @param Order $order
     * @param string $provider
     * @return void
    */
    public function __construct(Order $order , string $provider)
    {
        $this->log_service = new LogService('payment');
        $this->order = $order;
        $this->setProvider($provider);
    }

    /**
     * @return void
    */
    public function pay()
    {
        $this->startPayment();

        // add log
        $payment_response = $this->payment_provider
                                ->buildPayment($this->order)
                                ->pay();
        // add log

        $this->updatePayment();
    }
    
    /**
     * find the provider and set it
     *
     * @param string $provider
     * @return void
    */
    private function setProvider(string $provider)
    {
        $provider_class = self::PROVIDERS[$provider];
        $this->payment_provider = new $provider_class;
    }
    
    /**
     * Create a record for the payment
     *
     * @return Payment
    */
    private function startPayment(): Payment
    {
        // add log
        $payment                = new Payment;
        $payment->order_id      = $this->order->id;
        $payment->provider_id   = $this->payment_provider->getProviderID();
        $payment->status        = StatusService::IN_PROGRESS;
        $payment->save();

        // add log
        
        return $this->payment = $payment;
    }
    
    /**
     * update the payment status
     *
     * @return void
    */
    private function updatePayment()
    {
        // add log
        $this->payment->status = $this->payment_provider->isValid() ? StatusService::ACTIVE : StatusService::INACTIVE;
        $this->payment->save();
        // add log
    }
}