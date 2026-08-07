<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $ordenTrabajo->id }} - Vulcanizadora Don Chuy</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; padding: 20px; max-width: 800px; margin: 0 auto; }
        .ticket { border: 2px dashed #000; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 15px; }
        .header h1 { font-size: 24px; margin-bottom: 5px; }
        .header p { font-size: 12px; }
        .section { margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #000; }
        .section-title { font-weight: bold; font-size: 14px; margin-bottom: 5px; }
        .info-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 3px; }
        .totals { margin-top: 15px; }
        .total-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 5px; }
        .total-final { font-size: 18px; font-weight: bold; border-top: 2px solid #000; padding-top: 10px; margin-top: 10px; }
        .footer { text-align: center; margin-top: 20px; font-size: 11px; }
        .actions { text-align: center; margin-top: 20px; }
        .btn { padding: 10px 20px; margin: 5px; border: none; cursor: pointer; font-size: 14px; }
        .btn-print { background: #000; color: #fff; }
        .btn-back { background: #ccc; color: #000; }
        @media print {
            body { padding: 0; }
            .actions { display: none; }
            .ticket { border: 2px solid #000; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <h1>VULCANIZADORA DON CHUY</h1>
            <p>Sistema de Administración de Taller Mecánico</p>
            <p>Ticket de Cobro</p>
        </div>

        <div class="section">
            <div class="section-title">DATOS DE LA ORDEN</div>
            <div class="info-row">
                <span>Orden #:</span>
                <span>#{{ $ordenTrabajo->id }}</span>
            </div>
            <div class="info-row">
                <span>Fecha Entrada:</span>
                <span>{{ $ordenTrabajo->fecha_entrada->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span>Fecha Salida:</span>
                <span>{{ $ordenTrabajo->fecha_salida ? $ordenTrabajo->fecha_salida->format('d/m/Y H:i') : 'Pendiente' }}</span>
            </div>
            <div class="info-row">
                <span>Estado:</span>
                <span>{{ ucfirst($ordenTrabajo->estado) }}</span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">DATOS DEL CLIENTE</div>
            <div class="info-row">
                <span>Cliente:</span>
                <span>{{ $ordenTrabajo->vehiculo->cliente->nombre }}</span>
            </div>
            @if($ordenTrabajo->vehiculo->cliente->email)
            <div class="info-row">
                <span>Email:</span>
                <span>{{ $ordenTrabajo->vehiculo->cliente->email }}</span>
            </div>
            @endif
            @if($ordenTrabajo->vehiculo->cliente->telefono)
            <div class="info-row">
                <span>Teléfono:</span>
                <span>{{ $ordenTrabajo->vehiculo->cliente->telefono }}</span>
            </div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">DATOS DEL VEHÍCULO</div>
            <div class="info-row">
                <span>Vehículo:</span>
                <span>{{ $ordenTrabajo->vehiculo->marca }} {{ $ordenTrabajo->vehiculo->modelo }} ({{ $ordenTrabajo->vehiculo->anio }})</span>
            </div>
            <div class="info-row">
                <span>Placa:</span>
                <span>{{ $ordenTrabajo->vehiculo->placa }}</span>
            </div>
            @if($ordenTrabajo->vehiculo->vin)
            <div class="info-row">
                <span>VIN:</span>
                <span>{{ $ordenTrabajo->vehiculo->vin }}</span>
            </div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">DATOS DEL MECÁNICO</div>
            <div class="info-row">
                <span>Mecánico:</span>
                <span>{{ $ordenTrabajo->mecanico->name ?? 'No asignado' }}</span>
            </div>
        </div>

        @if($ordenTrabajo->diagnostico)
        <div class="section">
            <div class="section-title">DIAGNÓSTICO</div>
            <p style="font-size: 12px; white-space: pre-wrap;">{{ $ordenTrabajo->diagnostico }}</p>
        </div>
        @endif

        @if($ordenTrabajo->trabajos_realizados)
        <div class="section">
            <div class="section-title">TRABAJOS REALIZADOS</div>
            <p style="font-size: 12px; white-space: pre-wrap;">{{ $ordenTrabajo->trabajos_realizados }}</p>
        </div>
        @endif

        <div class="section">
            <div class="section-title">REFRACCIONES UTILIZADAS</div>
            <table style="width: 100%; font-size: 11px; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                        <th style="text-align: left; padding: 5px;">Refacción</th>
                        <th style="text-align: center; padding: 5px;">Cant.</th>
                        <th style="text-align: right; padding: 5px;">Precio</th>
                        <th style="text-align: right; padding: 5px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ordenTrabajo->refacciones as $refaccion)
                    <tr style="border-bottom: 1px dashed #ccc;">
                        <td style="padding: 5px;">{{ $refaccion->nombre }}</td>
                        <td style="text-align: center; padding: 5px;">{{ $refaccion->pivot->cantidad }}</td>
                        <td style="text-align: right; padding: 5px;">${{ number_format($refaccion->pivot->precio_unitario, 2) }}</td>
                        <td style="text-align: right; padding: 5px;">${{ number_format($refaccion->pivot->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="totals">
            <div class="total-row">
                <span>Subtotal Refacciones:</span>
                <span>${{ number_format($totales['subtotal_refacciones'], 2) }}</span>
            </div>
            <div class="total-row">
                <span>Mano de Obra:</span>
                <span>${{ number_format($totales['mano_obra'], 2) }}</span>
            </div>
            <div class="total-row">
                <span>Subtotal:</span>
                <span>${{ number_format($totales['subtotal'], 2) }}</span>
            </div>
            <div class="total-row">
                <span>IVA (16%):</span>
                <span>${{ number_format($totales['iva'], 2) }}</span>
            </div>
            <div class="total-row total-final">
                <span>TOTAL:</span>
                <span>${{ number_format($totales['total'], 2) }}</span>
            </div>
        </div>

        @if($ordenTrabajo->observaciones)
        <div class="section">
            <div class="section-title">OBSERVACIONES</div>
            <p style="font-size: 12px; white-space: pre-wrap;">{{ $ordenTrabajo->observaciones }}</p>
        </div>
        @endif

        <div class="footer">
            <p>¡Gracias por su preferencia!</p>
            <p>Vulcanizadora Don Chuy - {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="actions">
        <button onclick="window.print()" class="btn btn-print">
            <i class="fas fa-print"></i> Imprimir Ticket
        </button>
        <a href="{{ route('ordenes.show', $ordenTrabajo) }}" class="btn btn-back">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</body>
</html>