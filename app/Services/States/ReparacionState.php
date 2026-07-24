<?php

namespace App\Services\States;

class ReparacionState implements OrdenStateInterface
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
        return 'reparación';
    }

    public function siguienteEstado(): ?string
    {
        return 'finalizado';
    }
}
