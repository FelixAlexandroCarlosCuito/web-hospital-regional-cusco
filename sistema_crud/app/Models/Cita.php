<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $fillable = [
        'paciente_nombre',
        'paciente_documento',
        'especialidad',
        'fecha',
        'hora',
        'monto_pagar',
        'estado'
    ];
}