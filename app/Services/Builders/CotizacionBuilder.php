<?php

namespace App\Services\Builders;

use App\Models\Refaccion;
use App\Services\Strategies\PricingStrategyInterface;

class CotizacionBuilder
{
    private array $items = [];
    private float $manoObra = 0;
    private string $notas = '';
    private ?PricingStrategyInterface $pricingStrategy = null;
    private ?string $clienteNombre = null;
    private ?string $vehiculoInfo = null;

    public function reset(): self
    {
        $this->items = [];
        $this->manoObra = 0;
        $this->notas = '';
        $this->pricingStrategy = null;
        $this->clienteNombre = null;
        $this->vehiculoInfo = null;
        return $this;
    }

    public function setCliente(string $nombre): self
    {
        $this->clienteNombre = $nombre;
        return $this;
    }

    public function setVehiculo(string $marca, string $modelo, string $placa): self
    {
        $this->vehiculoInfo = "{$marca} {$modelo} - {$placa}";
        return $this;
    }

    public function setManoObra(float $monto): self
    {
        $this->manoObra = $monto;
        return $this;
    }

    public function addRefaccion(Refaccion $refaccion, int $cantidad): self
    {
        $this->items[] = [
            'tipo' => 'refaccion',
            'nombre' => $refaccion->nombre,
            'sku' => $refaccion->sku,
            'cantidad' => $cantidad,
            'precio_unitario' => $refaccion->precio_venta,
            'subtotal' => $refaccion->precio_venta * $cantidad,
        ];
        return $this;
    }

    public function addServicio(string $nombre, float $precio): self
    {
        $this->items[] = [
            'tipo' => 'servicio',
            'nombre' => $nombre,
            'cantidad' => 1,
            'precio_unitario' => $precio,
            'subtotal' => $precio,
        ];
        return $this;
    }

    public function setNotas(string $notas): self
    {
        $this->notas = $notas;
        return $this;
    }

    public function setPricingStrategy(PricingStrategyInterface $strategy): self
    {
        $this->pricingStrategy = $strategy;
        return $this;
    }

    public function build(): array
    {
        $subtotalItems = collect($this->items)->sum('subtotal');
        $subtotal = $subtotalItems + $this->manoObra;

        $strategy = $this->pricingStrategy ?? new \App\Services\Strategies\PublicoGeneralStrategy();
        $total = $strategy->calcularTotal($subtotal);
        $descuento = $subtotal - ($total / 1.16);
        $iva = $total - ($subtotal - $descuento);

        return [
            'cliente' => $this->clienteNombre,
            'vehiculo' => $this->vehiculoInfo,
            'items' => $this->items,
            'mano_obra' => $this->manoObra,
            'subtotal_items' => $subtotalItems,
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'iva' => $iva,
            'total' => $total,
            'notas' => $this->notas,
            'fecha' => now()->toDateTimeString(),
            'vigencia' => now()->addDays(7)->toDateTimeString(),
        ];
    }
}
