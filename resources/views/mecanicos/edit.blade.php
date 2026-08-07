<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold" style="color: var(--color-secondary);">
                    Editar Mecánico
                </h2>
                <p class="mt-1 text-sm" style="color: var(--color-secondary-light);">
                    Actualiza la información del mecánico
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-edit mr-2" style="color: var(--color-primary);"></i>
                    Información del Mecánico
                </h3>
                <a href="{{ route('mecanicos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
            </div>

            <form action="{{ route('mecanicos.update', $mecanico) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body space-y-6">
                    <!-- Nombre -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user mr-2" style="color: var(--color-primary);"></i>
                            Nombre Completo *
                        </label>
                        <input type="text" name="name" value="{{ old('name', $mecanico->name) }}" required class="form-input" placeholder="Ej: Juan Pérez">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope mr-2" style="color: var(--color-primary);"></i>
                            Email *
                        </label>
                        <input type="email" name="email" value="{{ old('email', $mecanico->email) }}" required class="form-input" placeholder="Ej: nombre@correo.com">
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <!-- Contraseña (opcional) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-lock mr-2" style="color: var(--color-primary);"></i>
                                Nueva Contraseña
                            </label>
                            <input type="password" name="password" class="form-input" placeholder="Dejar vacío para no cambiar">
                            @error('password') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-lock mr-2" style="color: var(--color-primary);"></i>
                                Confirmar Nueva Contraseña
                            </label>
                            <input type="password" name="password_confirmation" class="form-input" placeholder="Repite la nueva contraseña">
                        </div>
                    </div>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle mt-0.5" style="color: var(--color-secondary-light);"></i>
                            <div>
                                <p class="text-sm font-medium" style="color: var(--color-secondary);">Información de la cuenta</p>
                                <p class="text-sm mt-1" style="color: var(--color-secondary-light);">
                                    Registrado el {{ $mecanico->created_at->format('d/m/Y') }} · Rol: <strong>Mecánico</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <a href="{{ route('mecanicos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Actualizar Mecánico
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>