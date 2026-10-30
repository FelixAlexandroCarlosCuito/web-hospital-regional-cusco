<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $fillable = ['user_id', 'medico_id', 'fecha', 'hora', 'estado'];

    // Relación con el paciente
    public function paciente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con el médico
    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }
}