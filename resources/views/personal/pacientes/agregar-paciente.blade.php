@extends('personal.layouts.personal')

@section('title', 'Agregar Paciente')

@section('content')
@php
$breadcrumbs = [
    ['label' => 'Inicio', 'url' => route('personal.home')],
    ['label' => 'Pacientes', 'url' => route('personal.pacientes.pacientes')],
    ['label' => 'Agregar Paciente']
];
@endphp

<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <span class="material-icons align-middle text-4xl text-green-600">person_add</span>
            Agregar Nuevo Paciente
        </h1>
        <p class="text-gray-600">Completa el formulario para registrar un nuevo paciente</p>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="#" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label for="nombre" class="block mb-2 text-sm font-medium text-gray-900">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>

                <!-- CI -->
                <div>
                    <label for="ci" class="block mb-2 text-sm font-medium text-gray-900">Cédula de Identidad</label>
                    <input type="text" id="ci" name="ci" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>

                <!-- Teléfono -->
                <div>
                    <label for="telefono" class="block mb-2 text-sm font-medium text-gray-900">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                    <input type="email" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('personal.pacientes.pacientes') }}" class="px-6 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Guardar Paciente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
