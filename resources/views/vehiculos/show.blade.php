<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detalle Vehículo
        </h2>
    </x-slot>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-car mr-2 text-blue-600"></i>Detalle del Vehículo
        </h1>
        <a href="{{ route('vehiculos.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-1"></i>Volver
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">Información del Vehículo</h2>
            <div class="space-y-2">
                <p><span class="font-medium">Marca:</span> {{ $vehiculo->marca }}</p>
                <p><span class="font-medium">Modelo:</span> {{ $vehiculo->modelo }}</p>
                <p><span class="font-medium">Año:</span> {{ $vehiculo->anio }}</p>
                <p><span class="font-medium">Placa:</span> {{ $vehiculo->placa }}</p>
                <p><span class="font-medium">Color:</span> {{ $vehiculo->color ?? '-' }}</p>
                <p><span class="font-medium">VIN:</span> {{ $vehiculo->vin ?? '-' }}</p>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">Información del Cliente</h2>
            <div class="space-y-2">
                <p><span class="font-medium">Nombre:</span> {{ $vehiculo->cliente->nombre }}</p>
                <p><span class="font-medium">Teléfono:</span> {{ $vehiculo->cliente->telefono }}</p>
                <p><span class="font-medium">Email:</span> {{ $vehiculo->cliente->email ?? '-' }}</p>
            </div>
        </div>
    </div>

    @if($vehiculo->notas)
    <div class="bg-gray-50 p-4 rounded-lg mb-8">
        <h2 class="text-lg font-semibold mb-2">Notas</h2>
        <p class="text-sm text-gray-700">{{ $vehiculo->notas }}</p>
    </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Órdenes de Trabajo</h2>
        @if(auth()->user()->role !== 'mecanico')
        <a href="{{ route('ordenes.create') }}?vehiculo_id={{ $vehiculo->id }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Crear Orden
        </a>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mecánico</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($vehiculo->ordenesTrabajo as $orden)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $orden->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $estados = [
                                'diagnóstico' => 'bg-blue-100 text-blue-800',
                                'esperando_piezas' => 'bg-yellow-100 text-yellow-800',
                                'reparación' => 'bg-orange-100 text-orange-800',
                                'finalizado' => 'bg-green-100 text-green-800',
                            ];
                            $estadoClass = $estados[$orden->estado] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $estadoClass }}">
                            {{ ucfirst($orden->estado) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $orden->mecanico->name ?? 'No asignado' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $orden->fecha_entrada->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        @if($orden->total)
                            ${{ number_format($orden->total, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('ordenes.show', $orden) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay órdenes de trabajo</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
