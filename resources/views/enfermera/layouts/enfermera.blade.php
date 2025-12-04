{{-- resources/views/enfermera/layouts/enfermera.blade.php --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Enfermera - Clínica Santa Lucía')</title>

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
            background: linear-gradient(135deg, #9333ea 0%, #a855f7 100%);
        }

        .user-badge {
            background: linear-gradient(135deg, #c084fc 0%, #a855f7 100%);
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gradient-to-br from-purple-50 to-pink-50">

    <!-- Navbar -->
    <nav class="fixed top-0 z-50 w-full bg-white navbar-shadow">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <!-- Botón toggle sidebar -->
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar"
                        aria-controls="logo-sidebar" type="button"
                        class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-200 transition-all">
                        <span class="sr-only">Abrir sidebar</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                            </path>
                        </svg>
                    </button>

                    <!-- Logo -->
                    <a href="{{ route('enfermera.home') }}" class="flex ms-2 md:me-24 items-center group">
                        <img src="{{ asset('logo_rosado.png') }}" alt="Clínica Santa Lucía"
                            class="w-12 h-12 rounded-xl object-cover logo-shadow">
                        <div class="ms-3">
                            <span
                                class="block text-xl font-bold text-gray-800 group-hover:text-purple-600 transition-colors">Clínica
                                Santa Lucía</span>
                            <span class="block text-xs text-gray-500 font-medium">Panel de Enfermería</span>
                        </div>
                    </a>
                </div>

                <!-- Usuario y logout -->
                <div class="flex items-center">
                    <div class="flex items-center ms-3 gap-3">
                        <div
                            class="flex items-center gap-3 px-4 py-2 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-200">
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-semibold text-gray-800">{{ Auth::user()->nomPer }}
                                    {{ Auth::user()->paternoPer }}</span>
                                <span class="text-xs text-purple-700 font-medium">Enfermera</span>
                            </div>
                            <div
                                class="w-10 h-10 user-badge rounded-full flex items-center justify-center font-bold text-white text-sm shadow-lg">
                                {{ strtoupper(substr(Auth::user()->nomPer, 0, 1)) }}{{ strtoupper(substr(Auth::user()->paternoPer, 0, 1)) }}
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="button" onclick="abrirModalLogout()"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-white bg-gradient-to-r from-rose-500 to-red-600 rounded-lg hover:from-rose-600 hover:to-red-700 transition-all transform hover:scale-105 shadow-md hover:shadow-lg">
                                <span class="material-icons text-lg">logout</span>
                                <span class="font-medium">Cerrar sesión</span>
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
        <div class="h-full px-3 pb-4 overflow-y-auto bg-gradient-to-b from-white to-purple-50">
            <!-- Header del Sidebar -->
            <div class="mb-6 p-4 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg text-white shadow-lg">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-icons text-2xl">local_hospital</span>
                    <h3 class="font-bold text-lg">Menú Enfermería</h3>
                </div>
                <p class="text-xs text-purple-100">Gestión de atención</p>
            </div>

            <ul class="space-y-2 font-medium">
                <!-- Inicio -->
                <li>
                    <a href="{{ route('enfermera.home') }}"
                        class="sidebar-item flex items-center p-3 rounded-xl hover:bg-purple-50 group {{ request()->routeIs('enfermera.home') ? 'bg-gradient-to-r from-purple-500 to-pink-600 text-white shadow-lg' : 'text-gray-700 hover:text-purple-600' }}">
                        <span
                            class="material-icons {{ request()->routeIs('enfermera.home') ? 'text-white' : 'text-purple-600' }}">dashboard</span>
                        <span class="ms-3 font-semibold">Inicio</span>
                        @if(request()->routeIs('enfermera.home'))
                            <span class="material-icons ms-auto text-sm">chevron_right</span>
                        @endif
                    </a>
                </li>

                <!-- Calendario de Atención -->
                <li>
                    <a href="{{ route('enfermera.calendario.atencion') }}"
                        class="sidebar-item flex items-center p-3 rounded-xl hover:bg-purple-50 group {{ request()->routeIs('enfermera.calendario.*') ? 'bg-gradient-to-r from-purple-500 to-pink-600 text-white shadow-lg' : 'text-gray-700 hover:text-purple-600' }}">
                        <span
                            class="material-icons {{ request()->routeIs('enfermera.calendario.*') ? 'text-white' : 'text-purple-600' }}">event_available</span>
                        <span class="ms-3 font-semibold">Calendario de Atención</span>
                        @if(request()->routeIs('enfermera.calendario.*'))
                            <span class="material-icons ms-auto text-sm">chevron_right</span>
                        @endif
                    </a>
                </li>

                <!-- Pacientes -->
                <li>
                    <a href="{{ route('enfermera.pacientes.pacientes') }}"
                        class="sidebar-item flex items-center p-3 rounded-xl hover:bg-purple-50 group {{ request()->routeIs('enfermera.pacientes.*') ? 'bg-gradient-to-r from-purple-500 to-pink-600 text-white shadow-lg' : 'text-gray-700 hover:text-purple-600' }}">
                        <span
                            class="material-icons {{ request()->routeIs('enfermera.pacientes.*') ? 'text-white' : 'text-purple-600' }}">people</span>
                        <span class="ms-3 font-semibold">Pacientes</span>
                        @if(request()->routeIs('enfermera.pacientes.*'))
                            <span class="material-icons ms-auto text-sm">chevron_right</span>
                        @endif
                    </a>
                </li>

                <!-- Médicos -->
                <li>
                    <a href="{{ route('enfermera.medicos.medicos') }}"
                        class="sidebar-item flex items-center p-3 rounded-xl hover:bg-purple-50 group {{ request()->routeIs('enfermera.medicos.*') ? 'bg-gradient-to-r from-purple-500 to-pink-600 text-white shadow-lg' : 'text-gray-700 hover:text-purple-600' }}">
                        <span
                            class="material-icons {{ request()->routeIs('enfermera.medicos.*') ? 'text-white' : 'text-purple-600' }}">medical_services</span>
                        <span class="ms-3 font-semibold">Médicos</span>
                        @if(request()->routeIs('enfermera.medicos.*'))
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
                                        class="inline-flex items-center text-sm font-semibold text-gray-700 hover:text-purple-600 transition-colors">
                                        {{ $breadcrumb['label'] }}
                                    </a>
                                @else
                                    <span class="text-sm font-semibold text-purple-600">{{ $breadcrumb['label'] }}</span>
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
    <div id="modalLogout" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="cerrarModalLogout()">
        </div>

        <!-- Modal -->
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
                <!-- Header con icono -->
                <div class="p-6 pb-0">
                    <div class="flex justify-center mb-4">
                        <div
                            class="w-20 h-20 rounded-full bg-gradient-to-br from-red-100 to-rose-100 flex items-center justify-center">
                            <div
                                class="w-14 h-14 rounded-full bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center shadow-lg">
                                <span class="material-icons text-white text-3xl">logout</span>
                            </div>
                        </div>
                    </div>

                    <!-- Título -->
                    <h3 class="text-2xl font-bold text-gray-900 text-center mb-2">¿Cerrar sesión?</h3>

                    <!-- Mensaje -->
                    <p class="text-gray-600 text-center mb-2">
                        Estás a punto de salir del sistema
                    </p>

                    <!-- Info usuario -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 mt-4 border border-gray-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold shadow-md">
                                {{ strtoupper(substr(Auth::user()->nomPer, 0, 1)) }}{{ strtoupper(substr(Auth::user()->paternoPer, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ Auth::user()->nomPer }}
                                    {{ Auth::user()->paternoPer }}</p>
                                <p class="text-sm text-gray-500 flex items-center gap-1">
                                    <span class="material-icons text-xs">verified_user</span>
                                    enfermera
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="p-6 flex gap-3">
                    <button type="button" onclick="cerrarModalLogout()"
                        class="flex-1 inline-flex justify-center items-center px-4 py-3 text-sm font-semibold text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                        <span class="material-icons text-lg mr-2">close</span>
                        Cancelar
                    </button>

                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-3 text-sm font-semibold text-white bg-gradient-to-r from-red-500 to-rose-600 rounded-xl hover:from-red-600 hover:to-rose-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <span class="material-icons text-lg mr-2">logout</span>
                            Sí, cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <script>
        function abrirModalLogout() {
            document.getElementById('modalLogout').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function cerrarModalLogout() {
            document.getElementById('modalLogout').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Cerrar con tecla Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                cerrarModalLogout();
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
