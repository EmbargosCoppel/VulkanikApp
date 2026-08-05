<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Ruta de diagnóstico para verificar que la app funciona
Route::get('/debug', function (Request $request) {
    $user = auth()->user();
    
    return response()->json([
        'status' => 'ok',
        'app_name' => config('app.name'),
        'app_env' => config('app.env'),
        'app_debug' => config('app.debug'),
        'app_url' => config('app.url'),
        'database_connection' => config('database.default'),
        'database_host' => config('database.connections.mysql.host'),
        'database_database' => config('database.connections.mysql.database'),
        'user_authenticated' => $user ? true : false,
        'user' => $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ] : null,
        'timestamp' => now()->toDateTimeString(),
    ]);
})->middleware('auth');
