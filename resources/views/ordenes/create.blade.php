<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Nueva Orden de Trabajo
        </h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>Nueva Orden de Trabajo
        </h1>
        <a href="{{ route('ordenes.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-1"></i>Volver
        </a>
    </div>

    <form action="{{ route('ordenes.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Vehículo *</label>
                <select name="vehiculo_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccionar vehículo</option>
                    @foreach($vehiculos as $vehiculo)
                    <option value="{{ $vehiculo->id }}" {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                        {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placa }}) - {{ $vehiculo->cliente->nombre }}
                    </option>
                    @endforeach
                </select>
                @error('vehiculo_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Mecánico *</label>
                <select name="mecanico_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccionar mecánico</option>
                    @foreach($mecanicos as $mecanico)
                    <option value="{{ $mecanico->id }}" {{ old('mecanico_id') == $mecanico->id ? 'selected' : '' }}>
                        {{ $mecanico->name }}
                    </option>
                    @endforeach
                </select>
                @error('mecanico_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Diagnóstico</label>
                <textarea name="diagnostico" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('diagnostico') }}</textarea>
                @error('diagnostico') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('ordenes.index') }}" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Crear Orden
            </button>
        </div>
    </form>
</div>
</x-app-layout>
