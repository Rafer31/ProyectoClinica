<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'Servicio';
    protected $primaryKey = 'codServ';
    public $timestamps = false;

    protected $fillable = [
        'fechaSol',
        'horaSol',
        'nroServ',
        'fechaAten',
        'horaAten',
        'fechaEnt',
        'horaEnt',
        'tipoSeg',
        'nroFicha',
        'estado',
        'codPa',
        'codMed',
        'codTest',
        'fechaCrono'
    ];

    protected $casts = [
        'fechaSol' => 'date',
        'horaSol' => 'datetime',
        'fechaAten' => 'date',
        'horaAten' => 'datetime',
        'fechaEnt' => 'date',
        'horaEnt' => 'datetime',
        'tipoSeg' => 'string',
        'estado' => 'string'
    ];

    // Relación con Paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'codPa', 'codPa');
    }

    // Relación con Medico
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'codMed', 'codMed');
    }

    // Relación con TipoEstudio
    public function tipoEstudio()
    {
        return $this->belongsTo(TipoEstudio::class, 'codTest', 'codTest');
    }

    // Relación con CronogramaAtencion
    public function cronograma()
    {
        return $this->belongsTo(CronogramaAtencion::class, 'fechaCrono', 'fechaCrono');
    }

    // Relación muchos a muchos con Diagnostico
    public function diagnosticos()
    {
        return $this->belongsToMany(
            Diagnostico::class,
            'Servicio_Diagnostico',
            'codServ',
            'codDiag'
        )->withPivot('tipo');
    }

    // Relación con AsignacionConsultorio
    public function asignaciones()
    {
        return $this->hasMany(AsignacionConsultorio::class, 'codServ', 'codServ');
    }

    // Scopes útiles
    public function scopeProgramados($query)
    {
        return $query->where('estado', 'Programado');
    }

    public function scopeAtendidos($query)
    {
        return $query->where('estado', 'Atendido');
    }

    public function scopeEntregados($query)
    {
        return $query->where('estado', 'Entregado');
    }

    public function scopeEnProceso($query)
    {
        return $query->where('estado', 'EnProceso');
    }

    public function scopePorPaciente($query, $codPa)
    {
        return $query->where('codPa', $codPa);
    }

    public function scopePorMedico($query, $codMed)
    {
        return $query->where('codMed', $codMed);
    }

    public function scopePorTipoSeguro($query, $tipoSeg)
    {
        return $query->where('tipoSeg', $tipoSeg);
    }

    public function scopePorFechaSolicitud($query, $fecha)
    {
        return $query->whereDate('fechaSol', $fecha);
    }

    public function scopePorFechaAtencion($query, $fecha)
    {
        return $query->whereDate('fechaAten', $fecha);
    }
}
