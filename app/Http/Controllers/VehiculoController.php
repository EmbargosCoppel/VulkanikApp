<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehiculoRequest;
use App\Models\Vehiculo;
use App\Models\Cliente;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehiculo::with(['cliente', 'ordenesTrabajo']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('marca', 'like', '%' . $request->search . '%')
                  ->orWhere('modelo', 'like', '%' . $request->search . '%')
                  ->orWhere('placa', 'like', '%' . $request->search . '%');
            });
        }

        $vehiculos = $query->orderBy('marca')
            ->paginate(config('taller.pagination.per_page', 15));
        return view('vehiculos.index', compact('vehiculos'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('vehiculos.create', compact('clientes'));
    }

    public function store(VehiculoRequest $request)
    {
        Vehiculo::create($request->validated());
        return redirect()->route('vehiculos.index')->with('success', 'Vehículo registrado exitosamente');
    }

    public function show(Vehiculo $vehiculo)
    {
        $vehiculo->load(['cliente', 'ordenesTrabajo.mecanico', 'ordenesTrabajo.refacciones']);
        return view('vehiculos.show', compact('vehiculo'));
    }

    public function edit(Vehiculo $vehiculo)
    {
        $clientes = Cliente::all();
        return view('vehiculos.edit', compact('vehiculo', 'clientes'));
    }

    public function update(VehiculoRequest $request, Vehiculo $vehiculo)
    {
        $vehiculo->update($request->validated());
        return redirect()->route('vehiculos.index')->with('success', 'Vehículo actualizado exitosamente');
    }

    public function destroy(Vehiculo $vehiculo)
    {
        $vehiculo->delete();
        return redirect()->route('vehiculos.index')->with('success', 'Vehículo eliminado exitosamente');
    }
}
