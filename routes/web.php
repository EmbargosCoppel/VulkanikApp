<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\RefaccionController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

Route::get('/', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Profile
Route::get('/profile', [ProfileController::class, 'edit'])
    ->middleware('auth')
    ->name('profile.edit');

Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update'])
    ->middleware('auth')
    ->name('profile.update');

Route::delete('/profile', [ProfileController::class, 'destroy'])
    ->middleware('auth')
    ->name('profile.destroy');

// Clientes
Route::resource('clientes', ClienteController::class)->middleware('auth');

// Vehículos
Route::resource('vehiculos', VehiculoController::class)->middleware('auth');

// Refacciones
Route::get('refacciones/stock-bajo', [RefaccionController::class, 'stockBajo'])->name('refacciones.stock-bajo')->middleware('auth');
Route::get('refacciones/{refaccion}/stock', [RefaccionController::class, 'stock'])->name('refacciones.stock')->middleware('auth');
Route::put('refacciones/{refaccion}/stock', [RefaccionController::class, 'actualizarStock'])->name('refacciones.actualizarStock')->middleware('auth');
Route::resource('refacciones', RefaccionController::class)->middleware('auth');

// Órdenes de Trabajo
Route::resource('ordenes', OrdenTrabajoController::class, ['parameters' => ['ordenes' => 'ordenTrabajo']])->middleware('auth');
Route::post('ordenes/{ordenTrabajo}/refacciones', [OrdenTrabajoController::class, 'agregarRefaccion'])->name('ordenes.agregarRefaccion')->middleware('auth');

require __DIR__.'/auth.php';
