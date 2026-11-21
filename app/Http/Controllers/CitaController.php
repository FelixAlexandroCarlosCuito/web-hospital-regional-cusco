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

    public function create()
    {
        $especialidades = Especialidad::where('activo', true)
            ->with(['medicos' => function ($query) {
                $query->where('activo', true);
            }])
            ->orderBy('nombre')
            ->get();

        $hoy = now()->toDateString();

        $citasPendientesTotal = Cita::where('user_id', Auth::id())
            ->where('estado', 'Pendiente')
            ->whereDate('fecha', '>=', $hoy)
            ->count();

        // Calificación explícita con prefijo de tabla para evitar ambigüedad con medicos.user_id en el JOIN
        $especialidadesOcupadas = Cita::where('citas.user_id', Auth::id())
            ->where('citas.estado', 'Pendiente')
            ->whereDate('citas.fecha', '>=', $hoy)
            ->join('medicos', 'citas.medico_id', '=', 'medicos.id')
            ->pluck('medicos.especialidad_id')
            ->unique()
            ->toArray();

        return view('citas.reservar-cita', compact(
            'especialidades',
            'citasPendientesTotal',
            'especialidadesOcupadas'
        ));
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

        $hoy = now()->toDateString();
        $limiteGlobal = 3;

        // Bloqueo pesimista sobre usuario y médico para evitar reservas duplicadas concurrentes
        return DB::transaction(function () use ($request, $horaNormalizada, $hoy, $limiteGlobal) {
            $usuario = User::where('id', Auth::id())->lockForUpdate()->firstOrFail();
            $medico = Medico::with('especialidad')->where('id', $request->medico_id)->lockForUpdate()->firstOrFail();

            if ((int) $medico->especialidad_id !== (int) $request->especialidad_id) {
                return back()
                    ->withErrors(['medico_id' => 'El médico seleccionado no corresponde a la especialidad indicada.'])
                    ->withInput();
            }

            if (!$medico->activo || !$medico->especialidad || !$medico->especialidad->activo) {
                return back()
                    ->withErrors(['medico_id' => 'La especialidad o el médico seleccionado no se encuentra disponible actualmente.'])
                    ->withInput();
            }

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

            $citasPendientesTotal = Cita::where('user_id', $usuario->id)
                ->where('estado', 'Pendiente')
                ->whereDate('fecha', '>=', $hoy)
                ->count();

            if ($citasPendientesTotal >= $limiteGlobal) {
                return back()
                    ->withErrors(['limite' => "Has alcanzado el máximo permitido de {$limiteGlobal} citas pendientes activas."])
                    ->withInput();
            }

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
    }

    public function getHorarios(Request $request)
    {
        $request->validate([
            'medico_id' => 'required|exists:medicos,id',
            'fecha'     => 'required|date',
        ]);

        $medicoActivo = Medico::where('id', $request->medico_id)
            ->where('activo', true)
            ->whereHas('especialidad', fn ($q) => $q->where('activo', true))
            ->exists();

        if (!$medicoActivo) {
            return response()->json([]);
        }

        $horariosBase = [
            '08:00:00', '08:30:00', '09:00:00', '09:30:00', '10:00:00',
            '10:30:00', '11:00:00', '11:30:00', '12:00:00', '12:30:00', '13:00:00',
        ];

        $citasOcupadas = Cita::where('medico_id', $request->medico_id)
            ->where('fecha', $request->fecha)
            ->where('estado', '!=', 'Cancelado')
            ->pluck('hora')
            ->map(fn ($hora) => date('H:i:s', strtotime($hora)))
            ->toArray();

        $horariosDisponibles = array_values(array_diff($horariosBase, $citasOcupadas));

        // Excluye turnos cuya hora ya transcurrió si la fecha consultada es hoy
        if (Carbon::parse($request->fecha)->isToday()) {
            $ahora = now();
            $horariosDisponibles = array_values(array_filter($horariosDisponibles, function ($hora) use ($request, $ahora) {
                return Carbon::parse("{$request->fecha} {$hora}")->gt($ahora);
            }));
        }

        return response()->json($horariosDisponibles);
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
}