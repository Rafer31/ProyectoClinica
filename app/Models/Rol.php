<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'rol';
    protected $primaryKey = 'codRol';
    public $timestamps = false;

    protected $fillable = [
        'nombreRol'
    ];

    public function personales()
    {
        return $this->hasMany(PersonalSalud::class, 'codRol', 'codRol');
    }
}
