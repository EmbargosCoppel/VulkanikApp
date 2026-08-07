<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold" style="color: var(--color-secondary);">
                    Dashboard
                </h2>
                <p class="mt-1 text-sm" style="color: var(--color-secondary-light);">
                    Bienvenido al sistema de gestión de taller
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Clientes -->
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Total Clientes</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--color-secondary);">{{ $stats['clientes'] }}</p>
                        <p class="text-xs mt-2" style="color: var(--color-success);">
                            <i class="fas fa-arrow-up"></i> Activos
                        </p>
                    </div>
                    <div class="stat-icon" style="background-color: #e8f0fe; color: #1a73e8;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <!-- Vehículos -->
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Total Vehículos</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--color-secondary);">{{ $stats['vehiculos'] }}</p>
                        <p class="text-xs mt-2" style="color: var(--color-success);">
                            <i class="fas fa-arrow-up"></i> Registrados
                        </p>
                    </div>
                    <div class="stat-icon" style="background-color: #e6f4ea; color: #34a853;">
                        <i class="fas fa-car"></i>
                    </div>
                </div>
            </div>

            <!-- Órdenes -->
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Órdenes Activas</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--color-secondary);">{{ $stats['ordenes_activas'] }}</p>
                        <p class="text-xs mt-2" style="color: var(--color-warning);">
                            <i class="fas fa-clock"></i> En proceso
                        </p>
                    </div>
                    <div class="stat-icon" style="background-color: #fef7e0; color: #fbbc04;">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>

            <!-- Refacciones -->
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Refacciones</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--color-secondary);">{{ $stats['refacciones'] }}</p>
                        <p class="text-xs mt-2" style="color: var(--color-error);">
                            <i class="fas fa-exclamation-triangle"></i> {{ $stats['stock_bajo'] }} en stock bajo
                        </p>
                    </div>
                    <div class="stat-icon" style="background-color: #fce8e6; color: #ea4335;">
                        <i class="fas fa-cogs"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Orders -->
            <div class="lg:col-span-2 card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock mr-2" style="color: var(--color-primary);"></i>
                        Órdenes Recientes
                    </h3>
                    <a href="{{ route('ordenes.index') }}" class="text-sm font-medium" style="color: var(--color-primary);">
                        Ver todas <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Vehículo</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ordenes_recientes as $orden)
                                <tr>
                                    <td class="font-medium">#{{ $orden->id }}</td>
                                    <td>{{ $orden->cliente->nombre }}</td>
                                    <td>{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($orden->estado) {
                                                'pendiente' => 'badge-warning',
                                                'en_proceso' => 'badge-primary',
                                                'completada' => 'badge-success',
                                                'cancelada' => 'badge-error',
                                                default => 'badge-primary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                                        </span>
                                    </td>
                                    <td>{{ $orden->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8" style="color: var(--color-secondary-light);">
                                        <i class="fas fa-inbox text-4xl mb-2"></i>
                                        <p>No hay órdenes recientes</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="space-y-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt mr-2" style="color: var(--color-primary);"></i>
                            Acciones Rápidas
                        </h3>
                    </div>
                    <div class="card-body space-y-3">
                        <a href="{{ route('clientes.create') }}" class="btn btn-primary w-full">
                            <i class="fas fa-user-plus"></i>
                            Nuevo Cliente
                        </a>
                        <a href="{{ route('vehiculos.create') }}" class="btn btn-secondary w-full">
                            <i class="fas fa-car"></i>
                            Nuevo Vehículo
                        </a>
                        @if(auth()->user()->role !== 'mecanico')
                        <a href="{{ route('ordenes.create') }}" class="btn btn-secondary w-full">
                            <i class="fas fa-clipboard-list"></i>
                            Nueva Orden
                        </a>
                        @endif
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('refacciones.create') }}" class="btn btn-secondary w-full">
                            <i class="fas fa-cogs"></i>
                            Nueva Refacción
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Stock Alert -->
                @if($stats['stock_bajo'] > 0)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mt-0.5"></i>
                    <div>
                        <p class="font-medium">Alerta de Stock</p>
                        <p class="text-sm mt-1">Tienes {{ $stats['stock_bajo'] }} refacciones con stock bajo</p>
                        <a href="{{ route('refacciones.index') }}" class="text-sm font-medium mt-2 inline-block" style="color: var(--color-primary);">
                            Ver refacciones <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
