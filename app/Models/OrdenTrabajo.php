<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenTrabajo extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehiculo_id',
        'mecanico_id',
        'estado',
        'diagnostico',
        'trabajos_realizados',
        'mano_obra',
        'subtotal',
        'iva',
        'total',
        'fecha_entrada',
        'fecha_salida',
        'observaciones',
    ];

    protected $table = 'ordenes_trabajo';

    protected $casts = [
        'mano_obra' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_entrada' => 'datetime',
        'fecha_salida' => 'datetime',
    ];

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function mecanico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    public function refacciones(): BelongsToMany
    {
        return $this->belongsToMany(Refaccion::class, 'orden_refaccion')
            ->withPivot('cantidad', 'precio_unitario', 'subtotal')
            ->withTimestamps();
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function puedeAgregarRefacciones(): bool
    {
        return in_array($this->estado, ['diagnóstico', 'esperando_piezas', 'reparación']);
    }

    public function estaFinalizada(): bool
    {
        return $this->estado === 'finalizado';
    }
}
