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
     * @param Order $order
     * @param string $provider
     * @return void
     */
    public function __construct(Order $order, string $provider)
    {
        $this->log_service  = new LogService('payment');
        $this->order        = $order;
        $this->setProvider($provider);
    }

    /**
     * @return array;
     */
    public function startTransaction(): array
    {
        $this->log_service->info('Starting the order\'s process', ['order_id' => $this->order->id]);
        $this->payment_provider->buildPayment($this->order);
        $this->payment_provider->startTransaction();
        if (!$this->payment_provider->isValid()) {
            throw new Exception('The transaction failed in the order process');
        }

        $this->updatePaymentOrder($this->payment_provider->getTransactionResponse());
        $this->log_service->info('Finished the order\'s process successfully', ['order_id' => $this->order->id]);
        return [
            'token' => $this->payment_provider->getGeneratedPageToken(),
            'link'  => $this->payment_provider->getGeneratedPageLink() 
        ];
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

    /**
     * update the payment status
     *
     * @param Object $payment_response
     * @return void
     */
    private function updatePaymentOrder(Object $payment_response)
    {
        $this->order->supplier_id   = $this->payment_provider->getProviderID();
        $this->order->token         = $payment_response->data->page_request_uid;
        $this->order->save();
        $this->log_service->info('Order updated payment', ['order_id' => $this->order->id]);
    }
}
