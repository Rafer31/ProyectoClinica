<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionConsultorio extends Model
{
    use HasFactory;

    protected $table = 'AsignacionConsultorio';
    protected $primaryKey = 'idAsignacion';
    public $timestamps = false;

    protected $fillable = [
        'fechaInicio',
        'fechaFin',
        'codPer',
        'codCons',
        'codServ'
    ];

    protected $casts = [
        'fechaInicio' => 'date',
        'fechaFin' => 'date'
    ];

    // Relación con PersonalSalud
    public function personal()
    {
        return $this->belongsTo(PersonalSalud::class, 'codPer', 'codPer');
    }

    // Relación con Consultorio
    public function consultorio()
    {
        return $this->belongsTo(Consultorio::class, 'codCons', 'codCons');
    }

    // Relación con Servicio
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'codServ', 'codServ');
    }

    // Scopes útiles
    public function scopePorPersonal($query, $codPer)
    {
        return $query->where('codPer', $codPer);
    }

    public function scopePorConsultorio($query, $codCons)
    {
        return $query->where('codCons', $codCons);
    }

    public function scopePorServicio($query, $codServ)
    {
        return $query->where('codServ', $codServ);
    }

    public function scopeActivas($query)
    {
        $hoy = now()->toDateString();
        return $query->where('fechaInicio', '<=', $hoy)
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fechaFin')
                    ->orWhere('fechaFin', '>=', $hoy);
            });
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fechaInicio', [$fechaInicio, $fechaFin])
            ->orWhereBetween('fechaFin', [$fechaInicio, $fechaFin]);
    }
}
