<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'orden_trabajo_id',
        'transaction_id',
        'payment_method',
        'monto',
        'estado',
        'moneda',
        'metadata',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }
}