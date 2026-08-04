<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold" style="color: var(--color-secondary);">
                    Nueva Refacción
                </h2>
                <p class="mt-1 text-sm" style="color: var(--color-secondary-light);">
                    Agrega una nueva refacción al inventario
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-box mr-2" style="color: var(--color-primary);"></i>
                    Información de la Refacción
                </h3>
                <a href="{{ route('refacciones.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
            </div>

            <form action="{{ route('refacciones.store') }}" method="POST">
                @csrf
                <div class="card-body space-y-6">
                    <!-- Nombre y SKU -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-tag mr-2" style="color: var(--color-primary);"></i>
                                Nombre *
                            </label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" required class="form-input" placeholder="Ej: Filtro de aceite">
                            @error('nombre') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-barcode mr-2" style="color: var(--color-primary);"></i>
                                SKU *
                            </label>
                            <input type="text" name="sku" value="{{ old('sku') }}" required class="form-input" placeholder="Ej: REF-001">
                            @error('sku') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-align-left mr-2" style="color: var(--color-primary);"></i>
                            Descripción
                        </label>
                        <textarea name="descripcion" rows="3" class="form-input" placeholder="Describe la refacción...">{{ old('descripcion') }}</textarea>
                        @error('descripcion') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <!-- Costo y Precio -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-dollar-sign mr-2" style="color: var(--color-primary);"></i>
                                Costo *
                            </label>
                            <input type="number" name="costo" value="{{ old('costo') }}" step="0.01" min="0" required class="form-input" placeholder="0.00">
                            @error('costo') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-dollar-sign mr-2" style="color: var(--color-success);"></i>
                                Precio de Venta *
                            </label>
                            <input type="number" name="precio_venta" value="{{ old('precio_venta') }}" step="0.01" min="0" required class="form-input" placeholder="0.00">
                            @error('precio_venta') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Stock -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-boxes mr-2" style="color: var(--color-primary);"></i>
                                Stock Actual *
                            </label>
                            <input type="number" name="stock_actual" value="{{ old('stock_actual') }}" min="0" required class="form-input" placeholder="0">
                            @error('stock_actual') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-exclamation-triangle mr-2" style="color: var(--color-warning);"></i>
                                Stock Mínimo *
                            </label>
                            <input type="number" name="stock_minimo" value="{{ old('stock_minimo') }}" min="0" required class="form-input" placeholder="0">
                            @error('stock_minimo') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Ubicación y Proveedor -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-map-marker-alt mr-2" style="color: var(--color-primary);"></i>
                                Ubicación
                            </label>
                            <input type="text" name="ubicacion" value="{{ old('ubicacion') }}" class="form-input" placeholder="Ej: Estante A-1">
                            @error('ubicacion') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-truck mr-2" style="color: var(--color-primary);"></i>
                                Proveedor
                            </label>
                            <input type="text" name="proveedor" value="{{ old('proveedor') }}" class="form-input" placeholder="Ej: AutoPartes MX">
                            @error('proveedor') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-toggle-on mr-2" style="color: var(--color-primary);"></i>
                            Estado
                        </label>
                        <select name="activo" class="form-input">
                            <option value="1" {{ old('activo', '1') == '1' ? 'selected' : '' }}>
                                ✅ Activo
                            </option>
                            <option value="0" {{ old('activo') == '0' ? 'selected' : '' }}>
                                ❌ Inactivo
                            </option>
                        </select>
                        @error('activo') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <a href="{{ route('refacciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Guardar Refacción
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
