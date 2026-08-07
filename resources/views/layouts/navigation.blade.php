<nav x-data="{ open: false }" class="header">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <div class="w-10 h-10 rounded-lg overflow-hidden shadow-md group-hover:shadow-lg transition-all duration-300">
                            <x-application-logo class="w-full h-full" />
                        </div>
                        <span class="text-xl font-bold text-gray-800 hidden sm:block">{{ config('app.name', 'Vulcanik') }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:ms-10 space-x-2">
                    <a href="{{ route('dashboard') }}" 
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('clientes.index') }}" 
                       class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Clientes</span>
                    </a>
                    <a href="{{ route('vehiculos.index') }}" 
                       class="nav-link {{ request()->routeIs('vehiculos.*') ? 'active' : '' }}">
                        <i class="fas fa-car"></i>
                        <span>Vehículos</span>
                    </a>
                    <a href="{{ route('ordenes.index') }}" 
                       class="nav-link {{ request()->routeIs('ordenes.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Órdenes</span>
                    </a>
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('dashboard') }}#cobros" 
                       class="nav-link {{ request()->is('/*#cobros') ? 'active' : '' }}">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Cobros</span>
                    </a>
                    <a href="{{ route('mecanicos.index') }}" 
                       class="nav-link {{ request()->routeIs('mecanicos.*') ? 'active' : '' }}">
                        <i class="fas fa-user-cog"></i>
                        <span>Mecánicos</span>
                    </a>
                    <a href="{{ route('refacciones.index') }}" 
                       class="nav-link {{ request()->routeIs('refacciones.*') ? 'active' : '' }}">
                        <i class="fas fa-cogs"></i>
                        <span>Refacciones</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
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

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" 
               class="responsive-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
            <a href="{{ route('clientes.index') }}" 
               class="responsive-nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                Clientes
            </a>
            <a href="{{ route('vehiculos.index') }}" 
               class="responsive-nav-link {{ request()->routeIs('vehiculos.*') ? 'active' : '' }}">
                <i class="fas fa-car"></i>
                Vehículos
            </a>
            <a href="{{ route('ordenes.index') }}" 
               class="responsive-nav-link {{ request()->routeIs('ordenes.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list"></i>
                Órdenes
            </a>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('dashboard') }}#cobros" 
               class="responsive-nav-link">
                <i class="fas fa-money-bill-wave"></i>
                Cobros
            </a>
            <a href="{{ route('mecanicos.index') }}" 
               class="responsive-nav-link {{ request()->routeIs('mecanicos.*') ? 'active' : '' }}">
                <i class="fas fa-user-cog"></i>
                Mecánicos
            </a>
            <a href="{{ route('refacciones.index') }}" 
               class="responsive-nav-link {{ request()->routeIs('refacciones.*') ? 'active' : '' }}">
                <i class="fas fa-cogs"></i>
                Refacciones
            </a>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div
