<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold" style="color: var(--color-secondary);">
                    Tablero Kanban
                </h2>
                <p class="mt-1 text-sm" style="color: var(--color-secondary-light);">
                    Gestión visual de órdenes de trabajo
                </p>
            </div>
            <a href="{{ route('ordenes.index') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> Vista Lista
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Columna: Diagnóstico -->
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-blue-800">
                        <i class="fas fa-stethoscope mr-2"></i>Diagnóstico
                    </h3>
                    <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-sm font-semibold">
                        {{ $ordenes['diagnóstico']->count() }}
                    </span>
                </div>
                <div class="space-y-3">
                    @foreach($ordenes['diagnóstico'] as $orden)
                    <div class="bg-white rounded-lg p-4 shadow-sm border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-sm font-bold text-gray-700">#{{ $orden->id }}</span>
                            <a href="{{ route('ordenes.show', $orden) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 mb-1">
                            {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}
                        </p>
                        <p class="text-xs text-gray-600 mb-1">
                            <i class="fas fa-user mr-1"></i>{{ $orden->vehiculo->cliente->nombre }}
                        </p>
                        <p class="text-xs text-gray-600 mb-2">
                            <i class="fas fa-user-cog mr-1"></i>{{ $orden->mecanico->name ?? 'Sin asignar' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-clock mr-1"></i>{{ $orden->fecha_entrada->format('d/m/Y') }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Columna: Esperando Piezas -->
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-yellow-800">
                        <i class="fas fa-box-open mr-2"></i>Esperando Piezas
                    </h3>
                    <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-sm font-semibold">
                        {{ $ordenes['esperando_piezas']->count() }}
                    </span>
                </div>
                <div class="space-y-3">
                    @foreach($ordenes['esperando_piezas'] as $orden)
                    <div class="bg-white rounded-lg p-4 shadow-sm border-l-4 border-yellow-500 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-sm font-bold text-gray-700">#{{ $orden->id }}</span>
                            <a href="{{ route('ordenes.show', $orden) }}" class="text-yellow-600 hover:text-yellow-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 mb-1">
                            {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}
                        </p>
                        <p class="text-xs text-gray-600 mb-1">
                            <i class="fas fa-user mr-1"></i>{{ $orden->vehiculo->cliente->nombre }}
                        </p>
                        <p class="text-xs text-gray-600 mb-2">
                            <i class="fas fa-user-cog mr-1"></i>{{ $orden->mecanico->name ?? 'Sin asignar' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-clock mr-1"></i>{{ $orden->fecha_entrada->format('d/m/Y') }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Columna: Reparación -->
            <div class="bg-orange-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-orange-800">
                        <i class="fas fa-wrench mr-2"></i>Reparación
                    </h3>
                    <span class="bg-orange-200 text-orange-800 px-2 py-1 rounded-full text-sm font-semibold">
                        {{ $ordenes['reparación']->count() }}
                    </span>
                </div>
                <div class="space-y-3">
                    @foreach($ordenes['reparación'] as $orden)
                    <div class="bg-white rounded-lg p-4 shadow-sm border-l-4 border-orange-500 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-sm font-bold text-gray-700">#{{ $orden->id }}</span>
                            <a href="{{ route('ordenes.show', $orden) }}" class="text-orange-600 hover:text-orange-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 mb-1">
                            {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}
                        </p>
                        <p class="text-xs text-gray-600 mb-1">
                            <i class="fas fa-user mr-1"></i>{{ $orden->vehiculo->cliente->nombre }}
                        </p>
                        <p class="text-xs text-gray-600 mb-2">
                            <i class="fas fa-user-cog mr-1"></i>{{ $orden->mecanico->name ?? 'Sin asignar' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-clock mr-1"></i>{{ $orden->fecha_entrada->format('d/m/Y') }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Columna: Finalizado -->
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-green-800">
                        <i class="fas fa-check-circle mr-2"></i>Finalizado
                    </h3>
                    <span class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-sm font-semibold">
                        {{ $ordenes['finalizado']->count() }}
                    </span>
                </div>
                <div class="space-y-3">
                    @foreach($ordenes['finalizado'] as $orden)
                    <div class="bg-white rounded-lg p-4 shadow-sm border-l-4 border-green-500 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-sm font-bold text-gray-700">#{{ $orden->id }}</span>
                            <a href="{{ route('ordenes.show', $orden) }}" class="text-green-600 hover:text-green-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 mb-1">
                            {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}
                        </p>
                        <p class="text-xs text-gray-600 mb-1">
                            <i class="fas fa-user mr-1"></i>{{ $orden->vehiculo->cliente->nombre }}
                        </p>
                        <p class="text-xs text-gray-600 mb-2">
                            <i class="fas fa-user-cog mr-1"></i>{{ $orden->mecanico->name ?? 'Sin asignar' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-clock mr-1"></i>{{ $orden->fecha_entrada->format('d/m/Y') }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>