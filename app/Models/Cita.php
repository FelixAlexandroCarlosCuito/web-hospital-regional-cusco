<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa una cita programada de consulta externa y sus reglas de vigencia y cancelación.
 */
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
     * Filtra citas que ocupan cupo efectivo (excluye canceladas).
     */
    public function scopeNoCanceladas(Builder $query): Builder
    {
        return $query->where('estado', '!=', 'Cancelado');
    }

    /**
     * Filtra por fecha exacta usando comparación sargable compatible con índices B-Tree sobre columna DATE.
     */
    public function scopeEnFecha(Builder $query, string $fechaIso): Builder
    {
        return $query->where('fecha', $fechaIso);
    }

    public function fechaHora(): Carbon
    {
        return Carbon::parse($this->fecha)->setTimeFromTimeString($this->hora);
    }

    public function puedeCancelarsePorPaciente(int $horasAnticipacion = self::HORAS_MINIMAS_CANCELACION): bool
    {
        if ($this->estado !== 'Pendiente') {
            return false;
        }

        return $this->fechaHora()->gte(now()->addHours($horasAnticipacion));
    }
}