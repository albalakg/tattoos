<?php

namespace App\Domain\Interfaces;

interface IBaseServiceInterface
{
    public function isActive(object $item) :bool;
}