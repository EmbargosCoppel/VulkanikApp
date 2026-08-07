<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold" style="color: var(--color-secondary);">
                    Mecánicos
                </h2>
                <p class="mt-1 text-sm" style="color: var(--color-secondary-light);">
                    Gestión de mecánicos del taller
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-cog mr-2" style="color: var(--color-primary);"></i>
                    Lista de Mecánicos
                </h3>
                <a href="{{ route('mecanicos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Nuevo Mecánico
                </a>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Órdenes Activas</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mecanicos as $mecanico)
                            <tr>
                                <td class="font-medium">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                            {{ substr($mecanico->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div>{{ $mecanico->name }}</div>
                                            <div class="text-xs" style="color: var(--color-secondary-light);">ID: {{ $mecanico->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $mecanico->email }}</td>
                                <td>
                                    @if($mecanico->ordenes_asignadas_count > 0)
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clipboard-list mr-1"></i>
                                        {{ $mecanico->ordenes_asignadas_count }} activas
                                    </span>
                                    @else
                                    <span class="badge badge-success">
                                        <i class="fas fa-check mr-1"></i>
                                        Sin órdenes
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    @if($mecanico->trashed())
                                        <span class="badge badge-danger">Inactivo</span>
                                    @else
                                        <span class="badge badge-success">Activo</span>
                                    @endif
                                </td>
                                <td>{{ $mecanico->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="flex gap-2">
                                        <a href="{{ route('mecanicos.show', $mecanico) }}" class="text-sm" style="color: var(--color-primary);" title="Ver órdenes asignadas">
                                            <i class="fas fa-eye"></i> Ver órdenes
                                        </a>
                                        <a href="{{ route('mecanicos.edit', $mecanico) }}" class="text-sm" style="color: var(--color-warning);" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($mecanico->trashed())
                                        <form action="{{ route('mecanicos.restore', $mecanico->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-sm" style="color: var(--color-green);" title="Reactivar">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('mecanicos.destroy', $mecanico) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-sm" style="color: var(--color-error);" title="Dar de baja"
                                                onclick="confirmDelete(this, '¿Dar de baja al mecánico {{ $mecanico->name }}?')">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-8" style="color: var(--color-secondary-light);">
                                    <i class="fas fa-user-cog text-4xl mb-2"></i>
                                    <p>No hay mecánicos registrados</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $mecanicos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>