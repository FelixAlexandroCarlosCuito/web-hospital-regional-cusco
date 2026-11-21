<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Cita;
use App\Models\Medico;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        $hoy = now()->toDateString();

        $citasPendientesTotal = Cita::where('user_id', Auth::id())
            ->where('estado', 'Pendiente')
            ->whereDate('fecha', '>=', $hoy)
            ->count();

        $especialidadesOcupadas = Cita::where('user_id', Auth::id())
            ->where('estado', 'Pendiente')
            ->whereDate('fecha', '>=', $hoy)
            ->join('medicos', 'citas.medico_id', '=', 'medicos.id')
            ->pluck('medicos.especialidad_id')
            ->unique()
            ->toArray();

        return view('reservar-cita', compact('especialidades', 'citasPendientesTotal', 'especialidadesOcupadas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'especialidad_id'       => 'required|exists:especialidades,id',
            'medico_id'             => 'required|exists:medicos,id',
            'fecha'                 => 'required|date|after_or_equal:today',
            'hora'                  => 'required|string',
            'telefono'              => 'required|string|max:15',
            'codigo_referencia_sis' => 'nullable|string|max:50',
        ]);

        $horaNormalizada = Carbon::parse($request->hora)->format('H:i:s');
        $hoy = now()->toDateString();
        $limiteGlobal = 3;

        // Transacción con bloqueo pesimista para evitar condiciones de carrera
        // (doble clic del mismo paciente o dos pacientes tomando el mismo cupo a la vez)
        return DB::transaction(function () use ($request, $horaNormalizada, $hoy, $limiteGlobal) {
            $usuario = User::where('id', Auth::id())->lockForUpdate()->firstOrFail();
            $medico = Medico::where('id', $request->medico_id)->lockForUpdate()->firstOrFail();

            // 1. El médico debe pertenecer a la especialidad enviada
            if ((int) $medico->especialidad_id !== (int) $request->especialidad_id) {
                return back()
                    ->withErrors(['medico_id' => 'El médico seleccionado no corresponde a la especialidad indicada.'])
                    ->withInput();
            }

            // 2. Disponibilidad real del cupo (evita doble reserva en el mismo médico)
            $cupoOcupado = Cita::where('medico_id', $medico->id)
                ->where('fecha', $request->fecha)
                ->where('hora', $horaNormalizada)
                ->where('estado', 'Pendiente')
                ->exists();

            if ($cupoOcupado) {
                return back()
                    ->withErrors(['hora' => 'Ese horario acaba de ser ocupado. Por favor, seleccione otro turno.'])
                    ->withInput();
            }

            // 3. Límite global de citas pendientes vigentes por paciente
            $citasPendientesTotal = Cita::where('user_id', $usuario->id)
                ->where('estado', 'Pendiente')
                ->whereDate('fecha', '>=', $hoy)
                ->count();

            if ($citasPendientesTotal >= $limiteGlobal) {
                return back()
                    ->withErrors(['limite' => "Has alcanzado el máximo permitido de {$limiteGlobal} citas pendientes activas."])
                    ->withInput();
            }

            // 4. Límite por especialidad (máximo 1 cita pendiente vigente por especialidad)
            $tieneEnEspecialidad = Cita::where('user_id', $usuario->id)
                ->where('estado', 'Pendiente')
                ->whereDate('fecha', '>=', $hoy)
                ->whereHas('medico', function ($query) use ($medico) {
                    $query->where('especialidad_id', $medico->especialidad_id);
                })
                ->exists();

            if ($tieneEnEspecialidad) {
                return back()
                    ->withErrors(['especialidad_id' => 'Ya cuentas con una cita pendiente en esta especialidad.'])
                    ->withInput();
            }

            // 5. Prevención de cruce horario del mismo paciente en otro consultorio
            $tieneCruceHorario = Cita::where('user_id', $usuario->id)
                ->where('estado', 'Pendiente')
                ->where('fecha', $request->fecha)
                ->where('hora', $horaNormalizada)
                ->exists();

            if ($tieneCruceHorario) {
                return back()
                    ->withErrors(['hora' => 'Ya tienes otra cita programada en esa misma fecha y hora.'])
                    ->withInput();
            }

            // Si pasó todas las reglas, ejecutamos las escrituras
            if ($usuario->telefono !== $request->telefono) {
                $usuario->update(['telefono' => $request->telefono]);
            }

            $cita = Cita::create([
                'user_id'               => $usuario->id,
                'medico_id'             => $medico->id,
                'fecha'                 => $request->fecha,
                'hora'                  => $horaNormalizada,
                'codigo_referencia_sis' => $request->codigo_referencia_sis,
                'estado'                => 'Pendiente',
            ]);

            return redirect()->route('ticket', ['id' => $cita->id]);
        });

        $fechaHoraCita = Carbon::parse("{$request->fecha} {$horaNormalizada}");

        if ($fechaHoraCita->isWeekend()) {
            return back()
                ->withErrors(['fecha' => 'La atención de citas es únicamente de lunes a viernes.'])
                ->withInput();
        }

        if ($fechaHoraCita->isPast()) {
            return back()
                ->withErrors(['hora' => 'No puedes reservar un horario que ya pasó.'])
                ->withInput();
        }
    }

    /**
     * Muestra el ticket generado para una cita específica.
     */
    public function showTicket($id)
    {
        // 1. Buscamos la cita. Cargamos las relaciones 'medico' y 'paciente' en la misma consulta.
        $cita = Cita::with(['medico.especialidad', 'paciente'])->findOrFail($id);

        // 2. Seguridad (RBAC): Verificamos el acceso.
        // Si el rol es 'paciente', estrictamente debe ser el dueño de la cita.
        // Los roles administrativos ('admision', 'medico') eluden este bloqueo por jerarquía.
        $usuarioActual = Auth::user();
        if ($usuarioActual->rol === 'paciente' && $cita->user_id !== $usuarioActual->id) {
            abort(403, 'Acceso denegado: No tiene permisos para visualizar este ticket.');
        }

        // 3. Calculamos el "Orden" del paciente
        $ordenAtencion = Cita::where('medico_id', $cita->medico_id)
                                ->where('fecha', $cita->fecha)
                                ->where('id', '<=', $cita->id) 
                                ->count();

        // 4. Calculamos el total de citas del médico para ese día
        $totalCitasDia = Cita::where('medico_id', $cita->medico_id)
                                ->where('fecha', $cita->fecha)
                                ->count();

        return view('citas.ticket', compact('cita', 'ordenAtencion', 'totalCitasDia'));
    }
    
    /**
     * Cancela una cita médica cambiando su estado, sin destruir el registro.
     */
    public function cancelar($id)
    {
        $cita = Cita::findOrFail($id);

        // 1. Seguridad estricta (Anti-IDOR)
        if ($cita->user_id !== Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        // 2. Integridad de estados: no permitir cancelar citas ya atendidas o canceladas
        if ($cita->estado !== 'Pendiente') {
            return back()->with('error', 'Solo se pueden cancelar citas que se encuentren en estado Pendiente.');
        }

        // 3. Candado temporal (mínimo 12 horas de anticipación)
        if (!$cita->puedeCancelarsePorPaciente()) {
            $horas = Cita::HORAS_MINIMAS_CANCELACION;
            return back()->with('error', "No puedes cancelar esta cita porque faltan menos de {$horas} horas para tu atención. Si tienes un imprevisto, comunícate con Admisión.");
        }

        // Baja lógica para preservar trazabilidad en el historial
        $cita->update(['estado' => 'Cancelado']);

        return back()->with('success', 'Tu cita ha sido cancelada correctamente y el cupo fue liberado.');
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
        $citasOcupadas = Cita::where('medico_id', $request->medico_id)
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