<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Support\Facades\Auth;

/**
 * Renderiza el portal del paciente con sus citas activas e historial paginado.
 */
class PacienteController extends Controller
{
    public function dashboard()
    {
        $usuarioId = Auth::id();
        $hoy = now()->toDateString();

        $relaciones = [
            'medico:id,especialidad_id,nombres,apellidos,consultorio',
            'medico.especialidad:id,nombre',
        ];

        $citasActivas = Cita::select(['id', 'user_id', 'medico_id', 'fecha', 'hora', 'estado', 'codigo_referencia_sis'])
            ->with($relaciones)
            ->where('user_id', $usuarioId)
            ->where('estado', 'Pendiente')
            ->where('fecha', '>=', $hoy)
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->get();

        $historialCitas = Cita::select(['id', 'user_id', 'medico_id', 'fecha', 'hora', 'estado', 'codigo_referencia_sis'])
            ->with($relaciones)
            ->where('user_id', $usuarioId)
            ->where(function ($query) use ($hoy) {
                $query->where('estado', '!=', 'Pendiente')
                    ->orWhere('fecha', '<', $hoy);
            })
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->paginate(10);

        return view('paciente.dashboard', compact('citasActivas', 'historialCitas'));
    }
}