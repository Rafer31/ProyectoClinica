<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema Clínica</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-gray-50 to-indigo-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-6xl grid lg:grid-cols-2 gap-8 items-center">
            
            <!-- Sección izquierda - Información -->
            <div class="hidden lg:flex flex-col justify-center space-y-6 p-8">
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <span class="material-icons text-4xl text-white">local_hospital</span>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-blue-600">Clínica Santa Lucía</h1>
                            <p class="text-lg text-gray-600">Gestión médica profesional</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 mt-8">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="material-icons text-2xl text-blue-600">verified_user</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg text-gray-800">Seguro y Confiable</h3>
                            <p class="text-gray-600">Protección de datos médicos con los más altos estándares</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="material-icons text-2xl text-green-600">speed</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg text-gray-800">Rápido y Eficiente</h3>
                            <p class="text-gray-600">Acceso inmediato a información de pacientes</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="material-icons text-2xl text-amber-600">groups</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg text-gray-800">Gestión Integral</h3>
                            <p class="text-gray-600">Control completo de pacientes y servicios</p>
                        </div>
                    </div>
                </div>

                <div class="pt-8">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="material-icons text-4xl text-blue-600">people</span>
                            </div>
                            <div class="text-sm text-gray-600">Pacientes</div>
                            <div class="text-3xl font-bold text-blue-600">1,200+</div>
                        </div>

                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="material-icons text-4xl text-indigo-600">event_available</span>
                            </div>
                            <div class="text-sm text-gray-600">Consultas</div>
                            <div class="text-3xl font-bold text-indigo-600">5,000+</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección derecha - Formulario de login -->
            <div class="w-full">
                <div class="bg-white rounded-xl shadow-2xl">
                    <div class="p-8 lg:p-12">
                        <!-- Logo móvil -->
                        <div class="lg:hidden text-center mb-6">
                            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto shadow-lg mb-3">
                                <span class="material-icons text-4xl text-white">local_hospital</span>
                            </div>
                            <h2 class="text-3xl font-bold text-blue-600">Sistema Clínica</h2>
                        </div>

                        <div class="text-center lg:text-left mb-8">
                            <h2 class="text-3xl font-bold text-gray-800 mb-2">¡Bienvenido de nuevo!</h2>
                            <p class="text-gray-600">Ingresa tus credenciales para continuar</p>
                        </div>

                        <!-- Mensajes de error -->
                        @if ($errors->any())
                            <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
                                <span class="material-icons mr-3">error</span>
                                <div>
                                    @foreach ($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Mensaje de éxito al cerrar sesión -->
                        @if (session('success'))
                            <div class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50" role="alert">
                                <span class="material-icons mr-3">check_circle</span>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        <form id="login-form" action="{{ route('login') }}" method="POST" class="space-y-6">
                            @csrf

                            <!-- Campo Usuario -->
                            <div>
                                <label for="usuarioPer" class="block mb-2 text-sm font-semibold text-gray-700">Usuario</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <span class="material-icons text-gray-500">person</span>
                                    </div>
                                    <input 
                                        type="text" 
                                        id="usuarioPer" 
                                        name="usuarioPer" 
                                        value="{{ old('usuarioPer') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 p-3 {{ $errors->has('usuarioPer') ? 'border-red-500' : '' }}" 
                                        placeholder="Ingresa tu usuario"
                                        required
                                        autofocus
                                    />
                                </div>
                                @error('usuarioPer')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Campo Contraseña -->
                            <div>
                                <label for="clavePer" class="block mb-2 text-sm font-semibold text-gray-700">Contraseña</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <span class="material-icons text-gray-500">lock</span>
                                    </div>
                                    <input 
                                        type="password" 
                                        id="clavePer" 
                                        name="clavePer"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 p-3 {{ $errors->has('clavePer') ? 'border-red-500' : '' }}" 
                                        placeholder="••••••••"
                                        required
                                    />
                                </div>
                                @error('clavePer')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Recordarme -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input 
                                        id="remember" 
                                        type="checkbox" 
                                        name="remember"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                    />
                                    <label for="remember" class="ml-2 text-sm font-medium text-gray-700">Recordarme</label>
                                </div>
                            </div>

                            <!-- Botón de inicio de sesión -->
                            <button 
                                type="submit" 
                                class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-lg px-5 py-3 text-center inline-flex items-center justify-center transition-colors"
                            >
                                <span class="material-icons mr-2">login</span>
                                Iniciar Sesión
                            </button>
                        </form>

                        <!-- Footer del formulario -->
                        <div class="text-center mt-6 text-sm text-gray-600">
                            <p>¿Necesitas ayuda? <a href="#" class="text-blue-600 hover:underline font-medium">Contacta soporte técnico</a></p>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="text-center mt-6 text-sm text-gray-600">
                    <p>© 2025 Sistema Clínica Santa Lucía. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de carga -->
    <div id="loading-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-screen bg-gray-900 bg-opacity-50">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="p-6 text-center">
                    <span class="material-icons text-blue-600 text-6xl mb-4 animate-spin inline-block">sync</span>
                    <h3 class="mb-2 text-lg font-semibold text-gray-900">Verificando credenciales...</h3>
                    <p class="text-sm text-gray-600">Por favor espera un momento</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function() {
            const modal = document.getElementById('loading-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    </script>
</body>
</html>