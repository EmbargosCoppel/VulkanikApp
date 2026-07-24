<?php

namespace App\Services\States;

class EsperandoPiezasState implements OrdenStateInterface
{
    public function puedeAgregarRefacciones(): bool
    {
        return true;
    }

    public function puedeCambiarEstado(): bool
    {
        return true;
    }

    public function getEstadoNombre(): string
    {
        return 'esperando_piezas';
    }

    public function siguienteEstado(): ?string
    {
        return 'reparación';
    }
}
