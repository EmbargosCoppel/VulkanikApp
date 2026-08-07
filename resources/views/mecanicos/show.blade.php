<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold" style="color: var(--color-secondary);">
                    Detalle del Mecánico
                </h2>
                <p class="mt-1 text-sm" style="color: var(--color-secondary-light);">
                    Información y órdenes del mecánico
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Total Órdenes</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--color-primary);">{{ $stats['total_ordenes'] }}</p>
                    </div>
                    <div class="stat-icon" style="background-color: #e8f0fe; color: #1a73e8;">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Órdenes Pendientes</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--color-warning);">{{ $stats['ordenes_pendientes'] }}</p>
                    </div>
                    <div class="stat-icon" style="background-color: #fef7e0; color: #fbbc04;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Órdenes Completadas</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--color-success);">{{ $stats['ordenes_completadas'] }}</p>
                    </div>
                    <div class="stat-icon" style="background-color: #e6f4ea; color: #34a853;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del mecánico -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-cog mr-2" style="color: var(--color-primary);"></i>
                    Información del Mecánico
                </h3>
                <div class="flex gap-2">
                    <a href="{{ route('mecanicos.edit', $mecanico) }}" class="btn btn-secondary">
                        <i class="fas fa-edit"></i>
                        Editar
                    </a>
                    <a href="{{ route('mecanicos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Volver
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                            {{ substr($mecanico->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-lg font-bold" style="color: var(--color-secondary);">{{ $mecanico->name }}</h4>
                            <p class="text-sm" style="color: var(--color-secondary-light);">{{ $mecanico->email }}</p>
                            <p class="text-xs mt-1" style="color: var(--color-secondary-light);">Registrado el {{ $mecanico->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Órdenes del mecánico -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-2" style="color: var(--color-primary);"></i>
                    Órdenes de Trabajo
                </h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vehículo</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ordenes as $orden)
                            <tr>
                                <td class="font-medium">#{{ $orden->id }}</td>
                                <td>
                                    {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}
                                    <br>
                                    <span class="text-xs" style="color: var(--color-secondary-light);">
                                        ({{ $orden->vehiculo->placa }})
                                    </span>
                                </td>
                                <td>{{ $orden->vehiculo->cliente->nombre }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($orden->estado) {
                                            'diagnóstico' => 'badge-primary',
                                            'esperando_piezas' => 'badge-warning',
                                            'reparación' => 'badge-primary',
                                            'finalizado' => 'badge-success',
                                            default => 'badge-primary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                                    </span>
                                </td>
                                <td>{{ $orden->fecha_entrada->format('d/m/Y') }}</td>
                                <td>
                                    @if($orden->total)
                                        ${{ number_format($orden->total, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('ordenes.show', $orden) }}" class="text-sm" style="color: var(--color-primary);">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-8" style="color: var(--color-secondary-light);">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No hay órdenes asignadas a este mecánico</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>