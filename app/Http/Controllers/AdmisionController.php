<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Medico;

class AdmisionController extends Controller
{
    public function panel(Request $request)
    {
        // Traemos todos los médicos (con su especialidad) para el menú desplegable
        $medicos = Medico::with('especialidad')->get();

        // Parámetros de búsqueda enviados por el personal de ventanilla
        $medicoFiltro = $request->input('medico_id');
        $fechaFiltro = $request->input('fecha', now()->toDateString()); // Hoy por defecto

        // Si seleccionaron un médico, buscamos su agenda. Si no, devolvemos una colección vacía.
        if ($medicoFiltro) {
            $citas = Cita::with(['paciente', 'medico.especialidad'])
                        ->where('medico_id', $medicoFiltro)
                        ->where('fecha', $fechaFiltro)
                        ->orderBy('hora', 'asc')
                        ->get();
        } else {
            $citas = collect(); // Vacío inicial
        }

        return view('admision.panel', compact('medicos', 'citas', 'medicoFiltro', 'fechaFiltro'));
    }
}