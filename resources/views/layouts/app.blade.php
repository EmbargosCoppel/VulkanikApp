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
        <div style="display: flex; min-height: 100vh;">
            <!-- Sidebar -->
            <aside class="sidebar" style="width: 256px; flex-shrink: 0; background-color: var(--color-bg-primary); border-right: 1px solid var(--color-secondary-lighter); box-shadow: var(--shadow-sm);">
                <div style="display: flex; flex-direction: column; height: 100vh; position: sticky; top: 0;">
                    <!-- Logo -->
                    <div style="display: flex; align-items: center; justify-content: center; height: 64px; padding: 0 16px; margin-bottom: 16px;">
                        <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background-color: var(--color-primary);">
                                <svg style="width: 24px; height: 24px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <span style="font-size: 20px; font-weight: 700; color: var(--color-secondary);">Vulcanizadora</span>
                        </a>
                    </div>

                    <!-- Navigation -->
                    <nav style="flex: 1; padding: 0 8px; overflow-y: auto;">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; transition: all 300ms; color: var(--color-secondary); text-decoration: none;">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; transition: all 300ms; color: var(--color-secondary); text-decoration: none;">
                            <i class="fas fa-users"></i>
                            <span>Clientes</span>
                        </a>
                        <a href="{{ route('vehiculos.index') }}" class="nav-link {{ request()->routeIs('vehiculos.*') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; transition: all 300ms; color: var(--color-secondary); text-decoration: none;">
                            <i class="fas fa-car"></i>
                            <span>Vehículos</span>
                        </a>
                        <a href="{{ route('ordenes.index') }}" class="nav-link {{ request()->routeIs('ordenes.*') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; transition: all 300ms; color: var(--color-secondary); text-decoration: none;">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Órdenes</span>
                        </a>
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('refacciones.index') }}" class="nav-link {{ request()->routeIs('refacciones.*') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; transition: all 300ms; color: var(--color-secondary); text-decoration: none;">
                            <i class="fas fa-cogs"></i>
                            <span>Refacciones</span>
                        </a>
                        @endif
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <div style="flex: 1; display: flex; flex-direction: column; min-width: 0;">
                <!-- Top Navigation (Mobile) -->
                <div class="md:hidden">
                    @include('layouts.navigation')
                </div>

                <!-- Desktop Header -->
                <header class="header hidden md:flex" style="display: none; align-items: center; justify-content: space-between; height: 64px; background-color: var(--color-bg-primary); border-bottom: 1px solid var(--color-secondary-lighter); box-shadow: var(--shadow-sm);">
                    <div style="flex: 1;"></div>
                    <div style="display: flex; align-items: center; gap: 16px; padding: 0 24px;">
                        <div class="dropdown">
                            <button style="display: flex; align-items: center; gap: 12px; padding: 8px 16px; border-radius: 8px; transition: all 300ms; cursor: pointer; background: none; border: none;">
                                <div style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; font-weight: 600;">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div style="text-align: left;">
                                    <div style="font-size: 14px; font-weight: 500; color: #374151;">{{ Auth::user()->name }}</div>
                                    <div style="font-size: 12px; color: #6b7280;">{{ Auth::user()->role === 'admin' ? 'Administrador' : 'Mecánico' }}</div>
                                </div>
                                <svg style="width: 16px; height: 16px; color: #9ca3af;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div class="dropdown-menu" style="position: absolute; right: 0; margin-top: 8px; width: 192px; background: white; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; padding: 8px 0; z-index: 50;">
                                <div style="padding: 12px 16px; border-bottom: 1px solid #e5e7eb;">
                                    <p style="font-size: 12px; color: #6b7280;">Conectado como</p>
                                    <p style="font-size: 14px; font-weight: 500; color: #111827;">{{ Auth::user()->email }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" style="margin-top: 8px;">
                                    @csrf
                                    <button type="submit" style="width: 100%; text-align: left; padding: 8px 16px; color: #dc2626; background: none; border: none; cursor: pointer; transition: background 300ms;">
                                        <i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main style="flex: 1; padding: 24px;">
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