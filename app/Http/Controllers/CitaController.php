<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

/**
 * Gestiona el flujo de reserva con asignación automática de médico, constancia digital,
 * verificación mediante enlace firmado y cancelación operativa.
 */
class CitaController extends Controller
{
    private const BLOQUES_HORARIOS = [
        '08:00', '08:30', '09:00', '09:30', '10:00',
        '10:30', '11:00', '11:30', '12:00', '12:30',
    ];

    public function create(Request $request)
    {
        $usuario = Auth::user();

        $especialidades = Especialidad::where('activo', true)
            ->select(['id', 'nombre', 'descripcion'])
            ->withExists([
                'medicos as tiene_turnos' => fn ($q) => $q->where('activo', true),
            ])
            ->orderBy('nombre')
            ->get();

        // La especialidad se resuelve por relación porque citas no almacena especialidad_id (3NF)
        $citasPendientes = Cita::where('user_id', $usuario->id)
            ->where('estado', 'Pendiente')
            ->with('medico:id,especialidad_id')
            ->get(['id', 'medico_id']);

        $citasPendientesTotal = $citasPendientes->count();
        $especialidadesOcupadas = $citasPendientes
            ->pluck('medico.especialidad_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
        $especialidadPreseleccionada = (int) $request->query('especialidad', 0);

        $datosReserva = [
            'especialidades' => $especialidades->map(fn ($esp) => [
                'id' => $esp->id,
                'nombre' => $esp->nombre,
                'descripcion' => $esp->descripcion ?: 'Atención ambulatoria especializada en consulta externa previa reserva de cita.',
                'tiene_turnos' => (bool) $esp->tiene_turnos,
            ])->values(),
            'paciente' => [
                'nombres' => $usuario->nombres,
                'apellidos' => trim("{$usuario->apellido_paterno} {$usuario->apellido_materno}"),
                'documento' => trim("{$usuario->tipo_documento} {$usuario->numero_documento}"),
            ],
            'urlHorarios' => route('api.horarios.disponibles'),
            'hoy' => now()->toDateString(),
        ];

        return view('citas.reservar-cita', compact(
            'especialidades',
            'especialidadesOcupadas',
            'citasPendientesTotal',
            'especialidadPreseleccionada',
            'datosReserva'
        ));
    }

    public function horariosDisponibles(Request $request)
    {
        $datos = $request->validate([
            'especialidad_id' => ['required', 'integer', 'exists:especialidades,id'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $fecha = Carbon::parse($datos['fecha']);
        $fechaIso = $fecha->toDateString();

        if ($fecha->isWeekend()) {
            return response()->json([
                'horarios' => [],
                'mensaje' => 'Los consultorios externos atienden únicamente de lunes a viernes.',
            ]);
        }

        $medicosIds = Medico::where('especialidad_id', $datos['especialidad_id'])
            ->where('activo', true)
            ->pluck('id');

        if ($medicosIds->isEmpty()) {
            return response()->json([
                'horarios' => [],
                'mensaje' => 'Esta especialidad no cuenta con turnos habilitados temporalmente.',
            ]);
        }

        $totalMedicos = $medicosIds->count();

        // Usa pluck sobre índice cubriente sin hidratar modelos Cita completos
        $ocupacionPorHora = Cita::whereIn('medico_id', $medicosIds)
            ->enFecha($fechaIso)
            ->noCanceladas()
            ->pluck('hora')
            ->map(fn ($hora) => substr((string) $hora, 0, 5))
            ->countBy();

        $esHoy = $fecha->isToday();
        $horaActual = now()->format('H:i');

        $horariosDisponibles = [];
        foreach (self::BLOQUES_HORARIOS as $bloque) {
            if ($esHoy && $bloque <= $horaActual) {
                continue;
            }

            if ($ocupacionPorHora->get($bloque, 0) < $totalMedicos) {
                $horariosDisponibles[] = $bloque;
            }
        }

        return response()->json([
            'horarios' => $horariosDisponibles,
            'mensaje' => empty($horariosDisponibles)
                ? 'No quedan cupos disponibles para la fecha seleccionada. Pruebe con otro día hábil.'
                : null,
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'especialidad_id' => ['required', 'integer', 'exists:especialidades,id'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'in:' . implode(',', self::BLOQUES_HORARIOS)],
            'telefono' => ['required', 'regex:/^9\d{8}$/'],
            'codigo_referencia_sis' => ['nullable', 'string', 'max:50'],
        ], [
            'especialidad_id.required' => 'Seleccione una especialidad.',
            'fecha.after_or_equal' => 'La fecha de la cita no puede ser anterior a hoy.',
            'hora.in' => 'Seleccione un horario válido de consulta externa.',
            'telefono.regex' => 'Ingrese un número de celular válido de 9 dígitos que inicie con 9.',
        ]);

        $fecha = Carbon::parse($datos['fecha']);
        $fechaIso = $fecha->toDateString();

        if ($fecha->isWeekend()) {
            return back()->withInput()->withErrors(['fecha' => 'Solo se programan citas de lunes a viernes.']);
        }

        if ($fecha->isToday() && $datos['hora'] <= now()->format('H:i')) {
            return back()->withInput()->withErrors(['hora' => 'El horario seleccionado ya transcurrió. Elija otro turno.']);
        }

        $usuario = Auth::user();
        $horaSql = $datos['hora'] . ':00';

        $resultado = DB::transaction(function () use ($usuario, $datos, $fechaIso, $horaSql) {
            // Serializa solicitudes concurrentes del mismo paciente para impedir evasión de límites de reserva
            User::whereKey($usuario->id)->lockForUpdate()->first(['id']);
            $pendientesTotal = Cita::where('user_id', $usuario->id)
                ->where('estado', 'Pendiente')
                ->count();

            if ($pendientesTotal >= 3) {
                return ['error' => 'Ya alcanzaste el límite máximo de 3 citas pendientes activas.'];
            }

            $duplicadaEspecialidad = Cita::where('user_id', $usuario->id)
                ->where('estado', 'Pendiente')
                ->whereHas('medico', fn ($q) => $q->where('especialidad_id', $datos['especialidad_id']))
                ->exists();

            if ($duplicadaEspecialidad) {
                return ['error' => 'Ya cuentas con una cita pendiente en esta especialidad.'];
            }

            $crucePaciente = Cita::where('user_id', $usuario->id)
                ->enFecha($fechaIso)
                ->where('hora', $horaSql)
                ->noCanceladas()
                ->exists();

            if ($crucePaciente) {
                return ['error' => 'Ya tienes otra cita programada en esa misma fecha y hora.'];
            }

            // Bloqueo pesimista apoyado en índices compuestos para evitar sobreventa de cupos por concurrencia
            $medicoAsignado = Medico::where('especialidad_id', $datos['especialidad_id'])
                ->where('activo', true)
                ->whereDoesntHave('citas', function ($q) use ($fechaIso, $horaSql) {
                    $q->where('fecha', $fechaIso)
                        ->where('hora', $horaSql)
                        ->where('estado', '!=', 'Cancelado');
                })
                ->withCount(['citas as carga_dia' => function ($q) use ($fechaIso) {
                    $q->where('fecha', $fechaIso)
                        ->where('estado', '!=', 'Cancelado');
                }])
                ->orderBy('carga_dia')
                ->orderBy('id')
                ->lockForUpdate()
                ->first(['id', 'especialidad_id']);

            if (!$medicoAsignado) {
                return ['error' => 'El cupo para ese horario acaba de ocuparse. Seleccione otra hora disponible.'];
            }

            if ($usuario->telefono !== $datos['telefono']) {
                $usuario->update(['telefono' => $datos['telefono']]);
            }

            $cita = Cita::create([
                'user_id' => $usuario->id,
                'medico_id' => $medicoAsignado->id,
                'fecha' => $fechaIso,
                'hora' => $horaSql,
                'estado' => 'Pendiente',
                'codigo_referencia_sis' => !empty($datos['codigo_referencia_sis'])
                    ? trim($datos['codigo_referencia_sis'])
                    : null,
            ]);

            return ['cita' => $cita];
        });

        if (isset($resultado['error'])) {
            return back()->withInput()->withErrors(['reserva' => $resultado['error']]);
        }

        return redirect()
            ->action([self::class, 'showTicket'], [$resultado['cita']->id])
            ->with('success', 'Cita reservada correctamente. Se asignó automáticamente al especialista disponible.');
    }

    public function showTicket($id)
    {
        $cita = Cita::with([
            'medico:id,especialidad_id,nombres,apellidos,consultorio',
            'medico.especialidad:id,nombre',
            'paciente:id,tipo_documento,numero_documento,nombres,apellido_paterno,apellido_materno',
        ])->findOrFail($id);

        $usuarioActual = Auth::user();
        if ($usuarioActual->rol === 'paciente' && (int) $cita->user_id !== (int) $usuarioActual->id) {
            abort(403, 'Acceso denegado: No tiene permisos para visualizar este ticket.');
        }

        [$ordenAtencion, $totalCitasDia] = $this->calcularOrdenJornada($cita);
        $urlVerificacion = URL::signedRoute('citas.verificar', ['cita' => $cita->id]);

        return view('citas.ticket', compact('cita', 'ordenAtencion', 'totalCitasDia', 'urlVerificacion'));
    }

    public function verificarTicket($cita)
    {
        $registro = Cita::with([
            'medico:id,especialidad_id,nombres,apellidos,consultorio',
            'medico.especialidad:id,nombre',
            'paciente:id,tipo_documento,numero_documento,nombres,apellido_paterno,apellido_materno',
        ])->findOrFail($cita);

        [$ordenAtencion, $totalCitasDia] = $this->calcularOrdenJornada($registro);

        $documento = (string) ($registro->paciente->numero_documento ?? '');
        $esPersonalInterno = Auth::check() && Auth::user()->activo && in_array(Auth::user()->rol, ['admin', 'admision', 'medico'], true);
        $documentoVisible = $esPersonalInterno || strlen($documento) <= 4
            ? $documento
            : str_repeat('•', strlen($documento) - 4) . substr($documento, -4);

        return view('citas.verificar', [
            'cita' => $registro,
            'ordenAtencion' => $ordenAtencion,
            'totalCitasDia' => $totalCitasDia,
            'documentoVisible' => $documentoVisible,
            'esPersonalInterno' => $esPersonalInterno,
        ]);
    }

    public function cancelar($id)
    {
        $cita = Cita::findOrFail($id);

        if ((int) $cita->user_id !== (int) Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        if ($cita->estado !== 'Pendiente') {
            return back()->with('error', 'Solo se pueden cancelar citas que se encuentren en estado Pendiente.');
        }

        if (!$cita->puedeCancelarsePorPaciente()) {
            $horas = Cita::HORAS_MINIMAS_CANCELACION;
            return back()->with('error', "No puedes cancelar esta cita porque faltan menos de {$horas} horas para tu atención. Si tienes un imprevisto, comunícate con Admisión.");
        }

        $cita->update(['estado' => 'Cancelado']);

        return back()->with('success', 'Tu cita ha sido cancelada correctamente y el cupo fue liberado.');
    }

    /**
     * Calcula orden de atención y total diario del médico en una sola consulta parametrizada.
     */
    private function calcularOrdenJornada(Cita $cita): array
    {
        $resumen = Cita::where('medico_id', $cita->medico_id)
            ->enFecha($cita->fecha)
            ->noCanceladas()
            ->selectRaw(
                'COUNT(*) as total, SUM(CASE WHEN hora < ? OR (hora = ? AND id <= ?) THEN 1 ELSE 0 END) as orden',
                [$cita->hora, $cita->hora, $cita->id]
            )
            ->first();

        return [(int) ($resumen->orden ?? 0), (int) ($resumen->total ?? 0)];
    }
}