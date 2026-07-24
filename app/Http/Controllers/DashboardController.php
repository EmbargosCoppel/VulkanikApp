<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\OrdenTrabajo;
use App\Models\Refaccion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return $this->adminDashboard();
        }
        
        return $this->mecanicoDashboard();
    }
    
    private function adminDashboard(): View
    {
        $stats = [
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
        ];
        
        $ordenes_recientes = OrdenTrabajo::with(['vehiculo.cliente', 'mecanico'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $refacciones_stock_bajo = Refaccion::whereColumn('stock_actual', '<=', 'stock_minimo')
            ->where('activo', true)
            ->orderBy('stock_actual', 'asc')
            ->limit(5)
            ->get();
        
        return view('dashboard.admin', compact('stats', 'ordenes_recientes', 'refacciones_stock_bajo'));
    }
    
    private function mecanicoDashboard(): View
    {
        $ordenes_asignadas = OrdenTrabajo::with(['vehiculo.cliente'])
            ->where('mecanico_id', auth()->id())
            ->whereIn('estado', ['diagnóstico', 'esperando_piezas', 'reparación'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $ordenes_completadas = OrdenTrabajo::where('mecanico_id', auth()->id())
            ->where('estado', 'finalizado')
            ->count();
            
        $ordenes_pendientes = OrdenTrabajo::where('mecanico_id', auth()->id())
            ->whereIn('estado', ['diagnóstico', 'esperando_piezas', 'reparación'])
            ->count();
        
        return view('dashboard.mecanico', compact('ordenes_asignadas', 'ordenes_completadas', 'ordenes_pendientes'));
    }
}
