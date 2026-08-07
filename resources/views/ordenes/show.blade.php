<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Orden de Trabajo #{{ $ordenTrabajo->id }}
        </h2>
    </x-slot>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>Orden de Trabajo #{{ $ordenTrabajo->id }}
            </h1>
            <a href="{{ route('ordenes.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-1"></i>Volver
            </a>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('ordenes.ticket', $ordenTrabajo) }}" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">
                <i class="fas fa-receipt mr-1"></i>Ver Ticket
            </a>
            @if(auth()->user()->role === 'admin' && !$ordenTrabajo->estaFinalizada())
            <a href="{{ route('ordenes.pagar', $ordenTrabajo) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-credit-card mr-1"></i>Procesar Pago
            </a>
            @endif
            <a href="{{ route('ordenes.edit', $ordenTrabajo) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-edit mr-1"></i>Editar
            </a>
        </div>
        @if(auth()->user()->role === 'admin' && !$ordenTrabajo->estaFinalizada())
        <div class="mt-4 p-4 border border-green-200 rounded-lg bg-green-50 text-green-900">
            <p class="font-semibold">Acción disponible solo para administradores:</p>
            <p class="text-sm">Puedes cobrar esta orden con Stripe en cualquier etapa del proceso antes de finalizarla.</p>
        </div>
        @endif
    </div>


    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">Información General</h2>
            <div class="space-y-2 text-sm">
                <p><span class="font-medium">Estado:</span>
                    @php
                        $estados = [
                            'diagnóstico' => 'bg-blue-100 text-blue-800',
                            'esperando_piezas' => 'bg-yellow-100 text-yellow-800',
                            'reparación' => 'bg-orange-100 text-orange-800',
                            'finalizado' => 'bg-green-100 text-green-800',
                        ];
                        $estadoClass = $estados[$ordenTrabajo->estado] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $estadoClass }}">
                        {{ ucfirst($ordenTrabajo->estado) }}
                    </span>
                </p>
                <p><span class="font-medium">Fecha Entrada:</span> {{ $ordenTrabajo->fecha_entrada->format('d/m/Y H:i') }}</p>
                <p><span class="font-medium">Fecha Salida:</span> {{ $ordenTrabajo->fecha_salida ? $ordenTrabajo->fecha_salida->format('d/m/Y H:i') : '-' }}</p>
                <p><span class="font-medium">Mecánico:</span> {{ $ordenTrabajo->mecanico->name ?? 'No asignado' }}</p>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">Vehículo</h2>
            <div class="space-y-2 text-sm">
                <p><span class="font-medium">Marca/Modelo:</span> {{ $ordenTrabajo->vehiculo->marca }} {{ $ordenTrabajo->vehiculo->modelo }}</p>
                <p><span class="font-medium">Placa:</span> {{ $ordenTrabajo->vehiculo->placa }}</p>
                <p><span class="font-medium">Año:</span> {{ $ordenTrabajo->vehiculo->anio }}</p>
                <p><span class="font-medium">Cliente:</span> {{ $ordenTrabajo->vehiculo->cliente->nombre }}</p>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">Totales</h2>
            <div class="space-y-2 text-sm">
                <p><span class="font-medium">Refacciones:</span> ${{ number_format($totales['subtotal_refacciones'], 2) }}</p>
                <p><span class="font-medium">Mano de Obra:</span> ${{ number_format($totales['mano_obra'], 2) }}</p>
                <p><span class="font-medium">Subtotal:</span> ${{ number_format($totales['subtotal'], 2) }}</p>
                <p><span class="font-medium">IVA (16%):</span> ${{ number_format($totales['iva'], 2) }}</p>
                <p class="text-lg font-bold"><span class="font-medium">Total:</span> ${{ number_format($totales['total'], 2) }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">Diagnóstico</h2>
            <p class="text-sm text-gray-700">{{ $ordenTrabajo->diagnostico ?? 'Sin diagnóstico' }}</p>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">Trabajos Realizados</h2>
            <p class="text-sm text-gray-700">{{ $ordenTrabajo->trabajos_realizados ?? 'Sin trabajos registrados' }}</p>
        </div>
    </div>

    @if($ordenTrabajo->observaciones)
    <div class="bg-yellow-50 p-4 rounded-lg mb-8">
        <h2 class="text-lg font-semibold mb-2">Observaciones</h2>
        <p class="text-sm text-gray-700">{{ $ordenTrabajo->observaciones }}</p>
    </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Refacciones Utilizadas</h2>
        @if($ordenTrabajo->puedeAgregarRefacciones())
        <button onclick="document.getElementById('agregarRefaccionForm').classList.toggle('hidden')" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            <i class="fas fa-plus mr-2"></i>Agregar Refacción
        </button>
        @endif
    </div>

    <form id="agregarRefaccionForm" class="hidden bg-gray-50 p-4 rounded-lg mb-4" action="{{ route('ordenes.agregarRefaccion', $ordenTrabajo) }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Refacción</label>
                <select name="refaccion_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccionar</option>
                    @foreach(\App\Models\Refaccion::where('activo', true)->get() as $ref)
                    <option value="{{ $ref->id }}">{{ $ref->nombre }} (Stock: {{ $ref->stock_actual }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cantidad</label>
                <input type="number" name="cantidad" min="1" value="1" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Agregar
                </button>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Refacción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unitario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($ordenTrabajo->refacciones as $refaccion)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $refaccion->nombre }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $refaccion->sku }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $refaccion->pivot->cantidad }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($refaccion->pivot->precio_unitario, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${{ number_format($refaccion->pivot->subtotal, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay refacciones agregadas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
