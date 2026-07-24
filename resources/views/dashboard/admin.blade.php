<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Administrador') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                <h1 class="text-3xl font-bold text-gray-800">
                    Dashboard Administrador
                </h1>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Clientes</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $stats['clientes'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Vehículos</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $stats['vehiculos'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Órdenes</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $stats['ordenes'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Refacciones</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $stats['refacciones'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Órdenes Pendientes</p>
                                <p class="text-3xl font-bold text-orange-600">{{ $stats['ordenes_pendientes'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Órdenes Finalizadas</p>
                                <p class="text-3xl font-bold text-green-600">{{ $stats['ordenes_finalizadas'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Stock Bajo</p>
                                <p class="text-3xl font-bold text-red-600">{{ $stats['stock_bajo'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Orders -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">
                            Órdenes Recientes
                        </h2>
                        <div class="space-y-3">
                            @forelse($ordenes_recientes as $orden)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">#{{ $orden->id }} - {{ $orden->vehiculo->cliente->nombre }}</p>
                                    <p class="text-sm text-gray-500">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($orden->estado === 'finalizado') bg-green-100 text-green-800
                                    @elseif($orden->estado === 'reparación') bg-orange-100 text-orange-800
                                    @elseif($orden->estado === 'esperando_piezas') bg-yellow-100 text-yellow-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst($orden->estado) }}
                                </span>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center">No hay órdenes recientes</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Low Stock -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">
                            Refacciones con Stock Bajo
                        </h2>
                        <div class="space-y-3">
                            @forelse($refacciones_stock_bajo as $refaccion)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $refaccion->nombre }}</p>
                                    <p class="text-sm text-gray-500">SKU: {{ $refaccion->sku }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-red-600">{{ $refaccion->stock_actual }}</p>
                                    <p class="text-xs text-gray-500">Mín: {{ $refaccion->stock_minimo }}</p>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center">No hay refacciones con stock bajo</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
