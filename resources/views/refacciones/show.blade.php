<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detalle Refacción
        </h2>
    </x-slot>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-box mr-2 text-blue-600"></i>Detalle de Refacción
        </h1>
        <a href="{{ route('refacciones.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-1"></i>Volver
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">Información General</h2>
            <div class="space-y-2">
                <p><span class="font-medium">Nombre:</span> {{ $refaccion->nombre }}</p>
                <p><span class="font-medium">SKU:</span> {{ $refaccion->sku }}</p>
                <p><span class="font-medium">Descripción:</span> {{ $refaccion->descripcion ?? '-' }}</p>
                <p><span class="font-medium">Proveedor:</span> {{ $refaccion->proveedor ?? '-' }}</p>
                <p><span class="font-medium">Ubicación:</span> {{ $refaccion->ubicacion ?? '-' }}</p>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">Información de Stock y Precios</h2>
            <div class="space-y-2">
                <p><span class="font-medium">Stock Actual:</span> {{ $refaccion->stock_actual }}</p>
                <p><span class="font-medium">Stock Mínimo:</span> {{ $refaccion->stock_minimo }}</p>
                <p><span class="font-medium">Estado:</span>
                    @if($refaccion->activo)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>
                    @endif
                </p>
                <p><span class="font-medium">Costo:</span> ${{ number_format($refaccion->costo, 2) }}</p>
                <p><span class="font-medium">Precio Venta:</span> ${{ number_format($refaccion->precio_venta, 2) }}</p>
                <p><span class="font-medium">Margen:</span> 
                    @if($refaccion->costo > 0)
                        {{ number_format((($refaccion->precio_venta - $refaccion->costo) / $refaccion->costo) * 100, 2) }}%
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="flex justify-end space-x-3">
        <a href="{{ route('refacciones.stock', ['refaccion' => $refaccion]) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            <i class="fas fa-boxes mr-2"></i>Actualizar Stock
        </a>
        <a href="{{ route('refacciones.edit', ['refaccione' => $refaccion]) }}" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
            <i class="fas fa-edit mr-2"></i>Editar
        </a>
    </div>
</div>
</x-app-layout>
