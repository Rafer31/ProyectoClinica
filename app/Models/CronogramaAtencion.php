<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CronogramaAtencion extends Model
{
    use HasFactory;

    protected $table = 'CronogramaAtencion';
    protected $primaryKey = 'fechaCrono';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'fechaCrono',
        'cantDispo',
        'cantFijo',
        'estado',
        'codPer'
    ];

    protected $casts = [
        'cantDispo' => 'integer',
        'cantFijo' => 'integer',
    ];

    // NO usar cast de 'date' para fechaCrono porque causa problemas con timezone
    // Laravel lo manejará como string en formato Y-m-d

    /**
     * Relación con PersonalSalud (quien creó el cronograma)
     */
    public function personal()
    {
        return $this->belongsTo(PersonalSalud::class, 'codPer', 'codPer');
    }

    /**
     * Relación con Servicio
     */
    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'fechaCrono', 'fechaCrono');
    }

    /**
     * Scopes útiles
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeInactivosPasados($query)
    {
        return $query->where('estado', 'inactivoPas');
    }

    public function scopeInactivosFuturos($query)
    {
        return $query->where('estado', 'inactivoFut');
    }

    public function scopePorPersonal($query, $codPer)
    {
        return $query->where('codPer', $codPer);
    }

    public function scopePorFecha($query, $fecha)
    {
        return $query->where('fechaCrono', $fecha);
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fechaCrono', [$fechaInicio, $fechaFin]);
    }
}
