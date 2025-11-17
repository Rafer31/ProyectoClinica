{{-- resources/views/layouts/supervisor.blade.php --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Supervisor - Sistema Clínica')</title>

    <!-- Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Flowbite CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />

    <!-- Vite: Tailwind CSS + JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .sidebar-item {
            transition: all 0.2s ease;
        }

        .sidebar-item:hover {
            transform: translateX(3px);
        }

        .logo-shadow {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        }

        .user-badge {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100">

    <!-- Navbar -->
    <nav class="fixed top-0 z-50 w-full bg-white navbar-shadow">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <!-- Botón toggle sidebar -->
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar"
                        aria-controls="logo-sidebar" type="button"
                        class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all">
                        <span class="sr-only">Abrir sidebar</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                            </path>
                        </svg>
                    </button>

                    <!-- Logo -->
                    <a href="{{ route('supervisor.home') }}" class="flex ms-2 md:me-24 items-center group">
                        <div
                            class="w-12 h-12 rounded-xl flex items-center justify-center logo-shadow">
                            <img src="{{ asset('logo_azul.jpg') }}" alt="Logo" class="w-10 h-10 object-contain">

                        </div>
                        <div class="ms-3">
                            <span
                                class="block text-xl font-bold text-gray-800 group-hover:text-blue-600 transition-colors">Sistema
                                Clínica</span>
                            <span class="block text-xs text-gray-500 font-medium">Panel de Supervisión</span>
                        </div>
                    </a>

                </div>

                <!-- Usuario y logout -->
                <div class="flex items-center">
                    <div class="flex items-center ms-3 gap-3">
                        <div
                            class="flex items-center gap-3 px-4 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-semibold text-gray-800">{{ Auth::user()->nomPer }}
                                    {{ Auth::user()->paternoPer }}</span>
                                <span class="text-xs text-blue-700 font-medium">Supervisor</span>
                            </div>
                            <div
                                class="w-10 h-10 user-badge rounded-full flex items-center justify-center font-bold text-white text-sm shadow-lg">
                                {{ strtoupper(substr(Auth::user()->nomPer, 0, 1)) }}{{ strtoupper(substr(Auth::user()->paternoPer, 0, 1)) }}
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-white bg-gradient-to-r from-rose-500 to-red-600 rounded-lg hover:from-rose-600 hover:to-red-700 transition-all transform hover:scale-105 shadow-md hover:shadow-lg">
                                <span class="material-icons text-lg">logout</span>
                                <span class="font-medium">Salir</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside id="logo-sidebar"
        class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 shadow-xl"
        aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-gradient-to-b from-white to-gray-50">
            <!-- Header del Sidebar -->
            <div class="mb-6 p-4 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg text-white shadow-lg">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-icons text-2xl">admin_panel_settings</span>
                    <h3 class="font-bold text-lg">Panel Supervisor</h3>
                </div>
                <p class="text-xs text-blue-100">Administración del sistema</p>
            </div>

            <ul class="space-y-2 font-medium">
                <!-- Inicio -->
                <li>
                    <a href="{{ route('supervisor.home') }}"
                        class="sidebar-item flex items-center p-3 rounded-xl hover:bg-blue-50 group {{ request()->routeIs('supervisor.home') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' : 'text-gray-700 hover:text-blue-600' }}">
                        <span
                            class="material-icons {{ request()->routeIs('supervisor.home') ? 'text-white' : 'text-blue-600' }}">dashboard</span>
                        <span class="ms-3 font-semibold">Inicio</span>
                        @if(request()->routeIs('supervisor.home'))
                            <span class="material-icons ms-auto text-sm">chevron_right</span>
                        @endif
                    </a>
                </li>

                <!-- Gestión Personal -->
                <li>
                    <a href="{{ route('supervisor.gestion-personal.gestion-personal') }}"
                        class="sidebar-item flex items-center p-3 rounded-xl hover:bg-blue-50 group {{ request()->routeIs('supervisor.gestion-personal.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' : 'text-gray-700 hover:text-blue-600' }}">
                        <span
                            class="material-icons {{ request()->routeIs('supervisor.gestion-personal.*') ? 'text-white' : 'text-blue-600' }}">badge</span>
                        <span class="ms-3 font-semibold">Gestión Personal</span>
                        @if(request()->routeIs('supervisor.gestion-personal.*'))
                            <span class="material-icons ms-auto text-sm">chevron_right</span>
                        @endif
                    </a>
                </li>

                <!-- Estadísticas -->
                <li>
                    <a href="{{ route('supervisor.estadisticas.estadisticas') }}"
                        class="sidebar-item flex items-center p-3 rounded-xl hover:bg-blue-50 group {{ request()->routeIs('supervisor.estadisticas.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' : 'text-gray-700 hover:text-blue-600' }}">
                        <span
                            class="material-icons {{ request()->routeIs('supervisor.estadisticas.*') ? 'text-white' : 'text-blue-600' }}">bar_chart</span>
                        <span class="ms-3 font-semibold">Estadísticas</span>
                        @if(request()->routeIs('supervisor.estadisticas.*'))
                            <span class="material-icons ms-auto text-sm">chevron_right</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Contenido principal -->
    <div class="p-4 sm:ml-64 mt-14">
        <div class="p-6">
            <!-- Breadcrumb (opcional) -->
            @if(isset($breadcrumbs))
                <nav class="flex mb-6" aria-label="Breadcrumb">
                    <ol
                        class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200">
                        @foreach($breadcrumbs as $breadcrumb)
                            <li class="inline-flex items-center">
                                @if(!$loop->first)
                                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m1 9 4-4-4-4" />
                                    </svg>
                                @endif
                                @if(isset($breadcrumb['url']))
                                    <a href="{{ $breadcrumb['url'] }}"
                                        class="inline-flex items-center text-sm font-semibold text-gray-700 hover:text-blue-600 transition-colors">
                                        {{ $breadcrumb['label'] }}
                                    </a>
                                @else
                                    <span class="text-sm font-semibold text-blue-600">{{ $breadcrumb['label'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            @endif

            <!-- Contenido dinámico -->
            @yield('content')
        </div>
    </div>

    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>

    @stack('scripts')
</body>

</html>