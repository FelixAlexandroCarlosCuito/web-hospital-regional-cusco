<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Representa una especialidad clínica de consulta externa y su relación con médicos y citas.
 */
class Especialidad extends Model
{
    protected $table = 'especialidades';

    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen',
        'activo',
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