<?php

namespace App\Services\States;

class FinalizadoState implements OrdenStateInterface
{
    public function puedeAgregarRefacciones(): bool
    {
        return false;
    }

    public function puedeCambiarEstado(): bool
    {
        return false;
    }

    public function getEstadoNombre(): string
    {
        return 'finalizado';
    }

    public function siguienteEstado(): ?string
    {
        return null;
    }
}
