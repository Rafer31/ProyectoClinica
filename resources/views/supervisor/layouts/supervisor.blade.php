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

    @stack('styles')
</head>
<body class="bg-gray-50">

    <!-- Navbar -->
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <!-- Botón toggle sidebar -->
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <span class="sr-only">Abrir sidebar</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                        </svg>
                    </button>

                    <!-- Logo -->
                    <a href="{{ route('supervisor.home') }}" class="flex ms-2 md:me-24">
                        <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                            <span class="material-icons text-white text-2xl">admin_panel_settings</span>
                        </div>
                        <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap ms-2">Sistema Clínica</span>
                    </a>
                </div>

                <!-- Usuario y logout -->
                <div class="flex items-center">
                    <div class="flex items-center ms-3">
                        <div class="flex items-center me-4">
                            <span class="material-icons text-gray-600 me-2">person</span>
                            <span class="text-sm font-medium text-gray-700">{{ Auth::user()->nombrePer }}</span>
                            <span class="ml-2 px-2 py-1 text-xs font-semibold text-purple-700 bg-purple-100 rounded-full">Supervisor</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center px-3 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                <span class="material-icons text-sm me-1">logout</span>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0" aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white">
            <ul class="space-y-2 font-medium">
                <!-- Inicio -->
                <li>
                    <a href="{{ route('supervisor.home') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('supervisor.home') ? 'bg-purple-50 text-purple-600' : 'text-gray-900' }}">
                        <span class="material-icons">dashboard</span>
                        <span class="ms-3">Inicio</span>
                    </a>
                </li>

                <!-- Gestión Personal -->
                <li>
                    <a href="{{ route('supervisor.gestion-personal.gestion-personal') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('supervisor.gestion-personal.*') ? 'bg-purple-50 text-purple-600' : 'text-gray-900' }}">
                        <span class="material-icons">badge</span>
                        <span class="ms-3">Gestión Personal</span>
                    </a>
                </li>

                <!-- Clínicas -->
                <li>
                    <a href="{{ route('supervisor.clinicas.clinicas') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('supervisor.clinicas.*') ? 'bg-purple-50 text-purple-600' : 'text-gray-900' }}">
                        <span class="material-icons">business</span>
                        <span class="ms-3">Clínicas</span>
                    </a>
                </li>

                <!-- Estadísticas -->
                <li>
                    <a href="{{ route('supervisor.estadisticas.estadisticas') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('supervisor.estadisticas.*') ? 'bg-purple-50 text-purple-600' : 'text-gray-900' }}">
                        <span class="material-icons">bar_chart</span>
                        <span class="ms-3">Estadísticas</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Contenido principal -->
    <div class="p-4 sm:ml-64 mt-14">
        <div class="p-4">
            <!-- Breadcrumb (opcional) -->
            @if(isset($breadcrumbs))
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    @foreach($breadcrumbs as $breadcrumb)
                        <li class="inline-flex items-center">
                            @if(!$loop->first)
                                <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                            @endif
                            @if(isset($breadcrumb['url']))
                                <a href="{{ $breadcrumb['url'] }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-purple-600">
                                    {{ $breadcrumb['label'] }}
                                </a>
                            @else
                                <span class="text-sm font-medium text-gray-500">{{ $breadcrumb['label'] }}</span>
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
