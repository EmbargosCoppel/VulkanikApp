<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold" style="color: var(--color-secondary);">
            Procesar Pago
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Orden #{{ $ordenTrabajo->id }}</h3>
            
            <div class="space-y-3 mb-6">
                <div class="flex justify-between">
                    <span class="text-gray-600">Cliente:</span>
                    <span class="font-semibold">{{ $ordenTrabajo->vehiculo->cliente->nombre }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Vehículo:</span>
                    <span class="font-semibold">{{ $ordenTrabajo->vehiculo->marca }} {{ $ordenTrabajo->vehiculo->modelo }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Placa:</span>
                    <span class="font-semibold">{{ $ordenTrabajo->vehiculo->placa }}</span>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h4 class="text-lg font-bold text-gray-800 mb-3">Resumen de Costos</h4>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span>Subtotal Refacciones:</span>
                        <span>${{ number_format($totales['subtotal_refacciones'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Mano de Obra:</span>
                        <span>${{ number_format($totales['mano_obra'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Subtotal:</span>
                        <span>${{ number_format($totales['subtotal'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>IVA (16%):</span>
                        <span>${{ number_format($totales['iva'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-2 mt-2">
                        <span>Total:</span>
                        <span style="color: var(--color-primary);">${{ number_format($totales['total'], 2) }} MXN</span>
                    </div>
                </div>
            </div>

            @if(config('services.stripe.key'))
            <form id="payment-form" class="space-y-4">
                @csrf
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-blue-900 mb-2">💳 Información de Pago</h4>
                    <p class="text-sm text-blue-700">Pago seguro procesado por Stripe (modo prueba)</p>
                    <p class="text-xs text-blue-600 mt-1">Tarjeta de prueba: 4242 4242 4242 4242 · Fecha futura · CVC cualquiera</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Datos de la Tarjeta</label>
                    <div id="card-element" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white">
                        <!-- Stripe Card Element se insertará aquí -->
                    </div>
                    <div id="card-errors" class="text-red-600 text-sm mt-2" role="alert"></div>
                </div>

                <button type="submit" id="submit-button" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                    <span id="button-text">Pagar ${{ number_format($totales['total'], 2) }} MXN</span>
                    <span id="button-spinner" class="hidden">Procesando...</span>
                </button>
            </form>
            @else
            <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 p-4 rounded-lg">
                <p class="font-semibold">Stripe no está configurado</p>
                <p class="text-sm mt-1">Agrega STRIPE_KEY y STRIPE_SECRET en tu archivo .env</p>
            </div>
            @endif

            <div class="mt-6 text-center">
                <a href="{{ route('ordenes.show', $ordenTrabajo) }}" class="text-blue-600 hover:text-blue-800">
                    ← Volver a la orden
                </a>
            </div>
        </div>
    </div>

    @if(config('services.stripe.key'))
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe(@json(config('services.stripe.key')));
        const elements = stripe.elements();

        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#424770',
                    '::placeholder': {
                        color: '#aab7c4',
                    },
                },
                invalid: {
                    color: '#9e2146',
                },
            },
        });

        cardElement.mount('#card-element');

        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            displayError.textContent = event.error ? event.error.message : '';
        });

        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            const submitButton = document.getElementById('submit-button');
            const buttonText = document.getElementById('button-text');
            const buttonSpinner = document.getElementById('button-spinner');
            const errorBox = document.getElementById('card-errors');

            submitButton.disabled = true;
            buttonText.classList.add('hidden');
            buttonSpinner.classList.remove('hidden');
            errorBox.textContent = '';

            try {
                // 1. Crear PaymentMethod con los datos de la tarjeta
                const { paymentMethod, error } = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                });

                if (error) {
                    errorBox.textContent = error.message;
                    submitButton.disabled = false;
                    buttonText.classList.remove('hidden');
                    buttonSpinner.classList.add('hidden');
                    return;
                }

                // 2. Enviar al backend para confirmar el pago
                const response = await fetch(@json(route('ordenes.procesarPago', $ordenTrabajo)), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token()),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        payment_method_id: paymentMethod.id,
                    }),
                });

                const result = await response.json();

                if (!response.ok || result.error || result.exitoso === false) {
                    errorBox.textContent = result.error || result.message || 'Error al procesar el pago';
                    submitButton.disabled = false;
                    buttonText.classList.remove('hidden');
                    buttonSpinner.classList.add('hidden');
                    return;
                }

                // 3. Redirigir al ticket
                window.location.href = result.redirect || @json(route('ordenes.ticket', $ordenTrabajo));
            } catch (err) {
                errorBox.textContent = 'Error: ' + err.message;
                submitButton.disabled = false;
                buttonText.classList.remove('hidden');
                buttonSpinner.classList.add('hidden');
            }
        });
    </script>
    @endif
</x-app-layout>
