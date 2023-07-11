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

    private Order $order;

    private LogService $log_service;

    private IPaymentProvider $payment_provider;

    /**
     * @param string $provider
     * @return void
     */
    public function __construct(string $provider)
    {
        $this->log_service  = new LogService('payment');
        $this->setProvider($provider);
    }

    /**
     * @param Order $order
     * @return array;
     */
    public function startTransaction(Order $order): array
    {
        $this->order = $order;
        $this->log_service->info('Starting the order\'s process', ['order_id' => $this->order->id]);
        $this->payment_provider->buildPayment($this->order);
        $this->payment_provider->startTransaction();
        if (!$this->payment_provider->isTransactionValid()) {
            throw new Exception('The transaction failed in the order process');
        }

        $this->log_service->info('Finished the order\'s process successfully', ['order_id' => $this->order->id]);
        return [
            'token'         => $this->payment_provider->getGeneratedPageToken(),
            'link'          => $this->payment_provider->getGeneratedPageLink(),
            'supplier_id'   => $this->payment_provider->getProviderID()
        ];
    }
        
    /**
     * @param Order $order
     * @return void
    */ 
    public function sendInvoice(Order $order)
    {
        $this->payment_provider->sendInvoice($order);
        if (!$this->payment_provider->isInvoiceValid()) {
            throw new Exception('The invoice failed in the order process');
        }
    }
    
    /**
     * @param array $response
     * @return bool
    */
    public function isPaymentCallbackValid(array $response): bool
    {
        try {
            $this->payment_provider->isPaymentCallbackValid($response);
            return true;
        } catch(Exception $ex) {
            $this->log_service->critical($ex);
            return false;
        }
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
        } catch (Exception $ex) {
            $this->log_service->error($ex);
            throw new Exception('Failed to set provider with: ' . $provider);
        }
    }
}
