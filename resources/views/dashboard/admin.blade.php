ionable por un boton que despliegue una lista porfavor
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold" style="color: var(--color-secondary);">
                    Dashboard Administrador
                </h2>
                <p class="mt-1 text-sm" style="color: var(--color-secondary-light);">
                    Vista general del sistema
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
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Clientes</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--color-secondary);">{{ $stats['clientes'] }}</p>
                        <p class="text-xs mt-2" style="color: var(--color-success);">
                            <i class="fas fa-arrow-up"></i> Registrados
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
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Vehículos</p>
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
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Órdenes</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--color-secondary);">{{ $stats['ordenes'] }}</p>
                        <p class="text-xs mt-2" style="color: var(--color-info);">
                            <i class="fas fa-clipboard-list"></i> Totales
                        </p>
                    </div>
                    <div class="stat-icon" style="background-color: #e8f0fe; color: #1a73e8;">
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

        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Órdenes Pendientes</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--color-warning);">{{ $stats['ordenes_pendientes'] }}</p>
                        <p class="text-xs mt-2" style="color: var(--color-secondary-light);">
                            <i class="fas fa-clock"></i> En proceso
                        </p>
                    </div>
                    <div class="stat-icon" style="background-color: #fef7e0; color: #fbbc04;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Órdenes Finalizadas</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--color-success);">{{ $stats['ordenes_finalizadas'] }}</p>
                        <p class="text-xs mt-2" style="color: var(--color-secondary-light);">
                            <i class="fas fa-check-circle"></i> Completadas
                        </p>
                    </div>
                    <div class="stat-icon" style="background-color: #e6f4ea; color: #34a853;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--color-secondary-light);">Stock Bajo</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--color-error);">{{ $stats['stock_bajo'] }}</p>
                        <p class="text-xs mt-2" style="color: var(--color-secondary-light);">
                            <i class="fas fa-exclamation-triangle"></i> Requieren atención
                        </p>
                    </div>
                    <div class="stat-icon" style="background-color: #fce8e6; color: #ea4335;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Orders -->
            <div class="card">
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
                    <div class="space-y-3">
                        @forelse($ordenes_recientes as $orden)
                        <div class="flex items-center justify-between p-4 rounded-lg transition-all duration-300 hover:shadow-md" style="background-color: var(--color-bg-secondary);">
                            <div class="flex-1">
                                <p class="font-medium" style="color: var(--color-secondary);">#{{ $orden->id }} - {{ $orden->vehiculo->cliente->nombre }}</p>
                                <p class="text-sm mt-1" style="color: var(--color-secondary-light);">
                                    {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}
                                </p>
                                <p class="text-xs mt-1" style="color: var(--color-secondary-light);">
                                    {{ $orden->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
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
                        </div>
                        @empty
                        <div class="text-center py-8" style="color: var(--color-secondary-light);">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No hay órdenes recientes</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle mr-2" style="color: var(--color-warning);"></i>
                        Stock Bajo
                    </h3>
                    <a href="{{ route('refacciones.index') }}" class="text-sm font-medium" style="color: var(--color-primary);">
                        Ver todas <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="space-y-3">
                        @forelse($refacciones_stock_bajo as $refaccion)
                        <div class="flex items-center justify-between p-4 rounded-lg transition-all duration-300 hover:shadow-md" style="background-color: #fef7e0;">
                            <div class="flex-1">
                                <p class="font-medium" style="color: var(--color-secondary);">{{ $refaccion->nombre }}</p>
                                <p class="text-sm mt-1" style="color: var(--color-secondary-light);">SKU: {{ $refaccion->sku }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold" style="color: var(--color-error);">{{ $refaccion->stock_actual }}</p>
                                <p class="text-xs" style="color: var(--color-secondary-light);">Mín: {{ $refaccion->stock_minimo }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8" style="color: var(--color-secondary-light);">
                            <i class="fas fa-check-circle text-4xl mb-2" style="color: var(--color-success);"></i>
                            <p>No hay refacciones con stock bajo</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
