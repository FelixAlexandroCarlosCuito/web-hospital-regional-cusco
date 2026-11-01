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
    public function panel(Request $request)
    {
        $medico = Auth::user()->medico;

        if (!$medico) {
            abort(403, 'No se encontró un perfil clínico asociado a esta cuenta.');
        }

        // 1. Capturamos la fecha del filtro. Si no existe, usamos la fecha actual.
        $fechaFiltro = $request->input('fecha', now()->toDateString());

        // 2. Buscamos las citas usando la fecha dinámica
        $citasHoy = Cita::with('paciente')
                        ->where('medico_id', $medico->id)
                        ->where('fecha', $fechaFiltro)
                        ->orderBy('hora', 'asc')
                        ->get();

        // 3. Pasamos la variable $fechaFiltro a la vista para mantener el input actualizado
        return view('medico.panel', compact('citasHoy', 'medico', 'fechaFiltro'));
    }

    /**
     * Marca la cita de un paciente como 'Atendida'.
     */
    public function atender($id)
    {
        $cita = Cita::findOrFail($id);

        // Seguridad: El doctor solo puede atender sus propias citas, no las de otros médicos
        if ($cita->medico_id !== Auth::user()->medico->id) {
            abort(403, 'No tienes permiso para modificar las citas de otro consultorio.');
        }

        $cita->update(['estado' => 'Atendido']);

        return back(); // Recarga la agenda automáticamente
    }
}