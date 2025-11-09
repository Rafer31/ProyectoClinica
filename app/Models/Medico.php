<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;

    protected $table = 'Medico';
    protected $primaryKey = 'codMed';
    public $timestamps = false;

    protected $fillable = [
        'nomMed',
        'paternoMed',
        'tipoMed'
    ];

    // Relación con Servicio
    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'codMed', 'codMed');
    }

    // Accessor para obtener el nombre completo
    public function getNombreCompletoAttribute()
    {
        return trim("{$this->nomMed} {$this->paternoMed}");
    }

    // Scopes útiles
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipoMed', $tipo);
    }
}
