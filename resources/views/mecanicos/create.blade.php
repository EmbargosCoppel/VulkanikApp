<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold" style="color: var(--color-secondary);">
                    Nuevo Mecánico
                </h2>
                <p class="mt-1 text-sm" style="color: var(--color-secondary-light);">
                    Registra un nuevo mecánico en el sistema
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-plus mr-2" style="color: var(--color-primary);"></i>
                    Información del Mecánico
                </h3>
                <a href="{{ route('mecanicos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
            </div>

            <form action="{{ route('mecanicos.store') }}" method="POST">
                @csrf
                <div class="card-body space-y-6">
                    <!-- Nombre -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user mr-2" style="color: var(--color-primary);"></i>
                            Nombre Completo *
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="form-input" placeholder="Ej: Juan Pérez">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope mr-2" style="color: var(--color-primary);"></i>
                            Email *
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="form-input" placeholder="Ej: nombre@correo.com">
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <!-- Contraseña -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-lock mr-2" style="color: var(--color-primary);"></i>
                                Contraseña *
                            </label>
                            <input type="password" name="password" required class="form-input" placeholder="Mínimo 8 caracteres">
                            @error('password') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-lock mr-2" style="color: var(--color-primary);"></i>
                                Confirmar Contraseña *
                            </label>
                            <input type="password" name="password_confirmation" required class="form-input" placeholder="Repite la contraseña">
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle mt-0.5" style="color: var(--color-primary);"></i>
                            <div>
                                <p class="text-sm font-medium" style="color: var(--color-secondary);">Rol asignado</p>
                                <p class="text-sm mt-1" style="color: var(--color-secondary-light);">
                                    El usuario será registrado con el rol de <strong>Mecánico</strong> y podrá acceder al sistema con sus credenciales.
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
                        Guardar Mecánico
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>