<?php

namespace App\Domain\Payment\Interfaces;

interface IPaymentSupplier
{
    public function getSupplierID(): int;
    public function setPrice(int $price);
    public function setQuantity(int $quantity = 1);
    public function setCurrency(string $currency = 'NIS');
    public function pay();
    public function isValid(): bool;
}

