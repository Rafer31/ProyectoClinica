<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PersonalSalud extends Authenticatable
{
    use Notifiable;

    protected $table = 'PersonalSalud';
    protected $primaryKey = 'codPer';
    public $timestamps = false;

    protected $fillable = [
        'usuarioPer',
        'clavePer',
        'nomPer',
        'paternoPer',
        'maternoPer',
        'estado',
        'codRol',
    ];

    protected $hidden = [
        'clavePer',
    ];

    public function getAuthPassword()
    {
        return $this->clavePer;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'codRol', 'codRol');
    }
}
