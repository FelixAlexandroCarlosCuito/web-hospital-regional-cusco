@extends('layouts.main')

@section('titulo', 'Panel de Administración')

@section('contenido')
<div class="container">

    {{-- Encabezado y Sub-navegación del Módulo Admin --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-hrc-primario mb-1">Panel de Administración</h2>
            <p class="text-muted mb-0">Monitoreo operativo y gestión integral — Hospital Regional del Cusco</p>
        </div>
        <div class="text-md-end">
            <span class="badge bg-light text-dark border px-3 py-2">
                Actualizado: {{ now()->format('d/m/Y h:i A') }}
            </span>
        </div>
    </div>

    {{-- Barra de módulos internos del Admin --}}
    @include('admin._nav')

    {{-- FILA 1: TARJETAS KPI --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                <div class="card-body">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Citas para Hoy</div>
                    <div class="h2 fw-bold text-hrc-primario mb-2">{{ $citasHoyTotal }}</div>
                    <div class="small text-muted">
                        <span class="text-warning fw-bold">{{ $citasHoyPendientes }}</span> pend. &middot;
                        <span class="text-success fw-bold">{{ $citasHoyAtendidas }}</span> atend. &middot;
                        <span class="text-danger fw-bold">{{ $citasHoyCanceladas }}</span> canc.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                <div class="card-body">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Cumplimiento (Mes)</div>
                    <div class="h2 fw-bold text-success mb-2">{{ $tasaCumplimiento }}%</div>
                    <div class="small text-muted">
                        {{ $citasMesAtendidas }} atendidas de {{ $citasMesTotal }} programadas
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                <div class="card-body">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Pacientes Registrados</div>
                    <div class="h2 fw-bold text-hrc-primario mb-2">{{ $totalPacientes }}</div>
                    <div class="small text-muted">
                        +{{ $pacientesNuevosMes }} nuevos este mes
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                <div class="card-body">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Capacidad Operativa</div>
                    <div class="h2 fw-bold text-hrc-primario mb-2">{{ $medicosActivos }} <span class="fs-6 fw-normal text-muted">/ {{ $medicosTotal }} médicos</span></div>
                    <div class="small text-muted">
                        {{ $especialidadesActivas }} de {{ $especialidadesTotal }} especialidades activas
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILA 2: GRÁFICOS PRINCIPALES (Barras + Línea) --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-hrc-primario mb-0">Demanda Histórica por Especialidad</h6>
                </div>
                <div class="card-body">
                    <div style="height: 280px;">
                        <canvas id="graficoEspecialidades"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-hrc-primario mb-0">Agenda Programada (Próximos 7 días)</h6>
                </div>
                <div class="card-body">
                    <div style="height: 280px;">
                        <canvas id="graficoTendencia"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILA 3: GRÁFICOS DE DISTRIBUCIÓN (Donas) --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-hrc-primario mb-0">Distribución Global por Estado</h6>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div style="height: 250px; width: 100%; max-width: 340px;">
                        <canvas id="graficoEstados"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-hrc-primario mb-0">Origen de Atención: Referencia SIS vs. Regular</h6>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div style="height: 250px; width: 100%; max-width: 340px;">
                        <canvas id="graficoSis"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILA 4: TABLAS DE SUPERVISIÓN --}}
    <div class="row g-4">
        {{-- Carga por Médico --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-hrc-primario mb-0">Carga Operativa por Médico</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="ps-3">Médico</th>
                                <th class="text-center">Pend.</th>
                                <th class="text-center">Atend.</th>
                                <th class="text-center pe-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cargaMedicos as $medico)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-dark">Dr. {{ $medico->nombres }} {{ $medico->apellidos }}</div>
                                        <div class="text-muted">{{ $medico->especialidad->nombre ?? 'Sin asignar' }} &middot; {{ $medico->consultorio }}</div>
                                    </td>
                                    <td class="text-center fw-bold text-warning">{{ $medico->pendientes_count }}</td>
                                    <td class="text-center fw-bold text-success">{{ $medico->atendidas_count }}</td>
                                    <td class="text-center pe-3">
                                        @if($medico->activo)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No hay médicos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Monitor de Últimas Citas --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-hrc-primario mb-0">Actividad Reciente (Últimas 8 reservas)</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="ps-3">Paciente</th>
                                <th>Especialidad / Médico</th>
                                <th>Fecha y Hora</th>
                                <th class="pe-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimasCitas as $cita)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold">{{ $cita->paciente->nombres ?? 'N/A' }} {{ $cita->paciente->apellido_paterno ?? '' }}</div>
                                        <div class="text-muted">{{ $cita->paciente->numero_documento ?? '' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-hrc-primario">{{ $cita->medico->especialidad->nombre ?? 'N/A' }}</div>
                                        <div class="text-muted">Dr. {{ $cita->medico->apellidos ?? '' }}</div>
                                    </td>
                                    <td>
                                        <div>{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</div>
                                        <div class="text-muted">{{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}</div>
                                    </td>
                                    <td class="pe-3">
                                        @if($cita->estado === 'Atendido')
                                            <span class="badge bg-success">Atendido</span>
                                        @elseif($cita->estado === 'Cancelado')
                                            <span class="badge bg-danger">Cancelado</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Aún no se han registrado citas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const especialidadesLabels = @json($demandaEspecialidades->pluck('nombre'));
        const especialidadesData   = @json($demandaEspecialidades->pluck('total'));
        const tendenciaLabels      = @json($tendenciaLabels);
        const tendenciaValores     = @json($tendenciaValores);
        const estadosData          = @json(array_values($distribucionEstados));
        const sisData              = @json([$citasSis, $citasRegulares]);

        // 1. Demanda por especialidad
        new Chart(document.getElementById('graficoEspecialidades'), {
            type: 'bar',
            data: {
                labels: especialidadesLabels,
                datasets: [{
                    label: 'Total de citas',
                    data: especialidadesData,
                    backgroundColor: '#1e3a5f',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });

        // 2. Agenda próximos 7 días
        new Chart(document.getElementById('graficoTendencia'), {
            type: 'line',
            data: {
                labels: tendenciaLabels,
                datasets: [{
                    label: 'Citas programadas',
                    data: tendenciaValores,
                    borderColor: '#6f42c1',
                    backgroundColor: 'rgba(111, 66, 193, 0.12)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });

        // 3. Distribución por estado
        new Chart(document.getElementById('graficoEstados'), {
            type: 'doughnut',
            data: {
                labels: ['Pendiente', 'Atendido', 'Cancelado'],
                datasets: [{
                    data: estadosData,
                    backgroundColor: ['#ffc107', '#198754', '#dc3545'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // 4. Referencia SIS vs Regular
        new Chart(document.getElementById('graficoSis'), {
            type: 'doughnut',
            data: {
                labels: ['Con Referencia SIS', 'Paciente Regular'],
                datasets: [{
                    data: sisData,
                    backgroundColor: ['#0d6efd', '#6c757d'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    });
</script>
@endsection