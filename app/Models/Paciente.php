<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'Paciente';
    protected $primaryKey = 'codPa';
    public $timestamps = false;

    protected $fillable = [
        'nomPa',
        'paternoPa',
        'maternoPa',
        'estado',
        'fechaNac',
        'sexo',
        'nroHCI',
        'tipoPac'
    ];

    protected $casts = [
        'fechaNac' => 'datetime',
        'sexo' => 'string',
        'tipoPac' => 'string'
    ];

    // Relación con Servicio (un paciente puede tener muchos servicios)
    /*   public function servicios()
    {
        return $this->hasMany(Servicio::class, 'codPa', 'codPa');
    }
 */
    // Accessor para obtener el nombre completo
    public function getNombreCompletoAttribute()
    {
        return trim("{$this->nomPa} {$this->paternoPa} {$this->maternoPa}");
    }

    // Accessor para obtener la edad
    public function getEdadAttribute()
    {
        if (!$this->fechaNac) {
            return null;
        }
        return \Carbon\Carbon::parse($this->fechaNac)->age;
    }

    // Scopes útiles
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeInactivos($query)
    {
        return $query->where('estado', 'inactivo');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipoPac', $tipo);
    }

    public function scopePorSexo($query, $sexo)
    {
        return $query->where('sexo', $sexo);
    }
}
