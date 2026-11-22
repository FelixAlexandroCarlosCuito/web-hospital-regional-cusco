<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Representa las cuentas de pacientes y personal interno del sistema.
 * Los atributos de privilegio (rol y activo) quedan fuera de $fillable para impedir escalada por asignación masiva.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'direccion',
        'telefono',
        'correo',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function medico()
    {
        return $this->hasOne(Medico::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'user_id');
    }
}