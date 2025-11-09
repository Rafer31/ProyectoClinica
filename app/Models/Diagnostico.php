<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    use HasFactory;

    protected $table = 'Diagnostico';
    protected $primaryKey = 'codDiag';
    public $timestamps = false;

    protected $fillable = [
        'descripDiag'
    ];

    // Relación muchos a muchos con Servicio
    public function servicios()
    {
        return $this->belongsToMany(
            Servicio::class,
            'Servicio_Diagnostico',
            'codDiag',
            'codServ'
        )->withPivot('tipo');
    }
}
