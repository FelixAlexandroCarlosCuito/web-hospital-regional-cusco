<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function ticket(){
        return view('citas.ticket');
    }

    public function reservar_cita(){
        return view('citas.reservar-cita');
    }
    /**
     * Renderiza el formulario de reserva con los datos dinámicos de MySQL.
     */
    public function create()
    {
        // Extraemos las especialidades junto con los médicos que pertenecen a cada una
        $especialidades = Especialidad::with('medicos')->get();
        return view('citas.reservar-cita', compact('especialidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'especialidad_id'       => 'required|exists:especialidades,id',
            'medico_id'             => 'required|exists:medicos,id',
            'fecha'                 => 'required|date|after_or_equal:today',
            'hora'                  => 'required',
            'telefono'              => 'required|string|max:15',
            'codigo_referencia_sis' => 'nullable|string|max:50'
        ]);

        $usuario = \Illuminate\Support\Facades\Auth::user();

        // 1. Actualizamos el teléfono en el perfil del paciente (equivalente a actualizarlo en ventanilla)
        if ($usuario->telefono !== $request->telefono) {
            $usuario->update(['telefono' => $request->telefono]);
        }

        // 2. Registramos la cita médica con los datos formales
        $cita = \App\Models\Cita::create([
            'user_id'               => $usuario->id,
            'medico_id'             => $request->medico_id,
            'fecha'                 => $request->fecha,
            'hora'                  => $request->hora,
            'codigo_referencia_sis' => $request->codigo_referencia_sis,
            'estado'                => 'Pendiente'
        ]);

        return redirect()->route('ticket', ['id' => $cita->id]);
    }

    /**
     * Muestra el ticket generado para una cita específica.
     */
    public function showTicket($id)
    {
        // 1. Buscamos la cita. Usamos 'with' para traer los datos del médico y la especialidad.
        // failOrFail garantiza que si el ID no existe, arroja un 404 seguro.
        $cita = \App\Models\Cita::with(['medico.especialidad'])->findOrFail($id);

        // 2. Seguridad: Verificamos que el usuario actual sea el dueño de la cita.
        // Si alguien intenta cambiar el ID en la URL para ver el ticket de otro paciente, lo bloqueamos.
        if ($cita->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403, 'Acceso no autorizado a este ticket.');
        }

        // 3. Calculamos el "Orden" del paciente (Cuántos pacientes hay antes que él ese mismo día con ese médico)
        $ordenAtencion = \App\Models\Cita::where('medico_id', $cita->medico_id)
                                ->where('fecha', $cita->fecha)
                                ->where('id', '<=', $cita->id) // Solo contamos los que se registraron antes o igual que él
                                ->count();

        // 4. Calculamos el total de citas del médico para ese día
        $totalCitasDia = \App\Models\Cita::where('medico_id', $cita->medico_id)
                                ->where('fecha', $cita->fecha)
                                ->count();

        return view('citas.ticket', compact('cita', 'ordenAtencion', 'totalCitasDia'));
    }
}