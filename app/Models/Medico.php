<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    protected $fillable = [
        'user_id',
        'especialidad_id', 
        'nombres', 
        'apellidos', 
        'consultorio'
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