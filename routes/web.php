<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\ServicioController;
use Illuminate\Support\Facades\Auth;

// Página inicial -> redirige al login
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/home/estadisticas', [HomeController::class, 'estadisticas']);
// Rutas públicas (no autenticadas)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// Rutas protegidas (autenticadas) con middleware para prevenir caché
Route::middleware(['auth', 'prevent.back.history'])->group(function () {
    // Panel del supervisor
    Route::prefix('supervisor')->name('supervisor.')->group(function () {
        Route::get('/home', function () {
            return view('supervisor.home');
        })->name('home');
        Route::get('/accesos', function () {
            return view('supervisor.accesos.accesos');
        })->name('accesos.accesos');
        Route::get('/clinicas', function () {
            return view('supervisor.clinicas.clinicas');
        })->name('clinicas.clinicas');
        Route::get('/estadisticas', function () {
            return view('supervisor.estadisticas.estadisticas');
        })->name('estadisticas.estadisticas');
    });

    // Panel del personal de imagen
    Route::prefix('personal')->name('personal.')->group(function () {
        Route::get('/home', function () {
            return view('personal.home');
        })->name('home');

        // ==========================================
        // RUTAS DE MÉDICOS
        // ==========================================
        Route::prefix('medicos')->name('medicos.')->group(function () {
            Route::get('/', function () {
                return view('personal.medicos.medicos');
            })->name('medicos');
            Route::get('/agregar', function () {
                return view('personal.medicos.form-medicos')
                    ->with('medico', null);
            })->name('agregar');
            Route::get('/editar/{id}', function ($id) {
                $medico = App\Models\Medico::findOrFail($id);
                return view('personal.medicos.form-medicos', compact('medico'));
            })->name('editar');
        });

        // ==========================================
        // RUTAS DE CRONOGRAMAS (SOLO VISTAS)
        // ==========================================
        Route::get('/cronogramas', function () {
            return view('personal.cronogramas.cronogramas');
        })->name('cronogramas.cronogramas');

        // ==========================================
        // RUTAS DE PACIENTES
        // ==========================================
        Route::prefix('pacientes')->name('pacientes.')->group(function () {
            Route::get('/', function () {
                return view('personal.pacientes.pacientes');
            })->name('pacientes');
            Route::get('/agregar', function () {
                return view('personal.pacientes.form-paciente')
                    ->with('paciente', null);
            })->name('agregar');
            Route::get('/editar/{id}', function ($id) {
                $paciente = App\Models\Paciente::findOrFail($id);
                return view('personal.pacientes.form-paciente', compact('paciente'));
            })->name('editar');
        });
        Route::get('/tipos-estudio', function () {
            return view('personal.tipos-estudio.tipos-estudio');
        })->name('tipos-estudio.index');

        Route::get('/tipos-estudio/crear', function () {
            return view('personal.tipos-estudio.form-tipo-estudio')
                ->with('tipoEstudio', null);
        })->name('tipos-estudio.crear');
Route::get('/reportes/dia', [HomeController::class, 'reporteDia'])->name('reportes.dia');
    Route::get('/reportes/semana', [HomeController::class, 'reporteSemana'])->name('reportes.semana');
    Route::get('/reportes/mes', [HomeController::class, 'reporteMes'])->name('reportes.mes');
        Route::get('/tipos-estudio/editar/{id}', function ($id) {
            $tipoEstudio = App\Models\TipoEstudio::with('requisitos')->findOrFail($id);
            return view('personal.tipos-estudio.form-tipo-estudio', compact('tipoEstudio'));
        })->name('tipos-estudio.editar');
        Route::get('/servicios', function () {
            return view('personal.servicios.servicios');
        })->name('servicios.servicios');
        Route::get('/servicios/calcular-ficha/{fechaCrono}', [ServicioController::class, 'calcularNumeroFicha']);
    });

    // ==========================================
    // API ROUTES (IMPORTANTES: Estas son las que usa el JavaScript)
    // ==========================================
    Route::prefix('api')->name('api.')->group(function () {
        Route::prefix('supervisor')->name('supervisor.')->group(function () {
            // Rutas para el supervisor
        });
        Route::get('/usuario-actual', function () {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        })->name('usuario-actual');
        Route::prefix('personal')->name('personal.')->group(function () {
            // API de Pacientes
            Route::prefix('pacientes')->name('pacientes.')->group(function () {
                Route::get('/', [PacienteController::class, 'index'])->name('index');
                Route::post('/', [PacienteController::class, 'store'])->name('store');
                Route::get('/{id}', [PacienteController::class, 'show'])->name('show');
                Route::put('/{id}', [PacienteController::class, 'update'])->name('update');
                Route::delete('/{id}', [PacienteController::class, 'cambiarEstado'])->name('cambiarEstado');
            });
            Route::prefix('requisitos')->name('requisitos.')->group(function () {
                Route::get('/', [App\Http\Controllers\RequisitoController::class, 'index'])->name('index');
                Route::post('/', [App\Http\Controllers\RequisitoController::class, 'store'])->name('store');
                Route::get('/{id}', [App\Http\Controllers\RequisitoController::class, 'show'])->name('show');
                Route::put('/{id}', [App\Http\Controllers\RequisitoController::class, 'update'])->name('update');
                Route::delete('/{id}', [App\Http\Controllers\RequisitoController::class, 'destroy'])->name('destroy');
            });
            Route::prefix('requisitos')->name('requisitos.')->group(function () {
                Route::get('/', [App\Http\Controllers\RequisitoController::class, 'index'])->name('index');
                Route::post('/', [App\Http\Controllers\RequisitoController::class, 'store'])->name('store');
                Route::get('/{id}', [App\Http\Controllers\RequisitoController::class, 'show'])->name('show');
                Route::put('/{id}', [App\Http\Controllers\RequisitoController::class, 'update'])->name('update');
                Route::delete('/{id}', [App\Http\Controllers\RequisitoController::class, 'destroy'])->name('destroy');
            });
            Route::prefix('tipos-estudio')->name('tipos-estudio.')->group(function () {
                Route::get('/', [App\Http\Controllers\TipoEstudioController::class, 'index'])->name('index');
                Route::post('/con-requisitos', [App\Http\Controllers\TipoEstudioRequisitoController::class, 'crearConRequisitos'])->name('crearConRequisitos');
                Route::get('/exportar-pdf/{id}', [App\Http\Controllers\TipoEstudioController::class, 'exportarPDF'])->name('exportarPDF');
                Route::get('/{id}', [App\Http\Controllers\TipoEstudioController::class, 'show'])->name('show');
                Route::put('/{id}', [App\Http\Controllers\TipoEstudioController::class, 'update'])->name('update');
                Route::delete('/{id}', [App\Http\Controllers\TipoEstudioController::class, 'destroy'])->name('destroy');

                // Gestión de requisitos
                Route::prefix('{codTest}/requisitos')->group(function () {
                    Route::get('/', [App\Http\Controllers\TipoEstudioRequisitoController::class, 'listarRequisitos'])->name('requisitos.index');
                    Route::post('/asignar', [App\Http\Controllers\TipoEstudioRequisitoController::class, 'asignarRequisitos'])->name('requisitos.asignar');
                    Route::post('/agregar', [App\Http\Controllers\TipoEstudioRequisitoController::class, 'agregarRequisito'])->name('requisitos.agregar');
                    Route::put('/{codRequisito}/observacion', [App\Http\Controllers\TipoEstudioRequisitoController::class, 'actualizarObservacion'])->name('requisitos.actualizarObservacion');
                    Route::delete('/{codRequisito}', [App\Http\Controllers\TipoEstudioRequisitoController::class, 'eliminarRequisito'])->name('requisitos.eliminar');
                });
            });
            // API de Médicos
            Route::prefix('medicos')->name('medicos.')->group(function () {
                Route::get('/', [MedicoController::class, 'index'])->name('index');
                Route::post('/', [MedicoController::class, 'store'])->name('store');
                Route::get('/{id}', [MedicoController::class, 'show'])->name('show');
                Route::put('/{id}', [MedicoController::class, 'update'])->name('update');
                Route::delete('/{id}', [MedicoController::class, 'destroy'])->name('destroy');
            });

            // API de Cronogramas - MOVIDAS AQUÍ
            Route::prefix('cronogramas')->name('cronogramas.')->group(function () {
                // Rutas especiales PRIMERO (antes de las rutas con parámetros)
                Route::get('/activos', [App\Http\Controllers\CronogramaAtencionController::class, 'activos'])->name('activos');
                Route::get('/entre-fechas', [App\Http\Controllers\CronogramaAtencionController::class, 'entreFechas'])->name('entreFechas');
                Route::get('/personal/{codPer}', [App\Http\Controllers\CronogramaAtencionController::class, 'porPersonal'])->name('porPersonal');

                // Rutas CRUD estándar
                Route::get('/', [App\Http\Controllers\CronogramaAtencionController::class, 'index'])->name('index');
                Route::post('/', [App\Http\Controllers\CronogramaAtencionController::class, 'store'])->name('store');
                Route::get('/{fecha}', [App\Http\Controllers\CronogramaAtencionController::class, 'show'])->name('show');
                Route::put('/{fecha}', [App\Http\Controllers\CronogramaAtencionController::class, 'update'])->name('update');
                Route::patch('/{fecha}/estado', [App\Http\Controllers\CronogramaAtencionController::class, 'cambiarEstado'])->name('cambiarEstado');
                Route::delete('/{fecha}', [App\Http\Controllers\CronogramaAtencionController::class, 'destroy'])->name('destroy');
            });
            Route::prefix('servicios')->name('servicios.')->group(function () {
                Route::get('/', [App\Http\Controllers\ServicioController::class, 'index'])->name('index');
                Route::get('/datos-formulario', [App\Http\Controllers\ServicioController::class, 'datosFormulario'])->name('datosFormulario');
                Route::get('/estadisticas', [App\Http\Controllers\ServicioController::class, 'estadisticas'])->name('estadisticas');
                Route::get('/paciente/{codPa}', [App\Http\Controllers\ServicioController::class, 'porPaciente'])->name('porPaciente');
                Route::get('/estado/{estado}', [App\Http\Controllers\ServicioController::class, 'porEstado'])->name('porEstado');
                Route::post('/', [App\Http\Controllers\ServicioController::class, 'store'])->name('store');
                Route::get('/{id}', [App\Http\Controllers\ServicioController::class, 'show'])->name('show');
                Route::put('/{id}', [App\Http\Controllers\ServicioController::class, 'update'])->name('update');
                Route::patch('/{id}/estado', [App\Http\Controllers\ServicioController::class, 'cambiarEstado'])->name('cambiarEstado');
                Route::post('/{id}/diagnosticos', [App\Http\Controllers\ServicioController::class, 'asociarDiagnosticos'])->name('asociarDiagnosticos');
                Route::delete('/{id}', [App\Http\Controllers\ServicioController::class, 'destroy'])->name('destroy');
            });
        });
    });

    // Cerrar sesión - Solo POST, no GET
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});


// Ruta fallback - Maneja URLs no encontradas
Route::fallback(function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    if ($user->codRol == 1) {
        return redirect()->route('supervisor.home');
    } elseif ($user->codRol == 2) {
        return redirect()->route('personal.home');
    }

    Auth::logout();
    return redirect()->route('login')->with('error', 'Rol no válido');
});
