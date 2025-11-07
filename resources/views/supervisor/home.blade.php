@extends('layouts.app')

@section('title', 'Panel supervisor')

@section('content')
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 dark:bg-gray-900">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <!-- Logo/Título -->
            <a href="{{ route('supervisor.home') }}" class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-white text-2xl">local_hospital</span>
                </div>
                <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">Sistema Clínica</span>
            </a>

            <!-- Botón hamburguesa para móvil -->
            <button data-collapse-toggle="navbar-default" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                aria-controls="navbar-default" aria-expanded="false">
                <span class="sr-only">Abrir menú</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>

            <!-- Menú de navegación -->
            <div class="hidden w-full md:block md:w-auto" id="navbar-default">
                <ul
                    class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                    <li>
                        <a href="{{ route('supervisor.home') }}"
                            class="flex items-center py-2 px-3 rounded md:bg-transparent md:p-0 {{ request()->routeIs('supervisor.home') ? 'text-white bg-blue-700 md:text-blue-700 md:bg-transparent' : 'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:text-white' }}">
                            <span class="material-icons text-sm mr-1">home</span>
                            Inicio
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('supervisor.accesos.accesos') }}"
                            class="flex items-center py-2 px-3 rounded md:bg-transparent md:p-0 {{ request()->routeIs('supervisor.cronogramas') ? 'text-white bg-blue-700 md:text-blue-700 md:bg-transparent' : 'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:text-white' }}">
                            <span class="material-icons text-sm mr-1">security</span>
                            Accesos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('supervisor.clinicas.clinicas') }}"
                            class="flex items-center py-2 px-3 rounded md:bg-transparent md:p-0 {{ request()->routeIs('supervisor.medicos') ? 'text-white bg-blue-700 md:text-blue-700 md:bg-transparent' : 'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:text-white' }}">
                            <span class="material-icons text-sm mr-1">medical_services</span>
                            Clinicas
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('supervisor.estadisticas.estadisticas') }}"
                            class="flex items-center py-2 px-3 rounded md:bg-transparent md:p-0 {{ request()->routeIs('supervisor.servicios') ? 'text-white bg-blue-700 md:text-blue-700 md:bg-transparent' : 'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 dark:text-white' }}">
                            <span class="material-icons text-sm mr-1">charts</span>
                            Estadisticas
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full py-2 px-3 text-white bg-red-600 rounded hover:bg-red-700 md:inline-flex md:w-auto">
                                <span class="material-icons text-sm mr-1">logout</span>
                                Cerrar sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido de la página -->
    <main class="max-w-screen-xl mx-auto p-4">
        @yield('page-content')
    </main>
@endsection
