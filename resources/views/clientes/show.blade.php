<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detalle Cliente
        </h2>
    </x-slot>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-user mr-2 text-blue-600"></i>Detalle del Cliente
        </h1>
        <a href="{{ route('clientes.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-1"></i>Volver
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">Información Personal</h2>
            <div class="space-y-2">
                <p><span class="font-medium">Nombre:</span> {{ $cliente->nombre }}</p>
                <p><span class="font-medium">Teléfono:</span> {{ $cliente->telefono }}</p>
                <p><span class="font-medium">Email:</span> {{ $cliente->email ?? '-' }}</p>
                <p><span class="font-medium">RFC:</span> {{ $cliente->rfc ?? '-' }}</p>
                <p><span class="font-medium">Dirección:</span> {{ $cliente->direccion ?? '-' }}</p>
                <p><span class="font-medium">Tipo:</span>
                    @if($cliente->es_empresa)
                        Empresa ({{ $cliente->nombre_empresa ?? '-' }})
                    @else
                        Particular
                    @endif
                </p>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">Estadísticas</h2>
            <div class="space-y-2">
                <p><span class="font-medium">Vehículos registrados:</span> {{ $cliente->vehiculos->count() }}</p>
                <p><span class="font-medium">Órdenes de trabajo:</span> {{ $cliente->vehiculos->sum(function($v) { return $v->ordenesTrabajo->count(); }) }}</p>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Vehículos</h2>
        <a href="{{ route('vehiculos.create') }}?cliente_id={{ $cliente->id }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Agregar Vehículo
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca/Modelo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Año</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Órdenes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($cliente->vehiculos as $vehiculo)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $vehiculo->marca }}</div>
                        <div class="text-sm text-gray-500">{{ $vehiculo->modelo }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vehiculo->anio }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $vehiculo->placa }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vehiculo->ordenesTrabajo->count() }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('vehiculos.show', $vehiculo) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('ordenes.create') }}?vehiculo_id={{ $vehiculo->id }}" class="text-green-600 hover:text-green-900" title="Crear orden">
                            <i class="fas fa-clipboard-list"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay vehículos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
