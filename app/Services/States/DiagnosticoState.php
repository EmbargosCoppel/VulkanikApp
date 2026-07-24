<?php

namespace App\Services\States;

class DiagnosticoState implements OrdenStateInterface
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
        return 'diagnóstico';
    }

    public function siguienteEstado(): ?string
    {
        return 'esperando_piezas';
    }
}
