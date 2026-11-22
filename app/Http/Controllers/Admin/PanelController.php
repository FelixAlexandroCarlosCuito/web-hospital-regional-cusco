<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{
    public function panel()
    {
        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();

        // 1. KPIs del día actual
        $citasHoyTotal      = Cita::whereDate('fecha', $hoy)->count();
        $citasHoyPendientes = Cita::whereDate('fecha', $hoy)->where('estado', 'Pendiente')->count();
        $citasHoyAtendidas  = Cita::whereDate('fecha', $hoy)->where('estado', 'Atendido')->count();
        $citasHoyCanceladas = Cita::whereDate('fecha', $hoy)->where('estado', 'Cancelado')->count();

        // 2. Tasa de cumplimiento mensual (Atendidas vs. total del mes)
        $citasMesTotal     = Cita::whereDate('fecha', '>=', $inicioMes)->count();
        $citasMesAtendidas = Cita::whereDate('fecha', '>=', $inicioMes)->where('estado', 'Atendido')->count();
        $tasaCumplimiento  = $citasMesTotal > 0
            ? round(($citasMesAtendidas / $citasMesTotal) * 100, 1)
            : 0;

        // 3. Pacientes y capacidad operativa
        $totalPacientes       = User::where('rol', 'paciente')->count();
        $pacientesNuevosMes   = User::where('rol', 'paciente')->where('created_at', '>=', $inicioMes)->count();
        $medicosActivos       = Medico::where('activo', true)->count();
        $medicosTotal         = Medico::count();
        $especialidadesActivas = Especialidad::where('activo', true)->count();
        $especialidadesTotal   = Especialidad::count();

        // 4. Gráfico 1: Demanda por especialidad (LEFT JOIN para incluir las que tienen 0 citas)
        $demandaEspecialidades = DB::table('especialidades')
            ->leftJoin('medicos', 'especialidades.id', '=', 'medicos.especialidad_id')
            ->leftJoin('citas', 'medicos.id', '=', 'citas.medico_id')
            ->select('especialidades.nombre', DB::raw('COUNT(citas.id) as total'))
            ->groupBy('especialidades.id', 'especialidades.nombre')
            ->orderByDesc('total')
            ->get();

        // 5. Gráfico 2: Agenda programada para los próximos 7 días (excluyendo canceladas)
        $fechasProximas = collect();
        for ($i = 0; $i < 7; $i++) {
            $fechasProximas->push($hoy->copy()->addDays($i));
        }

        $conteoPorFecha = Cita::select(DB::raw('DATE(fecha) as dia'), DB::raw('COUNT(*) as total'))
            ->whereBetween('fecha', [$hoy->toDateString(), $hoy->copy()->addDays(6)->toDateString()])
            ->where('estado', '!=', 'Cancelado')
            ->groupBy(DB::raw('DATE(fecha)'))
            ->pluck('total', 'dia');

        $tendenciaLabels = $fechasProximas->map(fn ($d) => $d->format('d/m'))->values();
        $tendenciaValores = $fechasProximas->map(fn ($d) => (int) ($conteoPorFecha[$d->toDateString()] ?? 0))->values();

        // 6. Gráfico 3: Distribución global por estado
        $estadosRaw = Cita::select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $distribucionEstados = [
            'Pendiente' => (int) ($estadosRaw['Pendiente'] ?? 0),
            'Atendido'  => (int) ($estadosRaw['Atendido'] ?? 0),
            'Cancelado' => (int) ($estadosRaw['Cancelado'] ?? 0),
        ];

        // 7. Gráfico 4: Referidos SIS vs. Pacientes Regulares
        $citasSis = Cita::whereNotNull('codigo_referencia_sis')
            ->where('codigo_referencia_sis', '!=', '')
            ->count();
        $citasRegulares = Cita::where(function ($q) {
            $q->whereNull('codigo_referencia_sis')
              ->orWhere('codigo_referencia_sis', '');
        })->count();

        // 8. Tabla 1: Carga operativa por médico (sin problemas de ONLY_FULL_GROUP_BY)
        $cargaMedicos = Medico::with('especialidad')
            ->withCount([
                'citas as pendientes_count' => fn ($q) => $q->where('estado', 'Pendiente'),
                'citas as atendidas_count'  => fn ($q) => $q->where('estado', 'Atendido'),
                'citas as canceladas_count' => fn ($q) => $q->where('estado', 'Cancelado'),
            ])
            ->orderByDesc('pendientes_count')
            ->get();

        // 9. Tabla 2: Últimas 8 citas registradas en el sistema
        $ultimasCitas = Cita::with(['paciente', 'medico.especialidad'])
            ->latest()
            ->take(8)
            ->get();

        
        // Agrupa las series de los gráficos en una sola estructura serializable para la vista
        $metricasGraficos = [
            'especialidadesLabels' => $demandaEspecialidades->pluck('nombre')->values(),
            'especialidadesData' => $demandaEspecialidades->pluck('total')->values(),
            'tendenciaLabels' => $tendenciaLabels,
            'tendenciaValores' => $tendenciaValores,
            'estadosData' => array_values($distribucionEstados),
            'sisData' => [$citasSis, $citasRegulares],
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