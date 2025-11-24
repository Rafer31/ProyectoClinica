<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema Clínica</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<style>
    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    /* Animación de brillo */
    @keyframes shine {
        0% {
            opacity: 0.7;
        }

        50% {
            opacity: 1;
        }

        100% {
            opacity: 0.7;
        }
    }

    .material-icons {
        animation: shine 2s ease-in-out infinite;
    }

    @keyframes pulse-ring {
        0% {
            transform: scale(0.8);
            opacity: 0.8;
        }

        50% {
            transform: scale(1.1);
            opacity: 0.4;
        }

        100% {
            transform: scale(0.8);
            opacity: 0.8;
        }
    }

    /* Animación de rotación suave */
    @keyframes spin-slow {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Animación del gradiente */
    @keyframes gradient-shift {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    /* Animación de los puntos */
    @keyframes bounce-dot {

        0%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-8px);
        }
    }

    .pulse-ring {
        animation: pulse-ring 2s ease-in-out infinite;
    }

    .spin-slow {
        animation: spin-slow 3s linear infinite;
    }

    .gradient-animate {
        background: linear-gradient(-45deg, #3b82f6, #6366f1, #8b5cf6, #3b82f6);
        background-size: 400% 400%;
        animation: gradient-shift 3s ease infinite;
    }

    .dot-1 {
        animation: bounce-dot 1.4s ease-in-out infinite;
    }

    .dot-2 {
        animation: bounce-dot 1.4s ease-in-out 0.2s infinite;
    }

    .dot-3 {
        animation: bounce-dot 1.4s ease-in-out 0.4s infinite;
    }

    /* Efecto glassmorphism */
    .glass {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
</style>

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
                        <!-- Card 1: Disponibilidad 24/7 -->
                        <div
                            class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10">
                            </div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="material-icons text-4xl">schedule</span>
                                </div>
                                <div class="text-sm font-medium opacity-90">Disponibilidad</div>
                                <div class="text-3xl font-bold">24/7</div>
                                <div class="text-xs opacity-75 mt-1">Acceso continuo</div>
                            </div>
                        </div>

                        <!-- Card 2: Seguridad 100% -->
                        <div
                            class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10">
                            </div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="material-icons text-4xl">shield</span>
                                </div>
                                <div class="text-sm font-medium opacity-90">Seguridad</div>
                                <div class="text-3xl font-bold">100%</div>
                                <div class="text-xs opacity-75 mt-1">Datos protegidos</div>
                            </div>
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
                            <div
                                class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto shadow-lg mb-3">
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
                            <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50"
                                role="alert">
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
                            <div class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50"
                                role="alert">
                                <span class="material-icons mr-3">check_circle</span>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        <form id="login-form" action="{{ route('login') }}" method="POST" class="space-y-6">
                            @csrf

                            <!-- Campo Usuario -->
                            <div>
                                <label for="usuarioPer"
                                    class="block mb-2 text-sm font-semibold text-gray-700">Usuario</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <span class="material-icons text-gray-500">person</span>
                                    </div>
                                    <input type="text" id="usuarioPer" name="usuarioPer" value="{{ old('usuarioPer') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 p-3 {{ $errors->has('usuarioPer') ? 'border-red-500' : '' }}"
                                        placeholder="Ingresa tu usuario" required autofocus />
                                </div>
                                @error('usuarioPer')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Campo Contraseña -->
                            <div>
                                <label for="clavePer"
                                    class="block mb-2 text-sm font-semibold text-gray-700">Contraseña</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <span class="material-icons text-gray-500">lock</span>
                                    </div>
                                    <input type="password" id="clavePer" name="clavePer"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 p-3 {{ $errors->has('clavePer') ? 'border-red-500' : '' }}"
                                        placeholder="••••••••" required />
                                </div>
                                @error('clavePer')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Recordarme -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input id="remember" type="checkbox" name="remember"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2" />
                                    <label for="remember"
                                        class="ml-2 text-sm font-medium text-gray-700">Recordarme</label>
                                </div>
                            </div>

                            <!-- Botón de inicio de sesión -->
                            <button type="submit"
                                class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-lg px-5 py-3 text-center inline-flex items-center justify-center transition-colors">
                                <span class="material-icons mr-2">login</span>
                                Iniciar Sesión
                            </button>
                        </form>

                        <!-- Footer del formulario -->
                        <div class="text-center mt-6 text-sm text-gray-600">
                            <p>¿Necesitas ayuda? <a href="#" class="text-blue-600 hover:underline font-medium">Contacta
                                    soporte técnico</a></p>
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

    <!-- Modal de carga mejorado -->
    <div id="loading-modal" class="hidden fixed inset-0 z-50 overflow-y-auto overflow-x-hidden">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>


        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-8 w-full max-w-sm">

                <div class="flex justify-center mb-6">
                    <div class="relative">
                        <div class="absolute inset-0 w-24 h-24 rounded-full bg-blue-200 animate-ping opacity-30"></div>
                        <div class="relative w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 p-1 animate-spin"
                            style="animation-duration: 3s;">
                            <div class="w-full h-full rounded-full bg-white flex items-center justify-center">
                                <div
                                    class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                                    <span class="material-icons text-white text-3xl">local_hospital</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center space-y-3">
                    <h3 class="text-xl font-bold text-gray-800">Verificando credenciales</h3>
                    <p class="text-gray-500 text-sm">Por favor espera un momento...</p>
                </div>

                <div class="mt-6">
                    <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-500 via-indigo-500 to-blue-500 rounded-full animate-pulse"
                            style="width: 100%;"></div>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-center space-x-2 text-xs text-gray-400">
                    <span class="material-icons text-sm">security</span>
                    <span>Conexión segura</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function () {
            const modal = document.getElementById('loading-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    </script>
</body>

</html>
