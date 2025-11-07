@extends('personal.layouts.personal')

@section('title', 'Lista de Pacientes')

@section('content')
@php
$breadcrumbs = [
    ['label' => 'Inicio', 'url' => route('personal.home')],
    ['label' => 'Pacientes']
];
@endphp

<div class="space-y-6">
    <!-- Encabezado con botón -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <span class="material-icons align-middle text-4xl text-blue-600">people</span>
                    Lista de Pacientes
                </h1>
                <p class="text-gray-600">Gestiona la información de los pacientes</p>
            </div>
            <a href="{{ route('personal.pacientes.agregar') }}" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <span class="material-icons me-2">add</span>
                Agregar Paciente
            </a>
        </div>
    </div>

    <!-- Tabla de pacientes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Nombre</th>
                    <th scope="col" class="px-6 py-3">CI</th>
                    <th scope="col" class="px-6 py-3">Teléfono</th>
                    <th scope="col" class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">Juan Pérez</td>
                    <td class="px-6 py-4">12345678</td>
                    <td class="px-6 py-4">70123456</td>
                    <td class="px-6 py-4">
                        <a href="#" class="font-medium text-blue-600 hover:underline me-3">Editar</a>
                        <a href="#" class="font-medium text-red-600 hover:underline">Eliminar</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
