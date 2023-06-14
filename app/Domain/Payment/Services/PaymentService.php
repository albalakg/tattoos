<?php

namespace App\Domain\Payment\Services;

use App\Domain\Helpers\LogService;
use App\Domain\Orders\Models\Order;
use App\Domain\Payment\Services\Providers\PayPlusProvider;
use App\Domain\Payment\Interfaces\IPaymentProvider;
use Exception;

class PaymentService
{
    const PROVIDERS = [
        'visa' => PayPlusProvider::class
    ];

    const PAYMENT_METHODS = [
        'visa'
    ];

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
    public function startTransaction()
    {
        $this->startPayment();

        $this->log_service->info(('Order ' . $this->order->id . ' starting building payment'));
        $this->payment_provider->buildPayment($this->order);

        $this->log_service->info(('Order ' . $this->order->id . ' starting transaction'));
        $payment_response = $this->payment_provider->startTransaction();
        $this->log_service->info(('Order ' . $this->order->id . ' finished transaction'));

        // $this->updatePayment($payment_response);
    }
    
    /**
     * find the provider and set it
     *
     * @param string $provider
     * @return void
    */
    private function setProvider(string $provider)
    {
        try {
            $provider_class = self::PROVIDERS[$provider];
            $this->payment_provider = new $provider_class;
        } catch(Exception $ex) {
            throw new Exception('Failed to set provider with: ' . $provider);
        }
    }
    
    /**
     * Create a record for the payment
     *
     * @return void
    */
    private function startPayment()
    {
        $this->log_service->info(('Order ' . $this->order->id . ' starting payment'));
        $this->order->supplier_id = $this->payment_provider->getProviderID();
        $this->order->save();
    }
    
    /**
     * update the payment status
     *
     * @param Object $payment_response
     * @return void
    */
    private function updatePayment(Object $payment_response)
    {
        $this->order->token = $payment_response->token;
        $this->order->save();
        $this->log_service->info(('Order ' . $this->order->id . ' updated payment'));
    }
}