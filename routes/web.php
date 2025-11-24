<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Personal\ServicioPdfController;
use App\Http\Controllers\EstadisticasSupervisorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\PersonalSaludController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\ConsultorioController;
use App\Http\Controllers\AsignacionConsultorioController;
use App\Http\Controllers\ServicioApiController;
use Illuminate\Support\Facades\Auth;

// Página inicial -> redirige al login
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        if ($user->codRol == 1) {
            return redirect()->route('supervisor.home');
        } elseif ($user->codRol == 2) {
            return redirect()->route('personal.home');
        } elseif ($user->codRol == 3) {
            return redirect()->route('enfermera.home');
        }
    }

    return redirect()->route('login');
});

// Rutas públicas (no autenticadas)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// Rutas protegidas (autenticadas) con middleware para prevenir caché
Route::middleware(['auth', 'prevent.back.history'])->group(function () {

    // ==========================================
    // PANEL DE ENFERMERA - NUEVO
    // ==========================================
    Route::prefix('enfermera')->name('enfermera.')->group(function () {
    Route::get('/home', function () {
        return view('enfermera.home');
    })->name('home');
Route::get('/servicios/{id}/pdf-requisitos', [ServicioController::class, 'generarPDFRequisitos'])
    ->name('servicios.pdf-requisitos');
    // Calendario de Atención
    Route::prefix('calendario')->name('calendario.')->group(function () {
        Route::get('/', function () {
            return view('enfermera.calendario.calendario-atencion');
        })->name('atencion');

        Route::get('/agregar-paciente', function () {
            return view('enfermera.calendario.form-agregar-paciente');
        })->name('agregar-paciente');

        Route::get('/agregar-medico', function () {
            return view('enfermera.calendario.form-agregar-medico');
        })->name('agregar-medico');
    });
});


    // Panel del supervisor
    Route::prefix('supervisor')->name('supervisor.')->group(function () {
        Route::get('/home', function () {
            return view('supervisor.home');
        })->name('home');

        // Gestión de Personal
        Route::get('/gestion-personal', function () {
            return view('supervisor.gestion-personal.gestion-personal');
        })->name('gestion-personal.gestion-personal');

        Route::get('/gestion-personal/agregar', function () {
            return view('supervisor.gestion-personal.form-personal')
                ->with('personal', null);
        })->name('gestion-personal.agregar');

        Route::get('/gestion-personal/editar/{id}', function ($id) {
            $personal = App\Models\PersonalSalud::with('rol')->findOrFail($id);
            return view('supervisor.gestion-personal.form-personal', compact('personal'));
        })->name('gestion-personal.editar');

        // Consultorios
        Route::get('/gestion-personal/consultorios', function () {
            return view('supervisor.gestion-personal.consultorios');
        })->name('gestion-personal.consultorios');

        Route::get('/estadisticas', function () {
            return view('supervisor.estadisticas.estadisticas');
        })->name('estadisticas.estadisticas');
    });

    // Panel del personal de imagen
    Route::prefix('personal')->name('personal.')->group(function () {
        Route::get('/home', function () {
            return view('personal.home');
        })->name('home');

        Route::get('servicios/{nroServ}/pdf', [ServicioPdfController::class, 'generarFichaServicio'])
            ->name('servicios.pdf');

        Route::get('servicios/{nroServ}/pdf/ver', [ServicioPdfController::class, 'visualizarFichaServicio'])
            ->name('servicios.pdf.ver');

        // Reportes PDF
        Route::get('/reportes/dia', [HomeController::class, 'reporteDia'])->name('reportes.dia');
        Route::get('/reportes/semana', [HomeController::class, 'reporteSemana'])->name('reportes.semana');
        Route::get('/reportes/mes', [HomeController::class, 'reporteMes'])->name('reportes.mes');

        // Médicos
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

        // Cronogramas
        Route::get('/cronogramas', function () {
            return view('personal.cronogramas.cronogramas');
        })->name('cronogramas.cronogramas');

        Route::get('/cronogramas/calendario', function () {
            return view('personal.cronogramas.calendario-horarios');
        })->name('cronogramas.calendario');

        // Pacientes
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

        // Servicios
        Route::get('/atendidos', function () {
            return view('personal.servicios.atendidos');
        })->name('servicios.atendidos');

        Route::get('/servicios', function () {
            return view('personal.servicios.servicios');
        })->name('servicios.servicios');

        // Tipos de Estudio
        Route::get('/tipos-estudio', function () {
            return view('personal.tipos-estudio.tipos-estudio');
        })->name('tipos-estudio.index');

        Route::get('/tipos-estudio/crear', function () {
            return view('personal.tipos-estudio.form-tipo-estudio')
                ->with('tipoEstudio', null);
        })->name('tipos-estudio.crear');

        Route::get('/tipos-estudio/editar/{id}', function ($id) {
            $tipoEstudio = App\Models\TipoEstudio::with('requisitos')->findOrFail($id);
            return view('personal.tipos-estudio.form-tipo-estudio', compact('tipoEstudio'));
        })->name('tipos-estudio.editar');

        // Requisitos
        Route::get('/requisitos', [App\Http\Controllers\RequisitoController::class, 'indexView'])
            ->name('requisitos.index');
    });

    // ==========================================
    // API ROUTES
    // ==========================================
    Route::prefix('api')->name('api.')->group(function () {

        // API ENFERMERA - NUEVO
       Route::prefix('enfermera')->name('enfermera.')->group(function () {
    // Cronogramas
    Route::get('/cronogramas', [App\Http\Controllers\CronogramaAtencionController::class, 'index'])
        ->name('cronogramas.index');
    Route::get('/cronogramas/activo', [App\Http\Controllers\CronogramaAtencionController::class, 'obtenerCronogramaActivo'])
        ->name('cronogramas.activo');
    Route::get('/cronogramas/entre-fechas', [App\Http\Controllers\CronogramaAtencionController::class, 'entreFechas'])
        ->name('cronogramas.entreFechas');
    Route::post('/cronogramas', [App\Http\Controllers\CronogramaAtencionController::class, 'store'])
        ->name('cronogramas.store');

    // Servicios
    Route::get('/servicios/por-fecha-cronograma/{fechaCrono}', [ServicioController::class, 'serviciosPorFechaCronograma'])
        ->name('servicios.porFechaCronograma');
    Route::get('/servicios/horarios-disponibles/{fechaCrono}', [ServicioController::class, 'horariosDisponibles'])
        ->name('servicios.horariosDisponibles');
    Route::get('/servicios/calcular-ficha/{fechaCrono}', [ServicioController::class, 'calcularNumeroFicha'])
        ->name('servicios.calcularFicha');
    Route::get('/servicios/datos-formulario', [ServicioController::class, 'datosFormulario'])
        ->name('servicios.datosFormulario');
    Route::post('/servicios', [ServicioController::class, 'store'])
        ->name('servicios.store');
    Route::get('/servicios/{id}', [ServicioController::class, 'show'])
        ->name('servicios.show');

    // Pacientes
    Route::get('/pacientes', [PacienteController::class, 'index'])
        ->name('pacientes.index');
    Route::post('/pacientes', [PacienteController::class, 'store'])
        ->name('pacientes.store');
    Route::get('/pacientes/{id}', [PacienteController::class, 'show'])
        ->name('pacientes.show');

    // Médicos
    Route::get('/medicos', [MedicoController::class, 'index'])
        ->name('medicos.index');
    Route::post('/medicos', [MedicoController::class, 'store'])
        ->name('medicos.store');
    Route::get('/medicos/{id}', [MedicoController::class, 'show'])
        ->name('medicos.show');

    // Tipos de Estudio
    Route::get('/tipos-estudio', function() {
        $tipos = App\Models\TipoEstudio::select('codTest', 'descripcion')
            ->orderBy('descripcion')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $tipos
        ]);
    })->name('tiposEstudio.index');
});
        // API ESTADÍSTICAS DEL SUPERVISOR
        Route::prefix('supervisor/estadisticas')->name('supervisor.estadisticas.')->group(function () {
            Route::get('/generales', [EstadisticasSupervisorController::class, 'estadisticasGenerales'])
                ->name('generales');
            Route::get('/personal', [EstadisticasSupervisorController::class, 'estadisticasPersonal'])
                ->name('personal');
            Route::get('/personal/reporte-pdf', [EstadisticasSupervisorController::class, 'generarReportePersonal'])
                ->name('personal.reporte-pdf');
            Route::get('/personal/lista', [EstadisticasSupervisorController::class, 'listarPersonal'])
                ->name('personal.lista');
        });

        // API PERSONAL DE SALUD
        Route::prefix('personal-salud')->name('personal-salud.')->group(function () {
            Route::get('/', [PersonalSaludController::class, 'index'])->name('index');
            Route::get('/activos', [PersonalSaludController::class, 'activos'])->name('activos');
            Route::get('/{id}', [PersonalSaludController::class, 'show'])->name('show');
            Route::post('/', [PersonalSaludController::class, 'store'])->name('store');
            Route::put('/{id}', [PersonalSaludController::class, 'update'])->name('update');
            Route::patch('/{id}/cambiar-estado', [PersonalSaludController::class, 'cambiarEstado'])->name('cambiarEstado');
        });

        // API ROLES
        Route::get('/roles', [RolController::class, 'index'])->name('roles.index');

        // API CONSULTORIOS
       Route::prefix('consultorios')->name('consultorios.')->group(function () {
    Route::get('/', [ConsultorioController::class, 'index'])->name('index');
    Route::get('/disponibles', [ConsultorioController::class, 'disponibles'])->name('disponibles');
    Route::get('/{id}/verificar-disponibilidad', [ConsultorioController::class, 'verificarDisponibilidad'])->name('verificarDisponibilidad');
    Route::get('/{id}', [ConsultorioController::class, 'show'])->name('show');
    Route::post('/', [ConsultorioController::class, 'store'])->name('store');
    Route::put('/{id}', [ConsultorioController::class, 'update'])->name('update');
    Route::delete('/{id}', [ConsultorioController::class, 'destroy'])->name('destroy');
});

        // API ASIGNACIONES DE CONSULTORIO
        Route::prefix('asignaciones-consultorio')->name('asignaciones-consultorio.')->group(function () {
            Route::get('/', [AsignacionConsultorioController::class, 'index'])->name('index');
            Route::get('/activas', [AsignacionConsultorioController::class, 'activas'])->name('activas');
            Route::get('/personal/{codPer}', [AsignacionConsultorioController::class, 'porPersonal'])->name('porPersonal');
            Route::get('/consultorio/{codCons}', [AsignacionConsultorioController::class, 'porConsultorio'])->name('porConsultorio');
            Route::get('/{id}', [AsignacionConsultorioController::class, 'show'])->name('show');
            Route::post('/', [AsignacionConsultorioController::class, 'store'])->name('store');
            Route::put('/{id}', [AsignacionConsultorioController::class, 'update'])->name('update');
            Route::delete('/{id}', [AsignacionConsultorioController::class, 'destroy'])->name('destroy');
        });

        // API USUARIO ACTUAL
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
            // Estadísticas Home
            Route::get('/home/estadisticas', [HomeController::class, 'estadisticas']);

            // API DE PACIENTES
            Route::prefix('pacientes')->name('pacientes.')->group(function () {
                Route::get('/', [PacienteController::class, 'index'])->name('index');
                Route::post('/', [PacienteController::class, 'store'])->name('store');
                Route::get('/{id}', [PacienteController::class, 'show'])->name('show');
                Route::put('/{id}', [PacienteController::class, 'update'])->name('update');
                Route::delete('/{id}', [PacienteController::class, 'cambiarEstado'])->name('cambiarEstado');
            });

            // API DE REQUISITOS
            Route::prefix('requisitos')->name('requisitos.')->group(function () {
                Route::get('/', [App\Http\Controllers\RequisitoController::class, 'index'])->name('api.index');
                Route::post('/', [App\Http\Controllers\RequisitoController::class, 'store'])->name('api.store');
                Route::get('/{id}', [App\Http\Controllers\RequisitoController::class, 'show'])->name('api.show');
                Route::put('/{id}', [App\Http\Controllers\RequisitoController::class, 'update'])->name('api.update');
                Route::delete('/{id}', [App\Http\Controllers\RequisitoController::class, 'destroy'])->name('api.destroy');
            });

            // API DE TIPOS DE ESTUDIO
            Route::prefix('tipos-estudio')->name('tipos-estudio.')->group(function () {
                Route::get('/', [App\Http\Controllers\TipoEstudioController::class, 'index'])->name('api.index');
                Route::post('/con-requisitos', [App\Http\Controllers\TipoEstudioRequisitoController::class, 'crearConRequisitos'])->name('crearConRequisitos');
                Route::get('/exportar-pdf/{id}', [App\Http\Controllers\TipoEstudioController::class, 'exportarPDF'])->name('exportarPDF');
                Route::get('/{id}', [App\Http\Controllers\TipoEstudioController::class, 'show'])->name('api.show');
                Route::put('/{id}', [App\Http\Controllers\TipoEstudioController::class, 'update'])->name('api.update');
                Route::delete('/{id}', [App\Http\Controllers\TipoEstudioController::class, 'destroy'])->name('api.destroy');

                // Gestión de requisitos por tipo de estudio
                Route::prefix('{codTest}/requisitos')->group(function () {
                    Route::get('/', [App\Http\Controllers\TipoEstudioRequisitoController::class, 'listarRequisitos'])->name('requisitos.index');
                    Route::post('/asignar', [App\Http\Controllers\TipoEstudioRequisitoController::class, 'asignarRequisitos'])->name('requisitos.asignar');
                    Route::post('/agregar', [App\Http\Controllers\TipoEstudioRequisitoController::class, 'agregarRequisito'])->name('requisitos.agregar');
                    Route::put('/{codRequisito}/observacion', [App\Http\Controllers\TipoEstudioRequisitoController::class, 'actualizarObservacion'])->name('requisitos.actualizarObservacion');
                    Route::delete('/{codRequisito}', [App\Http\Controllers\TipoEstudioRequisitoController::class, 'eliminarRequisito'])->name('requisitos.eliminar');
                });
            });

            // API DE MÉDICOS
            Route::prefix('medicos')->name('medicos.')->group(function () {
                Route::get('/', [MedicoController::class, 'index'])->name('api.index');
                Route::post('/', [MedicoController::class, 'store'])->name('api.store');
                Route::get('/{id}', [MedicoController::class, 'show'])->name('api.show');
                Route::put('/{id}', [MedicoController::class, 'update'])->name('api.update');
                Route::delete('/{id}', [MedicoController::class, 'destroy'])->name('api.destroy');
            });

            // API DE CRONOGRAMAS
            Route::prefix('cronogramas')->name('cronogramas.')->group(function () {
                Route::get('/activos', [App\Http\Controllers\CronogramaAtencionController::class, 'activos'])->name('activos');
                Route::get('/entre-fechas', [App\Http\Controllers\CronogramaAtencionController::class, 'entreFechas'])->name('entreFechas');
                Route::get('/personal/{codPer}', [App\Http\Controllers\CronogramaAtencionController::class, 'porPersonal'])->name('porPersonal');

                Route::get('/', [App\Http\Controllers\CronogramaAtencionController::class, 'index'])->name('api.index');
                Route::post('/', [App\Http\Controllers\CronogramaAtencionController::class, 'store'])->name('api.store');
                Route::get('/{fecha}', [App\Http\Controllers\CronogramaAtencionController::class, 'show'])->name('api.show');
                Route::put('/{fecha}', [App\Http\Controllers\CronogramaAtencionController::class, 'update'])->name('api.update');
                Route::patch('/{fecha}/estado', [App\Http\Controllers\CronogramaAtencionController::class, 'cambiarEstado'])->name('cambiarEstado');
                Route::delete('/{fecha}', [App\Http\Controllers\CronogramaAtencionController::class, 'destroy'])->name('api.destroy');
            });

            // API DE SERVICIOS
            Route::prefix('servicios')->name('servicios.')->group(function () {
                Route::get('/calcular-ficha/{fechaCrono}', [ServicioController::class, 'calcularNumeroFicha']);
                Route::get('/datos-formulario', [ServicioController::class, 'datosFormulario'])->name('datosFormulario');
                Route::get('/estadisticas', [ServicioController::class, 'estadisticas'])->name('estadisticas');
                Route::get('/paciente/{codPa}', [ServicioController::class, 'porPaciente'])->name('porPaciente');
                Route::get('/estado/{estado}', [ServicioController::class, 'porEstado'])->name('porEstado');
                Route::get('/horarios-disponibles/{fechaCrono}', [ServicioController::class, 'horariosDisponibles']);
                Route::get('/por-fecha-cronograma/{fechaCrono}', [ServicioController::class, 'serviciosPorFechaCronograma']);
                Route::patch('/{id}/cambiar-horario', [ServicioController::class, 'cambiarHorario'])->name('cambiarHorario');

                Route::patch('/{id}/cancelar', [ServicioController::class, 'cancelar'])->name('cancelar');
                Route::patch('/{id}/entregar', [ServicioController::class, 'entregar'])->name('entregar');
                Route::patch('/{id}/estado', [ServicioController::class, 'cambiarEstado'])->name('cambiarEstado');

                Route::get('/', [ServicioController::class, 'index'])->name('api.index');
                Route::post('/', [ServicioController::class, 'store'])->name('api.store');
                Route::get('/{id}', [ServicioController::class, 'show'])->name('api.show');
                Route::put('/{id}', [ServicioController::class, 'update'])->name('api.update');
                Route::post('/{id}/diagnosticos', [ServicioController::class, 'asociarDiagnosticos'])->name('asociarDiagnosticos');
                Route::delete('/{id}', [ServicioController::class, 'destroy'])->name('api.destroy');
            });
        });
    });

    // Cerrar sesión
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Ruta fallback
Route::fallback(function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    if ($user->codRol == 1) {
        return redirect()->route('supervisor.home');
    } elseif ($user->codRol == 2) {
        return redirect()->route('personal.home');
    } elseif ($user->codRol == 3) {
        return redirect()->route('enfermera.home');
    }

    Auth::logout();
    return redirect()->route('login')->with('error', 'Rol no válido');
});
