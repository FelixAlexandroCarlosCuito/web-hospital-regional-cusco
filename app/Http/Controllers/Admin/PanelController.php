<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Compila indicadores operativos, series gráficas y actividad reciente del panel administrativo
 * utilizando agregaciones condicionales parametrizadas y carga ansiosa acotada.
 */
class PanelController extends Controller
{
    public function panel()
    {
        $hoy = Carbon::today();
        $hoyStr = $hoy->toDateString();
        $inicioMes = Carbon::now()->startOfMonth();
        $inicioMesStr = $inicioMes->toDateString();

        // Consolida 9 conteos de citas en un solo recorrido de tabla con parámetros enlazados
        $resumenCitas = Cita::selectRaw(
            "SUM(CASE WHEN fecha = ? THEN 1 ELSE 0 END) as hoy_total,
            SUM(CASE WHEN fecha = ? AND estado = 'Pendiente' THEN 1 ELSE 0 END) as hoy_pendientes,
            SUM(CASE WHEN fecha = ? AND estado = 'Atendido' THEN 1 ELSE 0 END) as hoy_atendidas,
            SUM(CASE WHEN fecha = ? AND estado = 'Cancelado' THEN 1 ELSE 0 END) as hoy_canceladas,
            SUM(CASE WHEN fecha >= ? THEN 1 ELSE 0 END) as mes_total,
            SUM(CASE WHEN fecha >= ? AND estado = 'Atendido' THEN 1 ELSE 0 END) as mes_atendidas,
            SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as global_pendientes,
            SUM(CASE WHEN estado = 'Atendido' THEN 1 ELSE 0 END) as global_atendidas,
            SUM(CASE WHEN estado = 'Cancelado' THEN 1 ELSE 0 END) as global_canceladas,
            SUM(CASE WHEN codigo_referencia_sis IS NOT NULL AND codigo_referencia_sis != '' THEN 1 ELSE 0 END) as global_sis,
            SUM(CASE WHEN codigo_referencia_sis IS NULL OR codigo_referencia_sis = '' THEN 1 ELSE 0 END) as global_regulares",
            [$hoyStr, $hoyStr, $hoyStr, $hoyStr, $inicioMesStr, $inicioMesStr]
        )->first();

        $citasHoyTotal = (int) ($resumenCitas->hoy_total ?? 0);
        $citasHoyPendientes = (int) ($resumenCitas->hoy_pendientes ?? 0);
        $citasHoyAtendidas = (int) ($resumenCitas->hoy_atendidas ?? 0);
        $citasHoyCanceladas = (int) ($resumenCitas->hoy_canceladas ?? 0);
        $citasMesTotal = (int) ($resumenCitas->mes_total ?? 0);
        $citasMesAtendidas = (int) ($resumenCitas->mes_atendidas ?? 0);
        $tasaCumplimiento = $citasMesTotal > 0
            ? round(($citasMesAtendidas / $citasMesTotal) * 100, 1)
            : 0;

        $resumenPacientes = User::where('rol', 'paciente')
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as nuevos_mes', [$inicioMes])
            ->first();
        $totalPacientes = (int) ($resumenPacientes->total ?? 0);
        $pacientesNuevosMes = (int) ($resumenPacientes->nuevos_mes ?? 0);

        $resumenMedicos = Medico::selectRaw('COUNT(*) as total, SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activos')
            ->first();
        $medicosTotal = (int) ($resumenMedicos->total ?? 0);
        $medicosActivos = (int) ($resumenMedicos->activos ?? 0);

        $resumenEspecialidades = Especialidad::selectRaw('COUNT(*) as total, SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activas')
            ->first();
        $especialidadesTotal = (int) ($resumenEspecialidades->total ?? 0);
        $especialidadesActivas = (int) ($resumenEspecialidades->activas ?? 0);

        $demandaEspecialidades = DB::table('especialidades')
            ->leftJoin('medicos', 'especialidades.id', '=', 'medicos.especialidad_id')
            ->leftJoin('citas', 'medicos.id', '=', 'citas.medico_id')
            ->select('especialidades.nombre', DB::raw('COUNT(citas.id) as total'))
            ->groupBy('especialidades.id', 'especialidades.nombre')
            ->orderByDesc('total')
            ->get();

        // Agrupa directamente sobre columna DATE sargable sin aplicar DATE() fila por fila
        $fechasProximas = collect();
        for ($i = 0; $i < 7; $i++) {
            $fechasProximas->push($hoy->copy()->addDays($i));
        }

        $conteoPorFecha = Cita::select('fecha', DB::raw('COUNT(*) as total'))
            ->whereBetween('fecha', [$hoyStr, $hoy->copy()->addDays(6)->toDateString()])
            ->noCanceladas()
            ->groupBy('fecha')
            ->pluck('total', 'fecha');

        $tendenciaLabels = $fechasProximas->map(fn ($d) => $d->format('d/m'))->values();
        $tendenciaValores = $fechasProximas->map(fn ($d) => (int) ($conteoPorFecha[$d->toDateString()] ?? 0))->values();

        $cargaMedicos = Medico::select(['id', 'especialidad_id', 'nombres', 'apellidos', 'consultorio', 'activo'])
            ->with('especialidad:id,nombre')
            ->withCount([
                'citas as pendientes_count' => fn ($q) => $q->where('estado', 'Pendiente'),
                'citas as atendidas_count' => fn ($q) => $q->where('estado', 'Atendido'),
                'citas as canceladas_count' => fn ($q) => $q->where('estado', 'Cancelado'),
            ])
            ->orderByDesc('pendientes_count')
            ->get();

        $ultimasCitas = Cita::select(['id', 'user_id', 'medico_id', 'fecha', 'hora', 'estado', 'codigo_referencia_sis', 'created_at'])
            ->with([
                'paciente:id,tipo_documento,numero_documento,nombres,apellido_paterno,apellido_materno',
                'medico:id,especialidad_id,nombres,apellidos,consultorio',
                'medico.especialidad:id,nombre',
            ])
            ->latest('created_at')
            ->take(8)
            ->get();

        $metricasGraficos = [
            'especialidadesLabels' => $demandaEspecialidades->pluck('nombre')->values(),
            'especialidadesData' => $demandaEspecialidades->pluck('total')->values(),
            'tendenciaLabels' => $tendenciaLabels,
            'tendenciaValores' => $tendenciaValores,
            'estadosData' => [
                (int) ($resumenCitas->global_pendientes ?? 0),
                (int) ($resumenCitas->global_atendidas ?? 0),
                (int) ($resumenCitas->global_canceladas ?? 0),
            ],
            'sisData' => [
                (int) ($resumenCitas->global_sis ?? 0),
                (int) ($resumenCitas->global_regulares ?? 0),
            ],
        ];

        return view('admin.panel', compact(
            'citasHoyTotal',
            'citasHoyPendientes',
            'citasHoyAtendidas',
            'citasHoyCanceladas',
            'citasMesTotal',
            'citasMesAtendidas',
            'tasaCumplimiento',
            'totalPacientes',
            'pacientesNuevosMes',
            'medicosActivos',
            'medicosTotal',
            'especialidadesActivas',
            'especialidadesTotal',
            'cargaMedicos',
            'ultimasCitas',
            'metricasGraficos'
        ));
    }
}