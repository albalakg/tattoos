<?php

namespace App\Domain\Payment\Suppliers;

use App\Domain\Payment\Models\LuSupplierType;
use App\Domain\Payment\Interfaces\IPaymentSupplier;

class PaypalSupplier implements IPaymentSupplier
{
    public function __construct()
    {
        // TODO
    }
    
    /**
     * @return int
    */
    public function getSupplierID(): int
    {
        return LuSupplierType::PAYPAL;
    }
    
    /**
     * @return bool
    */
    public function isValid(): bool
    {
        return true;
    }

    public function setPrice(int $price)
    {

    }

    public function setQuantity(int $quantity = 1)
    {

    }

    public function setCurrency(string $currency = 'NIS')
    {

    }

    public function pay()
    {

    }
}