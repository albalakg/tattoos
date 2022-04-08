<?php

namespace App\Domain\Payment\Interfaces;

use App\Domain\Orders\Models\Order;

interface IPaymentProvider
{
    public function getProviderID(): int;
    public function setPrice(int $price);
    public function setQuantity(int $quantity = 1);
    public function setCurrency(string $currency);
    public function buildPayment(Order $order);
    public function pay();
    public function isValid(): bool;
}
