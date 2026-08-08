<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\RefaccionController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

// Ruta de diagnóstico (solo para testing)
Route::get('/debug', function () {
    return response()->json([
        'status' => 'ok',
        'app_name' => config('app.name'),
        'app_env' => config('app.env'),
        'database_connection' => config('database.default'),
        'database_host' => config('database.connections.mysql.host'),
        'timestamp' => now()->toDateTimeString(),
    ]);
});

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth']);

Route::get('/admin/cobros', [DashboardController::class, 'cobros'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.cobros');

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

// Clientes - solo admin
Route::resource('clientes', ClienteController::class)->middleware(['auth', 'role:admin']);

// Mecánicos - solo admin
Route::resource('mecanicos', UserController::class)->middleware(['auth', 'role:admin']);
Route::post('mecanicos/{mecanico}/restore', [UserController::class, 'restore'])->name('mecanicos.restore')->middleware(['auth', 'role:admin']);

// Vehículos - admin y mecánico
Route::resource('vehiculos', VehiculoController::class)->middleware(['auth', 'role:admin,mecanico']);

// Refacciones - solo admin
Route::get('refacciones/stock-bajo', [RefaccionController::class, 'stockBajo'])->name('refacciones.stock-bajo')->middleware(['auth', 'role:admin']);
Route::get('refacciones/{refaccion}/stock', [RefaccionController::class, 'stock'])->name('refacciones.stock')->middleware(['auth', 'role:admin']);
Route::put('refacciones/{refaccion}/stock', [RefaccionController::class, 'actualizarStock'])->name('refacciones.actualizarStock')->middleware(['auth', 'role:admin']);
Route::resource('refacciones', RefaccionController::class, ['parameters' => ['refacciones' => 'refaccion']])->middleware(['auth', 'role:admin']);

// Órdenes de Trabajo - admin y mecánico
Route::resource('ordenes', OrdenTrabajoController::class, ['parameters' => ['ordenes' => 'ordenTrabajo']])->middleware(['auth', 'role:admin,mecanico']);
Route::post('ordenes/{ordenTrabajo}/refacciones', [OrdenTrabajoController::class, 'agregarRefaccion'])->name('ordenes.agregarRefaccion')->middleware(['auth', 'role:admin,mecanico']);

require __DIR__.'/auth.php';
