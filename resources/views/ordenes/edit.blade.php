<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Editar Orden de Trabajo #{{ $ordenTrabajo->id }}
        </h2>
    </x-slot>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-edit mr-2 text-blue-600"></i>Editar Orden de Trabajo #{{ $ordenTrabajo->id }}
        </h1>
        <a href="{{ route('ordenes.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-1"></i>Volver
        </a>
    </div>

    <form action="{{ route('ordenes.update', $ordenTrabajo) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="redirect_to" value="show">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Vehículo:</span> {{ $ordenTrabajo->vehiculo->marca }} {{ $ordenTrabajo->vehiculo->modelo }} ({{ $ordenTrabajo->vehiculo->placa }})
                </p>
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Cliente:</span> {{ $ordenTrabajo->vehiculo->cliente->nombre }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select name="estado"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="diagnóstico" {{ $ordenTrabajo->estado === 'diagnóstico' ? 'selected' : '' }}>Diagnóstico</option>
                    <option value="esperando_piezas" {{ $ordenTrabajo->estado === 'esperando_piezas' ? 'selected' : '' }}>Esperando Piezas</option>
                    <option value="reparación" {{ $ordenTrabajo->estado === 'reparación' ? 'selected' : '' }}>Reparación</option>
                    <option value="finalizado" {{ $ordenTrabajo->estado === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                </select>
                @error('estado') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mecánico</label>
                @if(auth()->user()->role === 'admin')
                <select name="mecanico_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccionar mecánico</option>
                    @foreach($mecanicos as $mecanico)
                    <option value="{{ $mecanico->id }}" {{ $ordenTrabajo->mecanico_id == $mecanico->id ? 'selected' : '' }}>
                        {{ $mecanico->name }}
                    </option>
                    @endforeach
                </select>
                @else
                <input type="hidden" name="mecanico_id" value="{{ auth()->id() }}">
                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100" value="{{ auth()->user()->name }}" disabled>
                <p class="text-xs text-gray-500 mt-1">Solo puedes cambiar el estado de la orden asignada y agregar refacciones.</p>
                @endif
                @error('mecanico_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Diagnóstico</label>
                <textarea name="diagnostico" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $ordenTrabajo->diagnostico }}</textarea>
                @error('diagnostico') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Trabajos Realizados</label>
                <textarea name="trabajos_realizados" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $ordenTrabajo->trabajos_realizados }}</textarea>
                @error('trabajos_realizados') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mano de Obra ($)</label>
                <input type="number" name="mano_obra" value="{{ $ordenTrabajo->mano_obra }}" step="0.01" min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('mano_obra') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                <textarea name="observaciones" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $ordenTrabajo->observaciones }}</textarea>
                @error('observaciones') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-6 grid gap-3 md:grid-cols-2">
            @if(auth()->user()->role === 'admin' && !$ordenTrabajo->estaFinalizada())
            <a href="{{ route('ordenes.pagar', $ordenTrabajo) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                <i class="fas fa-credit-card mr-2"></i>Cobrar Orden
            </a>
            @endif
            <div class="flex justify-end space-x-3">
                <a href="{{ route('ordenes.index') }}" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Actualizar
                </button>
            </div>
        </div>
    </form>
</div>
</x-app-layout>
