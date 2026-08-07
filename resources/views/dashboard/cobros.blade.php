<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold" style="color: var(--color-secondary);">
                    Cobros de Órdenes
                </h2>
                <p class="mt-1 text-sm" style="color: var(--color-secondary-light);">
                    Lista de órdenes que pueden cobrarse ahora mismo
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="card">
            <div class="card-body">
                @if($ordenes_cobrables->isEmpty())
                    <div class="text-center py-10" style="color: var(--color-secondary-light);">
                        <i class="fas fa-check-circle text-4xl mb-4" style="color: var(--color-success);"></i>
                        <p>No hay órdenes pendientes de cobro.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($ordenes_cobrables as $orden)
                            <div class="p-5 rounded-lg shadow-sm" style="background: var(--color-bg-secondary);">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-medium" style="color: var(--color-secondary);">Orden #{{ $orden->id }}</p>
                                        <p class="text-lg font-semibold" style="color: var(--color-secondary);">{{ $orden->vehiculo->cliente->nombre }}</p>
                                        <p class="text-sm" style="color: var(--color-secondary-light);">
                                            {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} – Estado: {{ ucfirst($orden->estado) }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500">Total</p>
                                        <p class="text-2xl font-bold" style="color: var(--color-success);">${{ number_format($orden->total, 2) }}</p>
                                    </div>
                                </div>
                                <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <a href="{{ route('ordenes.show', $orden) }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">
                                        Ver detalles
                                    </a>
                                    <a href="{{ route('ordenes.pagar', $orden) }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                        Cobrar ahora
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
