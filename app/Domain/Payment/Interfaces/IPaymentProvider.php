<?php

namespace App\Domain\Payment\Interfaces;

use App\Domain\Orders\Models\Order;

interface IPaymentProvider
{
    public function getProviderID(): int;
    public function getTransactionResponse();
    public function getGeneratedPageToken(): string;
    public function getGeneratedPageLink(): string;
    public function buildPayment(Order $order);
    public function startTransaction();
    public function sendInvoice(Order $order);
    public function isTransactionValid(): bool;
    public function isInvoiceValid(): bool;
    public function isPaymentCallbackValid(array $response): bool;
}
