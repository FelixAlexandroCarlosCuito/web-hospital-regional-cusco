<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    public const HORAS_MINIMAS_CANCELACION = 12;

    protected $fillable = [
        'user_id',
        'medico_id',
        'fecha',
        'hora',
        'codigo_referencia_sis',
        'estado',
    ];

    public function paciente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }

    /**
     * Combina fecha y hora de la cita en una sola instancia Carbon.
     */
    public function fechaHora(): Carbon
    {
        return Carbon::parse($this->fecha)->setTimeFromTimeString($this->hora);
    }

    /**
     * Determina si el paciente aún está dentro del plazo permitido para cancelar.
     */
    public function puedeCancelarsePorPaciente(int $horasAnticipacion = self::HORAS_MINIMAS_CANCELACION): bool
    {
        if ($this->estado !== 'Pendiente') {
            return false;
        }

        return $this->fechaHora()->gte(now()->addHours($horasAnticipacion));
    }
}