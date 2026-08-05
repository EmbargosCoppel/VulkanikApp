<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold" style="color: var(--color-secondary);">
                    Nueva Orden de Trabajo
                </h2>
                <p class="mt-1 text-sm" style="color: var(--color-secondary-light);">
                    Completa el formulario para crear una nueva orden
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clipboard-list mr-2" style="color: var(--color-primary);"></i>
                    Información de la Orden
                </h3>
                <a href="{{ route('ordenes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
            </div>

            <form action="{{ route('ordenes.store') }}" method="POST">
                @csrf
                <div class="card-body space-y-6">
                    <!-- Vehículo -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-car mr-2" style="color: var(--color-primary);"></i>
                            Vehículo *
                        </label>
                        <select name="vehiculo_id" required class="form-input">
                            <option value="">Seleccionar vehículo</option>
                            @foreach($vehiculos as $vehiculo)
                            <option value="{{ $vehiculo->id }}" {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placa }}) - {{ $vehiculo->cliente->nombre }}
                            </option>
                            @endforeach
                        </select>
                        @error('vehiculo_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <!-- Mecánico -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user-cog mr-2" style="color: var(--color-primary);"></i>
                            Mecánico *
                        </label>
                        @if(auth()->user()->role === 'admin')
                        <select name="mecanico_id" required class="form-input">
                            <option value="">Seleccionar mecánico</option>
                            @foreach($mecanicos as $mecanico)
                            <option value="{{ $mecanico->id }}" {{ old('mecanico_id') == $mecanico->id ? 'selected' : '' }}>
                                {{ $mecanico->name }}
                            </option>
                            @endforeach
                        </select>
                        @else
                        <input type="hidden" name="mecanico_id" value="{{ auth()->id() }}">
                        <input type="text" class="form-input" value="{{ auth()->user()->name }}" disabled>
                        <p class="text-xs mt-1" style="color: var(--color-secondary-light);">Como mecánico, serás asignado automáticamente a esta orden.</p>
                        @endif
                        @error('mecanico_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <!-- Estado -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-tasks mr-2" style="color: var(--color-primary);"></i>
                            Estado Inicial *
                        </label>
                        <select name="estado" required class="form-input">
                            <option value="">Seleccionar estado</option>
                            <option value="diagnóstico" {{ old('estado') == 'diagnóstico' ? 'selected' : '' }}>
                                🔍 Diagnóstico
                            </option>
                            <option value="esperando_piezas" {{ old('estado') == 'esperando_piezas' ? 'selected' : '' }}>
                                ⏳ Esperando Piezas
                            </option>
                            <option value="reparación" {{ old('estado') == 'reparación' ? 'selected' : '' }}>
                                🔧 En Reparación
                            </option>
                            <option value="completada" {{ old('estado') == 'completada' ? 'selected' : '' }}>
                                ✅ Completada
                            </option>
                        </select>
                        @error('estado') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <!-- Diagnóstico -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-stethoscope mr-2" style="color: var(--color-primary);"></i>
                            Diagnóstico
                        </label>
                        <textarea name="diagnostico" rows="4" class="form-input" placeholder="Describe el problema del vehículo...">{{ old('diagnostico') }}</textarea>
                        @error('diagnostico') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <a href="{{ route('ordenes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Crear Orden
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
