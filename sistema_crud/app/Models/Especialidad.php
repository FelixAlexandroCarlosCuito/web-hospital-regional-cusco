<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    // Le decimos a Laravel el nombre exacto de la tabla en la BD
    protected $table = 'especialidades';

    // Permitimos que se pueda guardar el campo "nombre"
    protected $fillable = ['nombre'];

    // Relación: Una especialidad tiene muchos médicos
    public function medicos()
    {
        return $this->hasMany(Medico::class);
    }
}