<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEstudio extends Model
{
    use HasFactory;

    protected $table = 'TipoEstudio';
    protected $primaryKey = 'codTest';
    public $timestamps = false;

    protected $fillable = [
        'descripcion'
    ];

    // Relación con Servicio
    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'codTest', 'codTest');
    }

    // Relación muchos a muchos con Requisito
    public function requisitos()
    {
        return $this->belongsToMany(
            Requisito::class,
            'TipoEstudio_Requisito',
            'codTest',
            'codRequisito'
        )->withPivot('observacion');
    }
}
