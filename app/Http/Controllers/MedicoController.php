<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medico;
use App\Models\Cita;
use App\Models\HistorialClinico;
use Carbon\Carbon;

class MedicoController extends Controller
{
    // 1. Ver la agenda del médico
    public function dashboard($id)
    {
        $medico = Medico::with('especialidad')->findOrFail($id);
        
        // Busca las citas de este médico, que ya estén pagadas y sean de hoy en adelante
        $citas = Cita::where('medico_id', $id)
                     ->where('estado', 'pagado')
                     ->whereDate('fecha', '>=', Carbon::today())
                     ->orderBy('fecha', 'asc')
                     ->orderBy('hora', 'asc')
                     ->get();

        return view('medico.dashboard', compact('medico', 'citas'));
    }

    // 2. Ver la pantalla para llenar la receta/historial de un paciente
    public function atenderCita($idCita)
    {
        $cita = Cita::findOrFail($idCita);
        
        // Buscamos si este paciente ya tiene historial previo con este médico
        $historialPrevio = HistorialClinico::where('paciente_documento', $cita->paciente_documento)
                                            ->orderBy('created_at', 'desc')
                                            ->get();

        return view('medico.atender', compact('cita', 'historialPrevio'));
    }

    // 3. Guardar el diagnóstico y finalizar la cita
    public function guardarHistorial(Request $request, $idCita)
    {
        $request->validate([
            'diagnostico' => 'required',
            'tratamiento' => 'required'
        ]);

        $cita = Cita::findOrFail($idCita);

        // Guardar en la tabla historial_clinicos
        HistorialClinico::create([
            'cita_id' => $cita->id,
            'medico_id' => $cita->medico_id,
            'paciente_documento' => $cita->paciente_documento,
            'diagnostico' => $request->diagnostico,
            'tratamiento' => $request->tratamiento,
        ]);

        // Cambiamos el estado de la cita a "atendido"
        $cita->estado = 'atendido';
        $cita->save();

        return redirect()->route('medico.dashboard', $cita->medico_id)
                         ->with('exito', 'Historial clínico guardado. Paciente atendido.');
    }
}