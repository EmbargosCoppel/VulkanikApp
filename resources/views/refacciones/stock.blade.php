<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Actualizar Stock
        </h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-boxes mr-2 text-blue-600"></i>Actualizar Stock
        </h1>
        <a href="{{ route('refacciones.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-1"></i>Volver
        </a>
    </div>

    <div class="bg-gray-50 p-4 rounded-lg mb-6">
        <h2 class="text-lg font-semibold">{{ $refaccion->nombre }}</h2>
        <p class="text-gray-600">SKU: {{ $refaccion->sku }}</p>
        <p class="text-gray-600">Stock actual: {{ $refaccion->stock_actual }}</p>
    </div>

    <form action="{{ route('refacciones.actualizarStock', $refaccion) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="max-w-md">
            <label class="block text-sm font-medium text-gray-700 mb-2">Nuevo Stock *</label>
            <input type="number" name="nuevo_stock" value="{{ $refaccion->stock_actual }}" min="0" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('nuevo_stock') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('refacciones.index') }}" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Actualizar
            </button>
        </div>
    </form>
</div>
</x-app-layout>
