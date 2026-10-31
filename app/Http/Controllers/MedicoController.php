<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cita;

class MedicoController extends Controller
{
    /**
     * Renderiza el panel principal del médico con su agenda del día.
     */
    public function panel()
    {
        // Extraemos el perfil médico asociado a esta cuenta de usuario
        $medico = Auth::user()->medico;

        // Validamos de forma paranoica que el usuario realmente tenga un perfil médico asignado
        if (!$medico) {
            abort(403, 'No se encontró un perfil clínico asociado a esta cuenta.');
        }

        // Consultamos las citas DE HOY, ordenadas por hora. 
        // Traemos los datos del paciente para que el doctor sepa a quién atiende.
        $citasHoy = Cita::with('paciente')
                        ->where('medico_id', $medico->id)
                        ->where('fecha', now()->toDateString())
                        ->orderBy('hora', 'asc')
                        ->get();

        return view('medico.panel', compact('citasHoy', 'medico'));
    }
}