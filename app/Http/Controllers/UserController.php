<?php

namespace App\Http\Controllers;

use App\Http\Requests\MecanicoRequest;
use App\Models\OrdenTrabajo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of mechanics (users with role 'mecanico').
     */
    public function index()
    {
        $mecanicos = User::withTrashed()
            ->where('role', 'mecanico')
            ->withCount(['ordenesAsignadas' => function ($query) {
                $query->whereIn('estado', ['diagnóstico', 'esperando_piezas', 'reparación']);
            }])
            ->orderBy('name')
            ->paginate(config('taller.pagination.per_page', 15));

        return view('mecanicos.index', compact('mecanicos'));
    }

    /**
     * Show the form for creating a new mechanic.
     */
    public function create()
    {
        return view('mecanicos.create');
    }

    /**
     * Store a newly created mechanic in storage.
     */
    public function store(MecanicoRequest $request)
    {
        User::create([
            'name' => $request->validated()['name'],
            'email' => $request->validated()['email'],
            'password' => Hash::make($request->validated()['password']),
            'role' => 'mecanico',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('mecanicos.index')->with('success', 'Mecánico creado exitosamente');
    }

    /**
     * Display the specified mechanic.
     */
    public function show(User $mecanico)
    {
        $mecanico->load('ordenesAsignadas.vehiculo.cliente');

        $ordenes = $mecanico->ordenesAsignadas()
            ->with(['vehiculo.cliente'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_ordenes' => $mecanico->ordenesAsignadas()->count(),
            'ordenes_completadas' => $mecanico->ordenesAsignadas()->where('estado', 'finalizado')->count(),
            'ordenes_pendientes' => $mecanico->ordenesAsignadas()->whereIn('estado', ['diagnóstico', 'esperando_piezas', 'reparación'])->count(),
        ];

        return view('mecanicos.show', compact('mecanico', 'ordenes', 'stats'));
    }

    /**
     * Show the form for editing the specified mechanic.
     */
    public function edit(User $mecanico)
    {
        return view('mecanicos.edit', compact('mecanico'));
    }

    /**
     * Update the specified mechanic in storage.
     */
    public function update(MecanicoRequest $request, User $mecanico)
    {
        $data = $request->validated();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $mecanico->update($data);

        return redirect()->route('mecanicos.index')->with('success', 'Mecánico actualizado exitosamente');
    }

    /**
     * Remove the specified mechanic from storage (inactivate).
     */
    public function destroy(User $mecanico)
    {
        // Prevent deleting yourself
        if ($mecanico->id === auth()->id()) {
            return redirect()->route('mecanicos.index')->with('error', 'No puedes eliminar tu propia cuenta');
        }

        // Reassign active orders to unassigned (null)
        OrdenTrabajo::where('mecanico_id', $mecanico->id)
            ->whereIn('estado', ['diagnóstico', 'esperando_piezas', 'reparación'])
            ->update(['mecanico_id' => null]);

        $mecanico->delete();

        return redirect()->route('mecanicos.index')->with('success', 'Mecánico dado de baja exitosamente');
    }

    public function restore($id)
    {
        $mecanico = User::withTrashed()->where('role', 'mecanico')->findOrFail($id);

        if ($mecanico->id === auth()->id()) {
            return redirect()->route('mecanicos.index')->with('error', 'No puedes reactivar tu propia cuenta desde aquí');
        }

        $mecanico->restore();

        return redirect()->route('mecanicos.index')->with('success', 'Mecánico reactivado exitosamente');
    }
}