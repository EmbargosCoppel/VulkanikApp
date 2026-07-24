<?php

namespace App\Services\Strategies;

interface PricingStrategyInterface
{
    public function calcularTotal(float $subtotal): float;
    public function aplicarDescuento(float $subtotal): float;
}
