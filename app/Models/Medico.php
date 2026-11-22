<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Representa el perfil clínico de un médico vinculado a su cuenta de usuario y especialidad.
 */
class Medico extends Model
{
    protected $fillable = [
        'user_id',
        'especialidad_id',
        'nombres',
        'apellidos',
        'consultorio',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}