<?php

namespace App\Domain\Payment\Services;

use App\Domain\Helpers\LogService;
use App\Domain\Helpers\StatusService;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Interfaces\IPaymentSupplier;

class PaymentService
{
    /**
     * @var Payment
    */
    private $payment;
    
    /**
     * @var LogService
    */
    private $log_service;
    
    /**
     * @var IPaymentSupplier
    */
    private $payment_supplier;
    
    /**
     * @param int $order_id
     * @param IPaymentSupplier $payment_supplier
     * @return void
    */
    public function __construct(int $order_id , IPaymentSupplier $payment_supplier)
    {
        $this->log_service = new LogService('payment');
        $this->payment_supplier = $payment_supplier;
        $this->startPayment($order_id);

        $this->log_service->info('Payment ID: ' . $this->payment->id . ' | Set Supplier: ' . $payment_supplier);
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
        $this->payment_supplier->setPrice($price);
        $this->log_service->info('Payment ID: ' . $this->payment->id . ' | Set Price: ' . $price);

        return $this;
    }
    
    /**
     * @param int $quantity
     * @return self
    */
    public function setQuantity(int $quantity = 1): self
    {
        $this->payment_supplier->setQuantity($quantity);
        $this->log_service->info('Payment ID: ' . $this->payment->id . ' | Set Quantity: ' . $quantity);

        return $this;
    }
    
    /**
     * @param string $currency
     * @return self
    */
    public function setCurrency(string $currency = 'NIS'): self
    {
        $this->payment_supplier->setCurrency($currency);
        $this->log_service->info('Payment ID: ' . $this->payment->id . ' | Set Currency: ' . $currency);

        return $this;
    }
    
    /**
     * @return void
    */
    public function pay()
    {
        $this->payment_supplier->pay();
        $this->log_service->info('Payment ID: ' . $this->payment->id . ' | Pay');
    }
    
    /**
     * @return bool
    */
    public function isValid(): bool
    {
        $status = $this->payment_supplier->isValid() ? StatusService::ACTIVE : StatusService::INACTIVE;

        $this->payment->update([
            'status' => $status
        ]);

        $this->log_service->info('Payment ID: ' . $this->payment->id . ' | Validation: ' . $status);

        return !!$status;
    }
    
    /**
     * @param int $order_id
     * @return void
    */
    private function startPayment(int $order_id)
    {
        $payment = new Payment;
        $payment->order_id = $order_id;
        $payment->supplier_id = $this->payment_supplier->getSupplierID();
        $payment->status = StatusService::IN_PROGRESS;
        $payment->save();

        $this->payment = $payment;
    }
}