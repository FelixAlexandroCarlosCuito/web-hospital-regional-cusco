@extends('layouts.main')

@section('titulo', 'Panel de Administración')
@section('clase_main', 'p-0')

@section('contenido')
    @php
        $nivelCumplimiento = $tasaCumplimiento >= 80
            ? ['texto' => 'Óptimo', 'clase' => 'badge-agenda-atendido']
            : ($tasaCumplimiento >= 50
                ? ['texto' => 'En seguimiento', 'clase' => 'badge-agenda-pendiente']
                : ['texto' => 'Por mejorar', 'clase' => 'badge-agenda-cancelado']);
    @endphp

    <section class="cabecera-interna-mate seccion-pizarra py-4">
        <div class="container py-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <span class="sobretitulo-mate d-block mb-1">Gestión institucional</span>
                    <h1 class="h3 fw-bold text-hrc-primario mb-1">Panel de Administración</h1>
                    <p class="text-muted small mb-0">
                        Monitoreo operativo y control de demanda — Hospital Regional del Cusco
                    </p>
                </div>

                <div class="pill-fecha-panel align-self-start align-self-md-center">
                    <i class="bi bi-clock me-1 text-hrc-acento"></i>
                    Actualizado: {{ now()->format('d/m/Y h:i A') }}
                </div>
            </div>

            {{-- Navegación horizontal entre módulos administrativos --}}
            @include('admin._nav')
        </div>
    </section>

    <section class="py-4 py-md-5">
        <div class="container">

            {{-- Bloque agrupado de indicadores operativos --}}
            <div class="panel-card-elevada p-4 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h2 class="h5 fw-bold titulo-panel-acento mb-0">Resumen Operativo</h2>
                        <span class="texto-xs text-muted">Indicadores consolidados de consulta externa</span>
                    </div>
                    <span class="pill-periodo-panel">{{ now()->format('d/m/Y') }}</span>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="kpi-bloque-interno p-3 h-100 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="kpi-icono-cuadro kpi-icono-azul">
                                    <i class="bi bi-calendar2-week"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Citas para Hoy</div>
                            </div>

                            <div class="mb-2">
                                <span class="h2 fw-bold text-hrc-primario mb-0">{{ $citasHoyTotal }}</span>
                                <span class="texto-xs text-muted ms-1">turnos</span>
                            </div>

                            <div class="texto-xs text-muted d-flex flex-wrap gap-2">
                                <span><strong class="text-hrc-ocre">{{ $citasHoyPendientes }}</strong> pend.</span>
                                <span>&middot;</span>
                                <span><strong class="text-hrc-salvia">{{ $citasHoyAtendidas }}</strong> atend.</span>
                                <span>&middot;</span>
                                <span><strong class="text-hrc-acento">{{ $citasHoyCanceladas }}</strong> canc.</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="kpi-bloque-interno p-3 h-100 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="kpi-icono-cuadro kpi-icono-salvia">
                                    <i class="bi bi-check2-circle"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Cumplimiento (Mes)</div>
                            </div>

                            <div class="mb-2">
                                <span class="h2 fw-bold text-hrc-primario mb-0">{{ $tasaCumplimiento }}%</span>
                            </div>

                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <span class="ticket-pill-estado {{ $nivelCumplimiento['clase'] }} py-1 px-2 texto-xs">
                                    {{ $nivelCumplimiento['texto'] }}
                                </span>
                                <span class="texto-xs text-muted">
                                    {{ $citasMesAtendidas }} / {{ $citasMesTotal }} citas
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="kpi-bloque-interno p-3 h-100 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="kpi-icono-cuadro kpi-icono-terracota">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Pacientes Registrados</div>
                            </div>

                            <div class="mb-2">
                                <span class="h2 fw-bold text-hrc-primario mb-0">{{ $totalPacientes }}</span>
                                <span class="texto-xs text-muted ms-1">cuentas</span>
                            </div>

                            <div>
                                <span class="ticket-pill-estado badge-agenda-atendido py-1 px-2 texto-xs">
                                    +{{ $pacientesNuevosMes }} nuevos este mes
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="kpi-bloque-interno p-3 h-100 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="kpi-icono-cuadro kpi-icono-ocre">
                                    <i class="bi bi-hospital"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Capacidad Operativa</div>
                            </div>

                            <div class="mb-2">
                                <span class="h2 fw-bold text-hrc-primario mb-0">{{ $medicosActivos }}</span>
                                <span class="texto-xs text-muted">/ {{ $medicosTotal }} médicos activos</span>
                            </div>

                            <div class="texto-xs text-muted">
                                <strong class="text-hrc-primario">{{ $especialidadesActivas }}</strong> de {{ $especialidadesTotal }} especialidades activas
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Gráficos principales de demanda y agenda semanal --}}
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="panel-card-elevada p-4 h-100">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <div>
                                <h2 class="h6 fw-bold titulo-panel-acento mb-0">Demanda por Especialidad</h2>
                                <span class="texto-xs text-muted">Histórico acumulado de citas registradas</span>
                            </div>
                            <span class="pill-periodo-panel">Histórico</span>
                        </div>
                        <div class="contenedor-canvas-barras">
                            <canvas id="graficoEspecialidades"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="panel-card-elevada p-4 h-100">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <div>
                                <h2 class="h6 fw-bold titulo-panel-acento mb-0">Agenda Programada</h2>
                                <span class="texto-xs text-muted">Proyección de demanda en consultorios externos</span>
                            </div>
                            <span class="pill-periodo-panel">Próximos 7 días</span>
                        </div>
                        <div class="contenedor-canvas-barras">
                            <canvas id="graficoTendencia"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Gráficos de distribución porcentual --}}
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="panel-card-elevada p-4 h-100">
                        <div class="mb-3">
                            <h2 class="h6 fw-bold titulo-panel-acento mb-0">Distribución Global por Estado</h2>
                            <span class="texto-xs text-muted">Proporción de citas pendientes, atendidas y canceladas</span>
                        </div>
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="contenedor-canvas-dona">
                                <canvas id="graficoEstados"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="panel-card-elevada p-4 h-100">
                        <div class="mb-3">
                            <h2 class="h6 fw-bold titulo-panel-acento mb-0">Modalidad de Atención</h2>
                            <span class="texto-xs text-muted">Comparativa entre pacientes con referencia SIS y regulares</span>
                        </div>
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="contenedor-canvas-dona">
                                <canvas id="graficoSis"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tablas de supervisión operativa --}}
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="panel-card-elevada overflow-hidden h-100">
                        <div class="px-4 pt-4 pb-3 d-flex justify-content-between align-items-center gap-2">
                            <div>
                                <h2 class="h6 fw-bold titulo-panel-acento mb-0">Carga por Médico</h2>
                                <span class="texto-xs text-muted">Distribución actual por profesional</span>
                            </div>
                            <span class="pill-periodo-panel">{{ $cargaMedicos->count() }} reg.</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table tabla-panel-suave align-middle mb-0 small">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Médico</th>
                                        <th class="text-center">Pend.</th>
                                        <th class="text-center">Atend.</th>
                                        <th class="text-end pe-4">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cargaMedicos as $medico)
                                        @php
                                            $inicialesMed = mb_strtoupper(
                                                mb_substr(trim($medico->nombres), 0, 1, 'UTF-8') .
                                                mb_substr(trim($medico->apellidos), 0, 1, 'UTF-8'),
                                                'UTF-8'
                                            );
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-mini-panel flex-shrink-0">{{ $inicialesMed }}</div>
                                                    <div class="min-w-0">
                                                        <div class="fw-bold text-hrc-primario text-truncate">
                                                            Dr. {{ $medico->nombres }} {{ $medico->apellidos }}
                                                        </div>
                                                        <div class="texto-xs text-muted text-truncate">
                                                            {{ $medico->especialidad->nombre ?? 'Sin asignar' }} &middot; {{ $medico->consultorio }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center fw-bold text-hrc-ocre">{{ $medico->pendientes_count }}</td>
                                            <td class="text-center fw-bold text-hrc-salvia">{{ $medico->atendidas_count }}</td>
                                            <td class="text-end pe-4">
                                                @if($medico->activo)
                                                    <span class="ticket-pill-estado badge-agenda-atendido py-1 px-2 texto-xs">Activo</span>
                                                @else
                                                    <span class="etiqueta-sin-cupos-mate py-1 px-2 texto-xs">Inactivo</span>
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

                <div class="col-lg-7">
                    <div class="panel-card-elevada overflow-hidden h-100">
                        <div class="px-4 pt-4 pb-3 d-flex justify-content-between align-items-center gap-2">
                            <div>
                                <h2 class="h6 fw-bold titulo-panel-acento mb-0">Actividad Reciente</h2>
                                <span class="texto-xs text-muted">Últimas 8 reservas registradas en el portal</span>
                            </div>
                            <span class="pill-periodo-panel">En tiempo real</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table tabla-panel-suave align-middle mb-0 small">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Paciente</th>
                                        <th>Especialidad / Médico</th>
                                        <th>Fecha y Hora</th>
                                        <th class="text-end pe-4">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ultimasCitas as $cita)
                                        @php
                                            $claseEstado = match ($cita->estado) {
                                                'Atendido' => 'badge-agenda-atendido',
                                                'Cancelado' => 'badge-agenda-cancelado',
                                                default => 'badge-agenda-pendiente',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark">
                                                    {{ $cita->paciente->nombres ?? 'N/A' }} {{ $cita->paciente->apellido_paterno ?? '' }}
                                                </div>
                                                <div class="texto-xs text-muted">
                                                    {{ $cita->paciente->numero_documento ?? '' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-hrc-primario">
                                                    {{ $cita->medico->especialidad->nombre ?? 'N/A' }}
                                                </div>
                                                <div class="texto-xs text-muted">
                                                    Dr. {{ $cita->medico->apellidos ?? '' }}
                                                </div>
                                            </td>
                                            <td class="text-nowrap">
                                                <div class="fw-semibold text-dark">
                                                    {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                                                </div>
                                                <div class="texto-xs text-muted">
                                                    {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="ticket-pill-estado {{ $claseEstado }} py-1 px-2 texto-xs">
                                                    {{ $cita->estado }}
                                                </span>
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
    </section>
@endsection

@section('js')
    <script id="datos-metricas-admin" type="application/json">@json($metricasGraficos)</script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/admin/panel.js') }}"></script>
@endsection