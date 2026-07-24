<?php

namespace App\Services\States;

interface OrdenStateInterface
{
    public function puedeAgregarRefacciones(): bool;
    public function puedeCambiarEstado(): bool;
    public function getEstadoNombre(): string;
    public function siguienteEstado(): ?string;
}
