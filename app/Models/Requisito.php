<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisito extends Model
{
    use HasFactory;

    protected $table = 'Requisito';
    protected $primaryKey = 'codRequisito';
    public $timestamps = false;

    protected $fillable = [
        'descripRequisito'
    ];

    // Relación muchos a muchos con TipoEstudio
    public function tiposEstudio()
    {
        return $this->belongsToMany(
            TipoEstudio::class,
            'TipoEstudio_Requisito',
            'codRequisito',
            'codTest'
        )->withPivot('observacion');
    }
}
