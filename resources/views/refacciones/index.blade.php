<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Inventario de Refacciones
        </h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-boxes mr-2 text-blue-600"></i>Inventario de Refacciones
        </h1>
        <div class="space-x-2">
            <a href="{{ route('refacciones.stock-bajo') }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                <i class="fas fa-exclamation-triangle mr-2"></i>Stock Bajo
            </a>
            <a href="{{ route('refacciones.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Nueva Refacción
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Venta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($refacciones as $refaccion)
                <tr class="hover:bg-gray-50 {{ $refaccion->estaBajoStock() ? 'bg-yellow-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $refaccion->sku }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $refaccion->nombre }}</div>
                        <div class="text-sm text-gray-500">{{ $refaccion->descripcion }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium {{ $refaccion->estaBajoStock() ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $refaccion->stock_actual }}
                        </span>
                        <span class="text-xs text-gray-500">/ {{ $refaccion->stock_minimo }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($refaccion->precio_venta, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $refaccion->proveedor ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($refaccion->activo)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                        @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('refacciones.show', $refaccion) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('refacciones.edit', $refaccion) }}" class="text-yellow-600 hover:text-yellow-900">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('refacciones.stock', $refaccion) }}" class="text-green-600 hover:text-green-900" title="Actualizar stock">
                            <i class="fas fa-boxes"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay refacciones registradas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
