<?php

namespace App\Services;

interface PaymentAdapterInterface
{
    public function procesarPago(float $monto, array $datosPago): array;
    public function reembolsar($orden): array;
}
