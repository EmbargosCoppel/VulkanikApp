<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            @media (max-width: 768px) {
                #sidebar { width: 0 !important; padding: 0 !important; overflow: hidden; }
                #sidebar.expanded { width: 250px !important; padding: 20px !important; }
                #main-content { margin-left: 0 !important; }
                #mobile-menu-btn { display: block !important; }
            }
            @media (min-width: 769px) { #mobile-menu-btn { display: none !important; } }
        </style>
    </head>
    <body>
        <button id="mobile-menu-btn" onclick="toggleMobileSidebar()" style="position: fixed; top: 15px; left: 15px; z-index: 1001; background: #1a73e8; color: white; border: none; border-radius: 5px; padding: 10px 15px; cursor: pointer; font-size: 20px; display: none;">
            <i class="fas fa-bars"></i>
        </button>
        <div id="overlay" onclick="closeMobileSidebar()" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999;"></div>
        <div style="display: flex; min-height: 100vh;">
            <!-- Sidebar -->
            <div id="sidebar" onmouseover="showSidebar()" onmouseout="hideSidebar()" style="width: 60px; background: #1a73e8; color: white; padding: 20px 10px; position: fixed; height: 100vh; overflow-y: auto; transition: width 0.3s ease;">
                <h2 id="sidebar-title" style="margin-bottom: 30px; font-size: 20px; white-space: nowrap; overflow: hidden; opacity: 0; transition: opacity 0.3s ease;">Vulcanizadora</h2>
                <nav style="list-style: none; padding: 0;">
                    <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; padding: 12px; color: white; text-decoration: none; margin-bottom: 5px; border-radius: 5px; background: rgba(255,255,255,0.1); white-space: nowrap; overflow: hidden;">
                        <i class="fas fa-home" style="font-size: 20px; min-width: 40px; text-align: center;"></i>
                        <span id="nav-dashboard" style="margin-left: 10px; opacity: 0; transition: opacity 0.3s ease;">Dashboard</span>
                    </a>
                    <a href="{{ route('clientes.index') }}" style="display: flex; align-items: center; padding: 12px; color: white; text-decoration: none; margin-bottom: 5px; border-radius: 5px; white-space: nowrap; overflow: hidden;">
                        <i class="fas fa-users" style="font-size: 20px; min-width: 40px; text-align: center;"></i>
                        <span id="nav-clientes" style="margin-left: 10px; opacity: 0; transition: opacity 0.3s ease;">Clientes</span>
                    </a>
                    <a href="{{ route('vehiculos.index') }}" style="display: flex; align-items: center; padding: 12px; color: white; text-decoration: none; margin-bottom: 5px; border-radius: 5px; white-space: nowrap; overflow: hidden;">
                        <i class="fas fa-car" style="font-size: 20px; min-width: 40px; text-align: center;"></i>
                        <span id="nav-vehiculos" style="margin-left: 10px; opacity: 0; transition: opacity 0.3s ease;">Vehículos</span>
                    </a>
                    <a href="{{ route('ordenes.index') }}" style="display: flex; align-items: center; padding: 12px; color: white; text-decoration: none; margin-bottom: 5px; border-radius: 5px; white-space: nowrap; overflow: hidden;">
                        <i class="fas fa-clipboard-list" style="font-size: 20px; min-width: 40px; text-align: center;"></i>
                        <span id="nav-ordenes" style="margin-left: 10px; opacity: 0; transition: opacity 0.3s ease;">Órdenes</span>
                    </a>
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.cobros') }}" style="display: flex; align-items: center; padding: 12px; color: white; text-decoration: none; margin-bottom: 5px; border-radius: 5px; white-space: nowrap; overflow: hidden;">
                        <i class="fas fa-money-bill-wave" style="font-size: 20px; min-width: 40px; text-align: center;"></i>
                        <span id="nav-cobros" style="margin-left: 10px; opacity: 0; transition: opacity 0.3s ease;">Cobros</span>
                    </a>
                    <a href="{{ route('mecanicos.index') }}" style="display: flex; align-items: center; padding: 12px; color: white; text-decoration: none; margin-bottom: 5px; border-radius: 5px; white-space: nowrap; overflow: hidden;">
                        <i class="fas fa-user-cog" style="font-size: 20px; min-width: 40px; text-align: center;"></i>
                        <span id="nav-mecanicos" style="margin-left: 10px; opacity: 0; transition: opacity 0.3s ease;">Mecánicos</span>
                    </a>
                    <a href="{{ route('refacciones.index') }}" style="display: flex; align-items: center; padding: 12px; color: white; text-decoration: none; margin-bottom: 5px; border-radius: 5px; white-space: nowrap; overflow: hidden;">
                        <i class="fas fa-cogs" style="font-size: 20px; min-width: 40px; text-align: center;"></i>
                        <span id="nav-refacciones" style="margin-left: 10px; opacity: 0; transition: opacity 0.3s ease;">Refacciones</span>
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Main Content -->
            <div id="main-content" style="margin-left: 60px; flex: 1; padding: 20px; transition: margin-left 0.3s ease;">
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

        <script src="https://js.stripe.com/v3/"></script>
        <script>
            @if(config('services.stripe.key'))
            const stripe = Stripe('{{ config('services.stripe.key') }}');
            @endif
            
            function showSidebar() {
                if (window.innerWidth > 768) {
                    document.getElementById('sidebar').style.width = '250px';
                    document.getElementById('sidebar').style.padding = '20px';
                    document.getElementById('main-content').style.marginLeft = '250px';
                    document.getElementById('sidebar-title').style.opacity = '1';
                    document.getElementById('nav-dashboard').style.opacity = '1';
                    document.getElementById('nav-clientes').style.opacity = '1';
                    document.getElementById('nav-vehiculos').style.opacity = '1';
                    document.getElementById('nav-ordenes').style.opacity = '1';
                    var m = document.getElementById('nav-mecanicos');
                    if (m) m.style.opacity = '1';
                    var r = document.getElementById('nav-refacciones');
                    if (r) r.style.opacity = '1';
                    var c = document.getElementById('nav-cobros');
                    if (c) c.style.opacity = '1';
                }
            }
            function hideSidebar() {
                if (window.innerWidth > 768) {
                    document.getElementById('sidebar').style.width = '60px';
                    document.getElementById('sidebar').style.padding = '20px 10px';
                    document.getElementById('main-content').style.marginLeft = '60px';
                    document.getElementById('sidebar-title').style.opacity = '0';
                    document.getElementById('nav-dashboard').style.opacity = '0';
                    document.getElementById('nav-clientes').style.opacity = '0';
                    document.getElementById('nav-vehiculos').style.opacity = '0';
                    document.getElementById('nav-ordenes').style.opacity = '0';
                    var m = document.getElementById('nav-mecanicos');
                    if (m) m.style.opacity = '0';
                    var r = document.getElementById('nav-refacciones');
                    if (r) r.style.opacity = '0';
                    var c = document.getElementById('nav-cobros');
                    if (c) c.style.opacity = '0';
                }
            }
            function toggleMobileSidebar() {
                var s = document.getElementById('sidebar');
                var o = document.getElementById('overlay');
                if (s.classList.contains('expanded')) { closeMobileSidebar(); }
                else {
                    s.classList.add('expanded');
                    s.style.width = '250px';
                    s.style.padding = '20px';
                    o.style.display = 'block';
                    document.getElementById('sidebar-title').style.opacity = '1';
                    document.getElementById('nav-dashboard').style.opacity = '1';
                    document.getElementById('nav-clientes').style.opacity = '1';
                    document.getElementById('nav-vehiculos').style.opacity = '1';
                    document.getElementById('nav-ordenes').style.opacity = '1';
                    var m = document.getElementById('nav-mecanicos');
                    if (m) m.style.opacity = '1';
                    var r = document.getElementById('nav-refacciones');
                    if (r) r.style.opacity = '1';
                }
            }
            function closeMobileSidebar() {
                var s = document.getElementById('sidebar');
                var o = document.getElementById('overlay');
                s.classList.remove('expanded');
                s.style.width = '0';
                s.style.padding = '0';
                o.style.display = 'none';
            }
        </script>
    </body>
</html>
