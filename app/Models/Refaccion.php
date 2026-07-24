<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refaccion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'sku',
        'descripcion',
        'costo',
        'precio_venta',
        'stock_actual',
        'stock_minimo',
        'ubicacion',
        'proveedor',
        'activo',
    ];

    protected $table = 'refacciones';

    protected $casts = [
        'costo' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function ordenesTrabajo(): BelongsToMany
    {
        return $this->belongsToMany(OrdenTrabajo::class, 'orden_refaccion')
            ->withPivot('cantidad', 'precio_unitario', 'subtotal')
            ->withTimestamps();
    }

    public function estaBajoStock(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }
}
