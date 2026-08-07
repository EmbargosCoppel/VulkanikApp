<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-center" style="color: var(--color-secondary);">
            Iniciar Sesión
        </h2>
        <p class="text-sm text-center mt-2" style="color: var(--color-secondary-light);">
            Ingresa tus credenciales para acceder
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <label class="form-label" for="email">
                <i class="fas fa-envelope mr-2" style="color: var(--color-primary);"></i>
                Correo Electrónico
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                   class="form-input" placeholder="nombre@correo.com">
            <x-input-error :messages="$errors->get('email')" class="form-error" />
        </div>

        <!-- Password -->
        <div class="form-group">
            <label class="form-label" for="password">
                <i class="fas fa-lock mr-2" style="color: var(--color-primary);"></i>
                Contraseña
            </label>
            <input id="password" type="password" name="password" required autocomplete="current-password" 
                   class="form-input" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="form-error" />
        </div>

        <!-- Remember Me -->
        <div class="form-group">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="remember" class="rounded border-gray-300" style="accent-color: var(--color-primary);">
                <span class="ms-2 text-sm" style="color: var(--color-secondary);">
                    Recordarme
                </span>
            </label>
        </div>

        <div class="flex flex-col gap-3">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" 
                   class="text-sm text-center" 
                   style="color: var(--color-primary);">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif

            <button type="submit" class="btn btn-primary w-full">
                <i class="fas fa-sign-in-alt"></i>
                Iniciar Sesión
            </button>
        </div>
    </form>
</x-guest-layout>
