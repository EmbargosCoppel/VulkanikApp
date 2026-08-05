<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div style="display: flex; min-height: 100vh;">
            <!-- Sidebar -->
            <div id="sidebar" style="width: 250px; background: #1a73e8; color: white; padding: 20px; position: fixed; height: 100vh; overflow-y: auto;">
                <h2 style="margin-bottom: 30px; font-size: 24px;">Vulcanizadora</h2>
                <nav style="list-style: none; padding: 0;">
                    <a href="{{ route('dashboard') }}" style="display: block; padding: 12px; color: white; text-decoration: none; margin-bottom: 5px; border-radius: 5px; background: rgba(255,255,255,0.1);">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="{{ route('clientes.index') }}" style="display: block; padding: 12px; color: white; text-decoration: none; margin-bottom: 5px; border-radius: 5px;">
                        <i class="fas fa-users"></i> Clientes
                    </a>
                    <a href="{{ route('vehiculos.index') }}" style="display: block; padding: 12px; color: white; text-decoration: none; margin-bottom: 5px; border-radius: 5px;">
                        <i class="fas fa-car"></i> Vehículos
                    </a>
                    <a href="{{ route('ordenes.index') }}" style="display: block; padding: 12px; color: white; text-decoration: none; margin-bottom: 5px; border-radius: 5px;">
                        <i class="fas fa-clipboard-list"></i> Órdenes
                    </a>
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('refacciones.index') }}" style="display: block; padding: 12px; color: white; text-decoration: none; margin-bottom: 5px; border-radius: 5px;">
                        <i class="fas fa-cogs"></i> Refacciones
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Main Content -->
            <div style="margin-left: 250px; flex: 1; padding: 20px;">
                <header style="background: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span style="font-weight: 600;">{{ Auth::user()->name }}</span>
                            <span style="color: #666; margin-left: 10px;">({{ Auth::user()->role === 'admin' ? 'Administrador' : 'Mecánico' }})</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="background: #dc2626; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer;">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </header>

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>