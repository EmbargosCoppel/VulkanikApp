<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BusquedaController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\CotizacionController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\VehiculoController;
use App\Http\Controllers\Api\OrdenTrabajoController;
use App\Http\Controllers\Api\RefaccionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WebhookController;
use App\Models\Cliente;
use App\Models\OrdenTrabajo;
use App\Models\Refaccion;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

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

// Público: webhook de Stripe (sin autenticación)
Route::post('/webhook/stripe', [WebhookController::class, 'handle'])->name('api.webhook.stripe');

// Público: registro de nuevo usuario (mecánico)
Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|lowercase|email|max:255|unique:users,email',
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => 'mecanico',
        'email_verified_at' => now(),
    ]);

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ],
    ], 201);
})->name('api.register');

// Rutas protegidas con Sanctum + rate limiting
// Nota: Se usa el prefijo de nombre 'api.' para evitar conflictos con rutas web
Route::middleware(['auth:sanctum', 'throttle:60,1'])->name('api.')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user');

    // Logout - revoca el token actual
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    })->name('logout');

    // Stats del dashboard
    Route::get('/stats', function () {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return response()->json([
                'clientes' => Cliente::count(),
                'vehiculos' => Vehiculo::count(),
                'ordenes' => OrdenTrabajo::count(),
                'refacciones' => Refaccion::where('activo', true)->count(),
                'ordenes_pendientes' => OrdenTrabajo::whereIn('estado', ['diagnóstico', 'esperando_piezas', 'reparación'])->count(),
                'ordenes_finalizadas' => OrdenTrabajo::where('estado', 'finalizado')->count(),
                'stock_bajo' => Refaccion::whereColumn('stock_actual', '<=', 'stock_minimo')
                    ->where('activo', true)
                    ->count(),
                'usuarios' => User::count(),
                'mecanicos' => User::where('role', 'mecanico')->count(),
            ]);
        }

        // Stats para mecánico
        return response()->json([
            'ordenes_asignadas' => OrdenTrabajo::where('mecanico_id', $user->id)
                ->whereIn('estado', ['diagnóstico', 'esperando_piezas', 'reparación'])
                ->count(),
            'ordenes_completadas' => OrdenTrabajo::where('mecanico_id', $user->id)
                ->where('estado', 'finalizado')
                ->count(),
            'total_ordenes' => OrdenTrabajo::where('mecanico_id', $user->id)->count(),
        ]);
    })->name('stats');

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

    // Mecánicos / Usuarios
    Route::apiResource('mecanicos', UserController::class);

    // Pagos
    Route::post('ordenes-trabajo/{ordenTrabajo}/pagar', [PaymentController::class, 'procesarPago'])->name('ordenes-trabajo.pagar')->middleware('role:admin');
    Route::post('ordenes-trabajo/{ordenTrabajo}/reembolsar', [PaymentController::class, 'reembolsar'])->name('ordenes-trabajo.reembolsar')->middleware('role:admin');
    Route::get('pagos/config', [PaymentController::class, 'getConfig'])->name('pagos.config')->middleware('role:admin');
    Route::post('ordenes-trabajo/{ordenTrabajo}/generar-link-pago', [PaymentController::class, 'generarLinkPago'])->name('ordenes-trabajo.generar-link-pago')->middleware('role:admin,mecanico');

    // Cotizaciones
    Route::post('cotizaciones/generar', [CotizacionController::class, 'generar'])->name('cotizaciones.generar');
    Route::get('cotizaciones/estrategias', [CotizacionController::class, 'getEstrategias'])->name('cotizaciones.estrategias');

    // Reportes
    Route::get('reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
    Route::get('reportes/ordenes-por-estado', [ReporteController::class, 'ordenesPorEstado'])->name('reportes.ordenes-por-estado');
    Route::get('reportes/refacciones-mas-usadas', [ReporteController::class, 'refaccionesMasUsadas'])->name('reportes.refacciones-mas-usadas');
    Route::get('reportes/rendimiento-mecanicos', [ReporteController::class, 'rendimientoMecanicos'])->name('reportes.rendimiento-mecanicos');
    Route::get('reportes/clientes', [ReporteController::class, 'clientes'])->name('reportes.clientes');
    Route::get('reportes/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');

    // Búsqueda global
    Route::get('buscar', [BusquedaController::class, 'buscar'])->name('buscar');
});
