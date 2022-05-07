<?php

namespace App\Domain\Payment\Interfaces;

use App\Domain\Orders\Models\Order;

interface IPaymentProvider
{
    public function getProviderID(): int;
    public function setPrice(int $price);
    public function setQuantity(int $quantity);
    public function setCurrency(string $currency);
    public function buildPayment(Order $order);
    public function startTransaction();
    public function isValid(): bool;
}
