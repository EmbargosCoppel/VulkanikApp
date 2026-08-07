<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Órdenes de Trabajo
        </h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>Órdenes de Trabajo
        </h1>
            @if(auth()->user()->role !== 'mecanico')
            <a href="{{ route('ordenes.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Nueva Orden
            </a>
            @endif
        <div class="relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por placa, cliente o estado..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="absolute right-3 top-2.5 text-gray-400">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mecánico</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Entrada</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($ordenes as $orden)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $orden->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} ({{ $orden->vehiculo->placa }})
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $orden->vehiculo->cliente->nombre }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $orden->mecanico->name ?? 'No asignado' }}</td>
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
                        <a href="{{ route('ordenes.edit', $orden) }}" class="text-yellow-600 hover:text-yellow-900">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">No hay órdenes de trabajo</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-6">
        {{ $ordenes->appends(request()->query())->links() }}
    </div>
</div>
</x-app-layout>
