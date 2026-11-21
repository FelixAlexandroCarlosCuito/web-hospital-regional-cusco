<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'especialidades'; 
    protected $fillable = [
        'nombre',
        'descripcion',
        'activo'
    ];

    public function medicos()
    {
        return $this->hasMany(Medico::class);
    }

    public function citas()
    {
        return $this->hasManyThrough(Cita::class, Medico::class);
    }
}