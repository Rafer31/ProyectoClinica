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
        'tipoAseg',
        'nroFicha',
        'estado',
        'codPa',
        'codMed',
        'codTest',
        'horaCrono',  // NUEVO CAMPO
        'fechaCrono'
    ];

    protected $casts = [
        'fechaSol' => 'date',
        'fechaAten' => 'date',
        'fechaEnt' => 'date',
    ];

    // Relaciones
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'codPa', 'codPa');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'codMed', 'codMed');
    }

    public function tipoEstudio()
    {
        return $this->belongsTo(TipoEstudio::class, 'codTest', 'codTest');
    }

    public function cronograma()
    {
        return $this->belongsTo(CronogramaAtencion::class, 'fechaCrono', 'fechaCrono');
    }

    public function diagnosticos()
    {
        return $this->belongsToMany(
            Diagnostico::class,
            'Servicio_Diagnostico',
            'codServ',
            'codDiag'
        )->withPivot('tipo');
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionConsultorio::class, 'codServ', 'codServ');
    }

    // Scopes
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
}
