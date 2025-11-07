@extends('personal.layouts.personal')

@section('title', 'Médicos')

@section('content')
@php
$breadcrumbs = [
    ['label' => 'Inicio', 'url' => route('personal.home')],
    ['label' => 'Médicos']
];
@endphp

<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <span class="material-icons align-middle text-4xl text-blue-600">medical_services</span>
                    Gestión de Médicos
                </h1>
                <p class="text-gray-600">Administra el personal médico de la clínica</p>
            </div>
            <button class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <span class="material-icons me-2">add</span>
                Agregar Médico
            </button>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search-medico" class="block mb-2 text-sm font-medium text-gray-900">Buscar médico</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <span class="material-icons text-gray-500 text-sm">search</span>
                    </div>
                    <input type="text" id="search-medico" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Nombre, CI o especialidad">
                </div>
            </div>
            <div>
                <label for="especialidad" class="block mb-2 text-sm font-medium text-gray-900">Especialidad</label>
                <select id="especialidad" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option selected>Todas las especialidades</option>
                    <option value="radiologia">Radiología</option>
                    <option value="ecografia">Ecografía</option>
                    <option value="tomografia">Tomografía</option>
                </select>
            </div>
            <div>
                <label for="estado" class="block mb-2 text-sm font-medium text-gray-900">Estado</label>
                <select id="estado" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option selected>Todos</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tarjetas de médicos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Médico 1 -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="material-icons text-blue-600 text-2xl">person</span>
                        </div>
                        <div class="ms-3">
                            <h3 class="text-lg font-semibold text-gray-900">Dr. Juan Pérez</h3>
                            <p class="text-sm text-gray-600">CI: 12345678</p>
                        </div>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Activo</span>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="material-icons text-sm me-2">medical_information</span>
                        <span>Radiología</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="material-icons text-sm me-2">phone</span>
                        <span>70123456</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="material-icons text-sm me-2">email</span>
                        <span>juan.perez@clinica.com</span>
                    </div>
                </div>

                <div class="flex justify-between pt-4 border-t border-gray-200">
                    <button class="flex items-center text-blue-600 hover:text-blue-700 text-sm font-medium">
                        <span class="material-icons text-sm me-1">edit</span>
                        Editar
                    </button>
                    <button class="flex items-center text-gray-600 hover:text-gray-700 text-sm font-medium">
                        <span class="material-icons text-sm me-1">visibility</span>
                        Ver detalles
                    </button>
                </div>
            </div>
        </div>

        <!-- Médico 2 -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="material-icons text-purple-600 text-2xl">person</span>
                        </div>
                        <div class="ms-3">
                            <h3 class="text-lg font-semibold text-gray-900">Dra. María López</h3>
                            <p class="text-sm text-gray-600">CI: 87654321</p>
                        </div>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Activo</span>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="material-icons text-sm me-2">medical_information</span>
                        <span>Ecografía</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="material-icons text-sm me-2">phone</span>
                        <span>71234567</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="material-icons text-sm me-2">email</span>
                        <span>maria.lopez@clinica.com</span>
                    </div>
                </div>

                <div class="flex justify-between pt-4 border-t border-gray-200">
                    <button class="flex items-center text-blue-600 hover:text-blue-700 text-sm font-medium">
                        <span class="material-icons text-sm me-1">edit</span>
                        Editar
                    </button>
                    <button class="flex items-center text-gray-600 hover:text-gray-700 text-sm font-medium">
                        <span class="material-icons text-sm me-1">visibility</span>
                        Ver detalles
                    </button>
                </div>
            </div>
        </div>

        <!-- Médico 3 -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="material-icons text-green-600 text-2xl">person</span>
                        </div>
                        <div class="ms-3">
                            <h3 class="text-lg font-semibold text-gray-900">Dr. Carlos Ruiz</h3>
                            <p class="text-sm text-gray-600">CI: 11223344</p>
                        </div>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Activo</span>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="material-icons text-sm me-2">medical_information</span>
                        <span>Tomografía</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="material-icons text-sm me-2">phone</span>
                        <span>72345678</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="material-icons text-sm me-2">email</span>
                        <span>carlos.ruiz@clinica.com</span>
                    </div>
                </div>

                <div class="flex justify-between pt-4 border-t border-gray-200">
                    <button class="flex items-center text-blue-600 hover:text-blue-700 text-sm font-medium">
                        <span class="material-icons text-sm me-1">edit</span>
                        Editar
                    </button>
                    <button class="flex items-center text-gray-600 hover:text-gray-700 text-sm font-medium">
                        <span class="material-icons text-sm me-1">visibility</span>
                        Ver detalles
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
