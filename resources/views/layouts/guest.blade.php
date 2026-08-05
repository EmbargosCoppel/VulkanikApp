<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" style="background-color: var(--color-bg-secondary);">
        <div class="min-h-screen flex items-center justify-center">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <a href="/" class="inline-flex items-center gap-3">
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center" style="background-color: var(--color-primary);">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <span class="text-3xl font-bold" style="color: var(--color-secondary);">Vulcanizadora</span>
                    </a>
                    <p class="mt-2 text-sm" style="color: var(--color-secondary-light);">Sistema de Gestión de Taller</p>
                </div>

                <!-- Login Card -->
                <div class="card">
                    {{ $slot }}
                </div>

                <!-- Footer -->
                <div class="text-center mt-6">
                    <p class="text-sm" style="color: var(--color-secondary-light);">
                        © 2026 Vulcanizadora Don Chuy. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
