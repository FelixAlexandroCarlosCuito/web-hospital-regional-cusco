<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    // Forzamos el nombre de la tabla
    protected $table = 'medicos';

    // Permitimos guardar estos campos
    protected $fillable = ['especialidad_id', 'nombre', 'horario_inicio', 'horario_fin'];

    // Relación: Un médico pertenece a una especialidad
    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }
}