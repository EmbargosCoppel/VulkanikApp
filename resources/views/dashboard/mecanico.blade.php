<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold" style="color: var(--color-secondary);">
                    Dashboard Mecánico
                </h2>
                <p class="mt-1 text-sm" style="color: var(--color-secondary-light);">
                    Mis órdenes asignadas
                </p>
            </div>
            <div class="hidden sm:block">
                <span class="text-sm" style="color: var(--color-secondary-light);">
                    {{ now()->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Órdenes Asignadas -->
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Órdenes Asignadas</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--color-warning);">{{ $ordenes_pendientes }}</p>
                        <p class="text-xs mt-2" style="color: var(--color-secondary-light);">
                            <i class="fas fa-clock"></i> En proceso
                        </p>
                    </div>
                    <div class="stat-icon" style="background-color: #fef7e0; color: #fbbc04;">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>

            <!-- Órdenes Completadas -->
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Órdenes Completadas</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--color-success);">{{ $ordenes_completadas }}</p>
                        <p class="text-xs mt-2" style="color: var(--color-secondary-light);">
                            <i class="fas fa-check-circle"></i> Finalizadas
                        </p>
                    </div>
                    <div class="stat-icon" style="background-color: #e6f4ea; color: #34a853;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <!-- Total -->
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Total Órdenes</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--color-primary);">{{ $ordenes_pendientes + $ordenes_completadas }}</p>
                        <p class="text-xs mt-2" style="color: var(--color-secondary-light);">
                            <i class="fas fa-chart-bar"></i> Histórico
                        </p>
                    </div>
                    <div class="stat-icon" style="background-color: #e8f0fe; color: #1a73e8;">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Orders -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-2" style="color: var(--color-primary);"></i>
                    Mis Órdenes Asignadas
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
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ordenes_asignadas as $orden)
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
                                    <div class="flex gap-2">
                                        <a href="{{ route('ordenes.show', $orden) }}" class="text-sm" style="color: var(--color-primary);">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <a href="{{ route('ordenes.edit', $orden) }}" class="text-sm" style="color: var(--color-warning);">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-8" style="color: var(--color-secondary-light);">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No tienes órdenes asignadas</p>
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
