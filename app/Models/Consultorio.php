<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultorio extends Model
{
    use HasFactory;

    protected $table = 'Consultorio';
    protected $primaryKey = 'codCons';
    public $timestamps = false;

    protected $fillable = [
        'numCons'
    ];

    // Relación con AsignacionConsultorio
    public function asignaciones()
    {
        return $this->hasMany(AsignacionConsultorio::class, 'codCons', 'codCons');
    }
}
