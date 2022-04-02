<?php

namespace App\Domain\Payment\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\StatusService;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Interfaces\IPaymentProvider;
use App\Domain\Payment\Services\Providers\CreditGuard;

class PaymentService
{
    const PROVIDERS = [
        'creditGuard' => CreditGuard::class
    ];

    /**
     * @var Payment
    */
    private $payment;
    
    /**
     * @var LogService
    */
    private $log_service;
        
    /**
     * @var IPaymentProvider
    */
    private $payment_provider;
    
    /**
     * @param int $order_id
     * @param string $provider
     * @return void
    */
    public function __construct(int $order_id , string $provider)
    {
        $this->log_service = new LogService('payment');
        $this->setProvider($provider);
        $this->startPayment($order_id);
    }
    
    /**
     * @param int $order_id
     * @return void
    */
    public function startPayment(int $order_id)
    {
        $payment                = new Payment;
        $payment->order_id      = $order_id;
        $payment->provider_id   = $this->payment_provider->getProviderID();
        $payment->status        = StatusService::IN_PROGRESS;
        $payment->save();

        $this->payment = $payment;
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
}