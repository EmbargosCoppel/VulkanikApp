<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\VehiculoController;
use App\Http\Controllers\Api\OrdenTrabajoController;
use App\Http\Controllers\Api\RefaccionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| API RESTful protegida con tokens Sanctum.
| Para obtener token: POST /api/login con email + password
|
*/

// Público: login para obtener token
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!auth()->attempt($credentials)) {
        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    $user = auth()->user();
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ],
    ]);
})->name('api.login');

// Rutas protegidas con Sanctum + rate limiting
// Nota: Se usa el prefijo de nombre 'api.' para evitar conflictos con rutas web
Route::middleware(['auth:sanctum', 'throttle:60,1'])->name('api.')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user');

    // Clientes
    Route::apiResource('clientes', ClienteController::class);

    // Vehículos
    Route::apiResource('vehiculos', VehiculoController::class);
    Route::get('vehiculos/cliente/{cliente}', [VehiculoController::class, 'indexByCliente'])->name('vehiculos.by-cliente');

    // Órdenes de Trabajo
    Route::apiResource('ordenes-trabajo', OrdenTrabajoController::class);
    Route::post('ordenes-trabajo/{ordenTrabajo}/refacciones', [OrdenTrabajoController::class, 'agregarRefaccion'])->name('ordenes-trabajo.refacciones');
    Route::get('ordenes-trabajo/{ordenTrabajo}/totales', [OrdenTrabajoController::class, 'calcularTotales'])->name('ordenes-trabajo.totales');

    // Refacciones
    Route::apiResource('refacciones', RefaccionController::class);
    Route::put('refacciones/{refaccion}/stock', [RefaccionController::class, 'actualizarStock'])->name('refacciones.stock');
    Route::get('refacciones/stock-bajo', [RefaccionController::class, 'stockBajo'])->name('refacciones.stock-bajo');
});
