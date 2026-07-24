<?php

namespace App\Listeners;

use App\Events\StockBajo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarAlertaStockBajo
{
    public function handle(StockBajo $event): void
    {
        $refaccion = $event->refaccion;
        
        Log::warning('Stock bajo detectado', [
            'refaccion' => $refaccion->nombre,
            'sku' => $refaccion->sku,
            'stock_actual' => $event->stockActual,
            'stock_minimo' => $event->stockMinimo,
        ]);

        // Implementación futura para enviar email
        // Mail::to('admin@vulcanizadora.com')->send(new StockBajoMail($refaccion));
        
        // Implementación futura para enviar SMS
        // SMS::send('Stock bajo para: ' . $refaccion->nombre);
    }
}
