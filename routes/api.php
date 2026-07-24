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
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Clientes
Route::apiResource('clientes', ClienteController::class);

// Vehículos
Route::apiResource('vehiculos', VehiculoController::class);
Route::get('vehiculos/cliente/{cliente}', [VehiculoController::class, 'indexByCliente']);

// Órdenes de Trabajo
Route::apiResource('ordenes-trabajo', OrdenTrabajoController::class);
Route::post('ordenes-trabajo/{ordenTrabajo}/refacciones', [OrdenTrabajoController::class, 'agregarRefaccion']);
Route::get('ordenes-trabajo/{ordenTrabajo}/totales', [OrdenTrabajoController::class, 'calcularTotales']);

// Refacciones
Route::apiResource('refacciones', RefaccionController::class);
Route::put('refacciones/{refaccion}/stock', [RefaccionController::class, 'actualizarStock']);
Route::get('refacciones/stock-bajo', [RefaccionController::class, 'stockBajo']);
