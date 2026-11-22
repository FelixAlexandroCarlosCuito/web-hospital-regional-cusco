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

        // Pagina el historial para acotar el renderizado conservando acceso a constancias anteriores
        $historialCitas = Cita::with('medico.especialidad')
            ->where('user_id', $usuarioId)
            ->where(function ($query) {
                $query->where('estado', '!=', 'Pendiente')
                    ->orWhere('fecha', '<', now()->toDateString());
            })
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->paginate(10);

        return view('paciente.dashboard', compact('citasActivas', 'historialCitas'));
    }
}
