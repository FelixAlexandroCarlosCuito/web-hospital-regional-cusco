<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PacienteController extends Controller
{
    public function dashboard()
    {
        $usuarioId = Auth::id();

        // 1. Citas Activas: Estado pendiente y fecha igual o mayor a hoy
        $citasActivas = Cita::with('medico.especialidad')
            ->where('user_id', $usuarioId)
            ->where('estado', 'Pendiente')
            ->where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->get();

        // 2. Historial: Estado diferente a pendiente, o citas que ya pasaron
        $historialCitas = Cita::with('medico.especialidad')
            ->where('user_id', $usuarioId)
            ->where(function($query) {
                $query->where('estado', '!=', 'Pendiente')
                      ->orWhere('fecha', '<', now()->toDateString());
            })
            ->orderBy('fecha', 'desc')
            ->get();

        return view('paciente.dashboard', compact('citasActivas', 'historialCitas'));
    }
}
