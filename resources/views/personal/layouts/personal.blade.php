{{-- resources/views/layouts/personal.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Personal - Clínica Santa Lucía')</title>

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
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }

        .user-badge {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
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
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-200 transition-all">
                        <span class="sr-only">Abrir sidebar</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                        </svg>
                    </button>

                    <!-- Logo -->
                    <a href="{{ route('personal.home') }}" class="flex ms-2 md:me-24 items-center group">
                        <img src="{{ asset('logo.jpg') }}" alt="Clínica Santa Lucía" class="w-12 h-12 rounded-xl object-cover logo-shadow">
                        <div class="ms-3">
                            <span class="block text-xl font-bold text-gray-800 group-hover:text-emerald-600 transition-colors">Clínica Santa Lucía</span>
                            <span class="block text-xs text-gray-500 font-medium">Sistema de Gestión</span>
                        </div>
                    </a>
                </div>

                <!-- Usuario y logout -->
                <div class="flex items-center">
                    <div class="flex items-center ms-3 gap-3">
                        <div class="flex items-center gap-3 px-4 py-2 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg border border-emerald-200">
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-semibold text-gray-800">{{ Auth::user()->nomPer }} {{ Auth::user()->paternoPer }}</span>
                                <span class="text-xs text-emerald-700 font-medium">Personal Clínico</span>
                            </div>
                            <div class="w-10 h-10 user-badge rounded-full flex items-center justify-center font-bold text-white text-sm shadow-lg">
                                {{ strtoupper(substr(Auth::user()->nomPer, 0, 1)) }}{{ strtoupper(substr(Auth::user()->paternoPer, 0, 1)) }}
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 px-4 py-2 text-sm text-white bg-gradient-to-r from-rose-500 to-red-600 rounded-lg hover:from-rose-600 hover:to-red-700 transition-all transform hover:scale-105 shadow-md hover:shadow-lg">
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
    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 shadow-xl" aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-gradient-to-b from-white to-gray-50">
            <!-- Header del Sidebar -->
            <div class="mb-6 p-4 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg text-white shadow-lg">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-icons text-2xl">menu_book</span>
                    <h3 class="font-bold text-lg">Menú Principal</h3>
                </div>
                <p class="text-xs text-emerald-100">Gestión hospitalaria</p>
            </div>

            <ul class="space-y-2 font-medium">
                <!-- Inicio -->
                <li>
                    <a href="{{ route('personal.home') }}" class="sidebar-item flex items-center p-3 rounded-xl hover:bg-emerald-50 group {{ request()->routeIs('personal.home') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg' : 'text-gray-700 hover:text-emerald-600' }}">
                        <span class="material-icons {{ request()->routeIs('personal.home') ? 'text-white' : 'text-emerald-600' }}">dashboard</span>
                        <span class="ms-3 font-semibold">Inicio</span>
                        @if(request()->routeIs('personal.home'))
                        <span class="material-icons ms-auto text-sm">chevron_right</span>
                        @endif
                    </a>
                </li>

                <!-- Cronogramas -->
                <li>
                    <a href="{{ route('personal.cronogramas.cronogramas') }}" class="sidebar-item flex items-center p-3 rounded-xl hover:bg-emerald-50 group {{ request()->routeIs('personal.cronogramas.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg' : 'text-gray-700 hover:text-emerald-600' }}">
                        <span class="material-icons {{ request()->routeIs('personal.cronogramas.*') ? 'text-white' : 'text-emerald-600' }}">calendar_today</span>
                        <span class="ms-3 font-semibold">Cronogramas</span>
                        @if(request()->routeIs('personal.cronogramas.*'))
                        <span class="material-icons ms-auto text-sm">chevron_right</span>
                        @endif
                    </a>
                </li>

                <!-- Médicos -->
                <li>
                    <a href="{{ route('personal.medicos.medicos') }}" class="sidebar-item flex items-center p-3 rounded-xl hover:bg-emerald-50 group {{ request()->routeIs('personal.medicos.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg' : 'text-gray-700 hover:text-emerald-600' }}">
                        <span class="material-icons {{ request()->routeIs('personal.medicos.*') ? 'text-white' : 'text-emerald-600' }}">medical_services</span>
                        <span class="ms-3 font-semibold">Médicos</span>
                        @if(request()->routeIs('personal.medicos.*'))
                        <span class="material-icons ms-auto text-sm">chevron_right</span>
                        @endif
                    </a>
                </li>

                <!-- Pacientes -->
                <li>
                    <a href="{{ route('personal.pacientes.pacientes') }}" class="sidebar-item flex items-center p-3 rounded-xl hover:bg-emerald-50 group {{ request()->routeIs('personal.pacientes.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg' : 'text-gray-700 hover:text-emerald-600' }}">
                        <span class="material-icons {{ request()->routeIs('personal.pacientes.*') ? 'text-white' : 'text-emerald-600' }}">people</span>
                        <span class="ms-3 font-semibold">Pacientes</span>
                        @if(request()->routeIs('personal.pacientes.*'))
                        <span class="material-icons ms-auto text-sm">chevron_right</span>
                        @endif
                    </a>
                </li>

                <!-- Servicios -->
                <li>
                    <button type="button"
                        class="sidebar-item flex items-center w-full p-3 rounded-xl hover:bg-emerald-50 group transition-colors {{ request()->routeIs('personal.servicios.*') || request()->routeIs('personal.tipos-estudio.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg' : 'text-gray-700 hover:text-emerald-600' }}"
                        onclick="toggleSubmenu('servicios-submenu')">
                        <span class="material-icons {{ request()->routeIs('personal.servicios.*') || request()->routeIs('personal.tipos-estudio.*') ? 'text-white' : 'text-emerald-600' }}">miscellaneous_services</span>
                        <span class="ms-3 font-semibold flex-1 text-left">Servicios</span>
                        <span class="material-icons text-sm transition-transform" id="servicios-submenu-icon">expand_more</span>
                    </button>

                    <!-- Submenú de Servicios -->
                    <ul class="ml-8 mt-2 space-y-2 {{ request()->routeIs('personal.servicios.*') || request()->routeIs('personal.tipos-estudio.*') ? '' : 'hidden' }}" id="servicios-submenu">
                        <li>
                            <a href="{{ route('personal.servicios.servicios') }}"
                                class="sidebar-item flex items-center p-2 rounded-lg hover:bg-emerald-50 group {{ request()->routeIs('personal.servicios.servicios') ? 'bg-emerald-100 text-emerald-700' : 'text-gray-700 hover:text-emerald-600' }}">
                                <span class="material-icons text-sm {{ request()->routeIs('personal.servicios.servicios') ? 'text-emerald-600' : 'text-gray-500' }}">list_alt</span>
                                <span class="ms-2 text-sm font-medium">Todos los Servicios</span>
                            </a>
                        </li>
                        <li>
                           <a href="{{ route('personal.servicios.atendidos') }}"
                                class="sidebar-item flex items-center p-2 rounded-lg hover:bg-emerald-50 group {{ request()->routeIs('personal.servicios.atendidos') ? 'bg-emerald-100 text-emerald-700' : 'text-gray-700 hover:text-emerald-600' }}">
                                <span class="material-icons text-sm {{ request()->routeIs('personal.servicios.atendidos') ? 'text-emerald-600' : 'text-gray-500' }}">check_circle</span>
                                <span class="ms-2 text-sm font-medium">Servicios Atendidos</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('personal.tipos-estudio.index') }}"
                                class="sidebar-item flex items-center p-2 rounded-lg hover:bg-emerald-50 group {{ request()->routeIs('personal.tipos-estudio.*') ? 'bg-emerald-100 text-emerald-700' : 'text-gray-700 hover:text-emerald-600' }}">
                                <span class="material-icons text-sm {{ request()->routeIs('personal.tipos-estudio.*') ? 'text-emerald-600' : 'text-gray-500' }}">category</span>
                                <span class="ms-2 text-sm font-medium">Tipos de Estudio</span>
                            </a>
                        </li>
                    </ul>
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
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200">
                    @foreach($breadcrumbs as $breadcrumb)
                        <li class="inline-flex items-center">
                            @if(!$loop->first)
                                <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                            @endif
                            @if(isset($breadcrumb['url']))
                                <a href="{{ $breadcrumb['url'] }}" class="inline-flex items-center text-sm font-semibold text-gray-700 hover:text-emerald-600 transition-colors">
                                    {{ $breadcrumb['label'] }}
                                </a>
                            @else
                                <span class="text-sm font-semibold text-emerald-600">{{ $breadcrumb['label'] }}</span>
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

    <script>
        // Toggle submenú
        function toggleSubmenu(id) {
            const submenu = document.getElementById(id);
            const icon = document.getElementById(id + '-icon');

            if (submenu.classList.contains('hidden')) {
                submenu.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                submenu.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
