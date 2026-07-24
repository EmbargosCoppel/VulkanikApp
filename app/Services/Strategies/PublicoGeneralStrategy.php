<?php

namespace App\Services\Strategies;

class PublicoGeneralStrategy implements PricingStrategyInterface
{
    private const IVA = 0.16;
    private const DESCUENTO = 0.0;

    public function calcularTotal(float $subtotal): float
    {
        $subtotalConDescuento = $this->aplicarDescuento($subtotal);
        $iva = $subtotalConDescuento * self::IVA;
        return $subtotalConDescuento + $iva;
    }

    public function aplicarDescuento(float $subtotal): float
    {
        return $subtotal * (1 - self::DESCUENTO);
    }
}
