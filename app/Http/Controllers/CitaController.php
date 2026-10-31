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

    /**
     * Cancela una cita médica cambiando su estado, sin destruir el registro.
     */
    public function cancelar($id)
    {
        $cita = \App\Models\Cita::findOrFail($id);

        // Seguridad estricta: Solo el dueño de la cita puede cancelarla
        if ($cita->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        // Actualizamos el estado. Al no destruirla, pasará al Historial automáticamente.
        $cita->update(['estado' => 'Cancelado']);

        // Retornamos a la página anterior (el dashboard)
        return back();
    }

    /**
     * Devuelve en JSON los horarios disponibles para un médico en una fecha específica.
     */
    public function getHorarios(Request $request)
    {
        $request->validate([
            'medico_id' => 'required|exists:medicos,id',
            'fecha'     => 'required|date'
        ]);

        // 1. Definimos la jornada estándar del hospital (Intervalos de 30 min)
        $horariosBase = [
            '08:00:00', '08:30:00', '09:00:00', '09:30:00', '10:00:00',
            '10:30:00', '11:00:00', '11:30:00', '12:00:00', '12:30:00', '13:00:00'
        ];

        // 2. Buscamos todas las citas que YA están ocupadas para ese doctor en ese día.
        // Ignoramos las canceladas, porque esos cupos vuelven a estar libres.
        $citasOcupadas = \App\Models\Cita::where('medico_id', $request->medico_id)
                            ->where('fecha', $request->fecha)
                            ->where('estado', '!=', 'Cancelado')
                            ->pluck('hora')
                            ->toArray();

        // 3. Normalizamos el formato de hora que devuelve MySQL para evitar errores de comparación
        $citasOcupadas = array_map(function($hora) {
            return date('H:i:s', strtotime($hora));
        }, $citasOcupadas);

        // 4. Matemáticas de conjuntos: Horarios Base MENOS Horarios Ocupados
        $horariosDisponibles = array_values(array_diff($horariosBase, $citasOcupadas));

        return response()->json($horariosDisponibles);
    }
}