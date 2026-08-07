<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrdenTrabajo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * List all mechanics (users with role 'mecanico').
     */
    public function index(): JsonResponse
    {
        $mecanicos = User::where('role', 'mecanico')
            ->withCount(['ordenesAsignadas' => function ($query) {
                $query->whereIn('estado', ['diagnóstico', 'esperando_piezas', 'reparación']);
            }])
            ->orderBy('name')
            ->get();

        return response()->json($mecanicos);
    }

    /**
     * Store a new mechanic.
     */
    public function store(Request $request): JsonResponse
    {
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

        return response()->json($user, 201);
    }

    /**
     * Show a specific mechanic.
     */
    public function show(User $mecanico): JsonResponse
    {
        $mecanico->load('ordenesAsignadas.vehiculo.cliente');

        $stats = [
            'total_ordenes' => $mecanico->ordenesAsignadas()->count(),
            'ordenes_completadas' => $mecanico->ordenesAsignadas()->where('estado', 'finalizado')->count(),
            'ordenes_pendientes' => $mecanico->ordenesAsignadas()->whereIn('estado', ['diagnóstico', 'esperando_piezas', 'reparación'])->count(),
        ];

        return response()->json([
            'user' => $mecanico,
            'stats' => $stats,
        ]);
    }

    /**
     * Update a mechanic.
     */
    public function update(Request $request, User $mecanico): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|lowercase|email|max:255|unique:users,email,' . $mecanico->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = [];

        if (isset($validated['name'])) {
            $data['name'] = $validated['name'];
        }

        if (isset($validated['email'])) {
            $data['email'] = $validated['email'];
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $mecanico->update($data);

        return response()->json($mecanico);
    }

    /**
     * Delete a mechanic.
     */
    public function destroy(User $mecanico): JsonResponse
    {
        // Reassign active orders to unassigned (null)
        OrdenTrabajo::where('mecanico_id', $mecanico->id)
            ->whereIn('estado', ['diagnóstico', 'esperando_piezas', 'reparación'])
            ->update(['mecanico_id' => null]);

        $mecanico->delete();

        return response()->json(null, 204);
    }
}