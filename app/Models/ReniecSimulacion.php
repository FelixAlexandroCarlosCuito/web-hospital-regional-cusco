<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReniecSimulacion extends Model
{
    protected $fillable = [
        'dni', 
        'digito_verificador', 
        'fecha_emision', 
        'nombres', 
        'apellido_paterno', 
        'apellido_materno'
    ];
}