<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Órdenes de Trabajo
        </h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>Órdenes de Trabajo
            </h1>
            @if(auth()->user()->role !== 'mecanico')
            <a href="{{ route('ordenes.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Nueva Orden
            </a>
            @endif
        </div>

        <!-- Búsqueda -->
        <form method="GET" class="mb-4">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por placa, cliente o estado..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="absolute right-3 top-2.5 text-gray-400">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mecánico</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Entrada</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($ordenes as $orden)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $orden->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} ({{ $orden->vehiculo->placa }})
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $orden->vehiculo->cliente->nombre }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $orden->mecanico->name ?? 'No asignado' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $estados = [
                                'diagnóstico' => 'bg-blue-100 text-blue-800',
                                'esperando_piezas' => 'bg-yellow-100 text-yellow-800',
                                'reparación' => 'bg-orange-100 text-orange-800',
                                'finalizado' => 'bg-green-100 text-green-800',
                            ];
                            $estadoClass = $estados[$orden->estado] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $estadoClass }}">
                            {{ ucfirst($orden->estado) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $orden->fecha_entrada->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        @if($orden->total)
                            ${{ number_format($orden->total, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('ordenes.show', $orden) }}" class="text-blue-600 hover:text-blue-900" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('ordenes.edit', $orden) }}" class="text-yellow-600 hover:text-yellow-900" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($orden->estado !== 'finalizado')
                        <button onclick="mostrarModalPago(this)" 
                                data-orden-id="{{ $orden->id }}"
                                data-cliente-email="{{ $orden->vehiculo->cliente->email }}"
                                data-cliente-nombre="{{ $orden->vehiculo->cliente->nombre }}"
                                class="text-green-600 hover:text-green-900" 
                                title="Enviar link de pago">
                            <i class="fas fa-link"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">No hay órdenes de trabajo</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-6">
        {{ $ordenes->appends(request()->query())->links() }}
    </div>
</div>

    <!-- Modal para generar link de pago -->
    <div id="modalPago" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Enviar Link de Pago</h3>
                    <button onclick="cerrarModalPago()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-2">Cliente: <span id="modalClienteNombre" class="font-semibold"></span></p>
                    <p class="text-sm text-gray-600">Email: <span id="modalClienteEmail" class="font-semibold"></span></p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email del cliente</label>
                    <input type="email" id="emailCliente" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" readonly>
                </div>

                <div class="flex justify-end space-x-3">
                    <button onclick="cerrarModalPago()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button onclick="generarYEnviarLink()" id="btnGenerarLink" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-paper-plane mr-2"></i>Generar y Enviar Link
                    </button>
                </div>

                <div id="mensajeLink" class="hidden mt-4 p-3 rounded-lg"></div>
            </div>
        </div>
    </div>

    <!-- Toast notification -->
    <div id="toast" class="hidden fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full">
        <div class="flex items-center">
            <i id="toastIcon" class="fas mr-3"></i>
            <div>
                <p id="toastTitle" class="font-semibold"></p>
                <p id="toastMessage" class="text-sm"></p>
            </div>
        </div>
    </div>

@push('scripts')
<script>
let ordenIdActual = null;

function mostrarModalPago(button) {
    ordenIdActual = button.dataset.ordenId;
    const email = button.dataset.clienteEmail;
    const nombre = button.dataset.clienteNombre;
    
    document.getElementById('modalClienteNombre').textContent = nombre;
    document.getElementById('modalClienteEmail').textContent = email;
    document.getElementById('emailCliente').value = email;
    document.getElementById('modalPago').classList.remove('hidden');
    document.getElementById('mensajeLink').classList.add('hidden');
}

function cerrarModalPago() {
    document.getElementById('modalPago').classList.add('hidden');
    ordenIdActual = null;
}

function mostrarToast(tipo, titulo, mensaje) {
    const toast = document.getElementById('toast');
    const toastIcon = document.getElementById('toastIcon');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    
    // Limpiar clases previas
    toast.className = 'fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 transform transition-all duration-300';
    
    if (tipo === 'success') {
        toast.classList.add('bg-green-500', 'text-white');
        toastIcon.className = 'fas fa-check-circle mr-3';
    } else if (tipo === 'error') {
        toast.classList.add('bg-red-500', 'text-white');
        toastIcon.className = 'fas fa-exclamation-circle mr-3';
    } else {
        toast.classList.add('bg-blue-500', 'text-white');
        toastIcon.className = 'fas fa-info-circle mr-3';
    }
    
    toastTitle.textContent = titulo;
    toastMessage.textContent = mensaje;
    
    // Mostrar toast
    toast.classList.remove('hidden', 'translate-x-full');
    
    // Ocultar después de 5 segundos
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 300);
    }, 5000);
}

async function generarYEnviarLink() {
    const email = document.getElementById('emailCliente').value;
    const mensajeDiv = document.getElementById('mensajeLink');
    const btnGenerar = document.getElementById('btnGenerarLink');
    
    if (!email) {
        mensajeDiv.className = 'mt-4 p-3 rounded-lg bg-red-100 text-red-700 border border-red-400';
        mensajeDiv.textContent = 'El email del cliente es requerido';
        mensajeDiv.classList.remove('hidden');
        mostrarToast('error', 'Error', 'El email del cliente es requerido');
        return;
    }

    // Mostrar estado de carga
    mensajeDiv.className = 'mt-4 p-3 rounded-lg bg-blue-100 text-blue-700 border border-blue-400';
    mensajeDiv.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generando link de pago...';
    mensajeDiv.classList.remove('hidden');
    btnGenerar.disabled = true;
    btnGenerar.classList.add('opacity-50', 'cursor-not-allowed');

    try {
        const response = await fetch(`/api/ordenes-trabajo/${ordenIdActual}/generar-link-pago`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                email_cliente: email,
            }),
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Error al generar el link de pago');
        }

        // Mostrar el link generado
        mensajeDiv.className = 'mt-4 p-3 rounded-lg bg-green-100 text-green-700 border border-green-400';
        mensajeDiv.innerHTML = `
            <p class="font-semibold mb-2">✓ Link de pago generado exitosamente</p>
            <a href="${result.payment_link_url}" target="_blank" class="text-blue-600 hover:text-blue-800 underline break-all text-sm">
                ${result.payment_link_url}
            </a>
            <p class="text-xs mt-2">Copia y envía este link al cliente</p>
        `;
        
        // Mostrar toast de éxito
        mostrarToast('success', 'Éxito', 'Link de pago generado correctamente');

    } catch (error) {
        mensajeDiv.className = 'mt-4 p-3 rounded-lg bg-red-100 text-red-700 border border-red-400';
        mensajeDiv.textContent = '✗ ' + error.message;
        mostrarToast('error', 'Error', error.message);
    } finally {
        // Restaurar botón
        btnGenerar.disabled = false;
        btnGenerar.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('modalPago');
    if (event.target === modal) {
        cerrarModalPago();
    }
}
</script>
@endpush
</x-app-layout>
