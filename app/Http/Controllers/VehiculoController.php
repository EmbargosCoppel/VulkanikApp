<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Cliente;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index()
    {
        $vehiculos = Vehiculo::with('cliente')->get();
        return view('vehiculos.index', compact('vehiculos'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('vehiculos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'placa' => 'required|string|unique:vehiculos,placa',
            'color' => 'nullable|string|max:50',
            'vin' => 'nullable|string|max:17',
            'notas' => 'nullable|string',
        ]);

        Vehiculo::create($validated);
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

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $validated = $request->validate([
            'cliente_id' => 'sometimes|exists:clientes,id',
            'marca' => 'sometimes|string|max:100',
            'modelo' => 'sometimes|string|max:100',
            'anio' => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
            'placa' => 'sometimes|string|unique:vehiculos,placa,' . $vehiculo->id,
            'color' => 'nullable|string|max:50',
            'vin' => 'nullable|string|max:17',
            'notas' => 'nullable|string',
        ]);

        $vehiculo->update($validated);
        return redirect()->route('vehiculos.index')->with('success', 'Vehículo actualizado exitosamente');
    }

    public function destroy(Vehiculo $vehiculo)
    {
        $vehiculo->delete();
        return redirect()->route('vehiculos.index')->with('success', 'Vehículo eliminado exitosamente');
    }
}
