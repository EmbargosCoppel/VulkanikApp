<?php

return [

    /*
    |--------------------------------------------------------------------------
    | IVA (Impuesto al Valor Agregado)
    |--------------------------------------------------------------------------
    |
    | Tasa de IVA aplicada a las órdenes de trabajo.
    | Configurado para México (16%).
    |
    */

    'iva' => env('IVA_RATE', 0.16),

    /*
    |--------------------------------------------------------------------------
    | Configuración de Paginación
    |--------------------------------------------------------------------------
    |
    | Número de elementos por página en listados.
    |
    */

    'pagination' => [
        'per_page' => env('PAGINATION_PER_PAGE', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Pagos
    |--------------------------------------------------------------------------
    |
    | Configuración para la pasarela de pagos.
    |
    */

    'payment' => [
        'currency' => env('PAYMENT_CURRENCY', 'mxn'),
        'stripe_key' => env('STRIPE_KEY'),
        'stripe_secret' => env('STRIPE_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Backup
    |--------------------------------------------------------------------------
    |
    | Configuración para backups automatizados.
    |
    */

    'backup' => [
        'disk' => env('BACKUP_DISK', 'local'),
        'path' => env('BACKUP_PATH', 'backups'),
    ],

];
