se usen desplegables<!DOCTYPE html>
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
        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <aside class="sidebar hidden md:block md:w-64 md:flex-shrink-0">
                <div class="flex flex-col h-screen sticky top-0">
                    <!-- Logo -->
                    <div class="flex items-center justify-center h-16 px-4 mb-4">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: var(--color-primary);">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <span class="text-xl font-bold" style="color: var(--color-secondary);">Vulcanizadora</span>
                        </a>
                    </div>

                    <!-- Navigation -->
                    <nav class="flex-1 px-2 space-y-1 overflow-y-auto">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>Clientes</span>
                        </a>
                        <a href="{{ route('vehiculos.index') }}" class="nav-link {{ request()->routeIs('vehiculos.*') ? 'active' : '' }}">
                            <i class="fas fa-car"></i>
                            <span>Vehículos</span>
                        </a>
                        <a href="{{ route('ordenes.index') }}" class="nav-link {{ request()->routeIs('ordenes.*') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Órdenes</span>
                        </a>
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('refacciones.index') }}" class="nav-link {{ request()->routeIs('refacciones.*') ? 'active' : '' }}">
                            <i class="fas fa-cogs"></i>
                            <span>Refacciones</span>
                        </a>
                        @endif
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex flex-col flex-1 min-w-0">
                <!-- Top Navigation (Mobile) -->
                <div class="md:hidden">
                    @include('layouts.navigation')
                </div>

                <!-- Desktop Header -->
                <header class="header hidden md:flex md:items-center md:justify-between md:h-16">
                    <div class="flex-1"></div>
                    <div class="flex items-center gap-4 px-6">
                        <div class="dropdown">
                            <button class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition-all duration-300">
                                <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div class="text-left">
                                    <div class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Auth::user()->role === 'admin' ? 'Administrador' : 'Mecánico' }}</div>
                                </div>
                                <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div class="dropdown-menu">
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <p class="text-sm text-gray-500">Conectado como</p>
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->email }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                    @csrf
                                    <button type="submit" class="dropdown-item w-full text-left text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-6 lg:p-8">
                    @if(isset($header))
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Confirmation Modal Script -->
        <script>
            function confirmDelete(button, message) {
                if (confirm(message)) {
                    button.closest('form').submit();
                }
            }
        </script>
    </body>
</html>
