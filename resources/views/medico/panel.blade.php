@extends('layouts.main')

@section('titulo', 'Panel Clínico')
@section('clase_main', 'p-0')

@section('contenido')
    @php
        $totalDia = $citasHoy->count();
        $pendientesDia = $citasHoy->where('estado', 'Pendiente')->count();
        $atendidasDia = $citasHoy->where('estado', 'Atendido')->count();
        $canceladasDia = $citasHoy->where('estado', 'Cancelado')->count();
        $fechaHoy = now()->toDateString();
        $fechaFormateada = \Carbon\Carbon::parse($fechaFiltro)->format('d/m/Y');
    @endphp

    <section class="cabecera-interna-mate seccion-pizarra py-4">
        <div class="container py-2">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <span class="sobretitulo-mate d-block mb-1">Agenda clínica diaria</span>
                    <h1 class="h3 fw-bold text-hrc-primario mb-1">
                        Dr. {{ $medico->nombres }} {{ $medico->apellidos }}
                    </h1>
                    <div class="d-flex flex-wrap align-items-center gap-3 small text-muted">
                        <span>
                            <i class="bi bi-heart-pulse me-1 text-hrc-acento"></i>{{ $medico->especialidad->nombre }}
                        </span>
                        <span>
                            <i class="bi bi-door-open me-1 text-hrc-primario"></i>{{ $medico->consultorio }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('medico.panel') }}"
                      method="GET"
                      id="form-filtro-fecha-medico"
                      class="pill-fecha-panel gap-2 align-self-start align-self-lg-center">
                    <label for="fecha" class="texto-xs fw-bold text-hrc-primario text-nowrap mb-0">
                        <i class="bi bi-calendar3 me-1 text-hrc-acento"></i>Fecha:
                    </label>
                    <input type="date"
                           id="fecha"
                           name="fecha"
                           value="{{ $fechaFiltro }}"
                           class="form-control form-control-sm input-fecha-panel">
                    @if ($fechaFiltro !== $fechaHoy)
                        <a href="{{ route('medico.panel') }}" class="btn btn-outline-hrc-primario btn-sm rounded-pill px-3">
                            Hoy
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </section>

    <section class="py-4 py-md-5">
        <div class="container">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            {{-- Bloque agrupado de indicadores operativos de la jornada --}}
            <div class="panel-card-elevada p-4 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h2 class="h6 fw-bold titulo-panel-acento mb-0">Resumen de la Jornada</h2>
                        <span class="texto-xs text-muted">
                            {{ $medico->especialidad->nombre }} &middot; {{ $medico->consultorio }}
                        </span>
                    </div>
                    <span class="pill-periodo-panel">
                        {{ $fechaFiltro === $fechaHoy ? 'Hoy · ' . $fechaFormateada : $fechaFormateada }}
                    </span>
                </div>

                <div class="row g-3">
                    <div class="col-6 col-lg-3">
                        <div class="kpi-bloque-interno p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="kpi-icono-cuadro kpi-icono-azul">
                                    <i class="bi bi-calendar2-week"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Programadas</div>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="h3 fw-bold text-hrc-primario mb-0">{{ $totalDia }}</span>
                                <span class="texto-xs text-muted">turnos</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="kpi-bloque-interno p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="kpi-icono-cuadro kpi-icono-ocre">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">En Espera</div>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="h3 fw-bold text-hrc-primario mb-0">{{ $pendientesDia }}</span>
                                <span class="texto-xs text-muted">pendientes</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="kpi-bloque-interno p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="kpi-icono-cuadro kpi-icono-salvia">
                                    <i class="bi bi-check2-circle"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Atendidas</div>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="h3 fw-bold text-hrc-primario mb-0">{{ $atendidasDia }}</span>
                                <span class="texto-xs text-muted">completadas</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="kpi-bloque-interno p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="kpi-icono-cuadro kpi-icono-terracota">
                                    <i class="bi bi-x-circle"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Canceladas</div>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="h3 fw-bold text-hrc-primario mb-0">{{ $canceladasDia }}</span>
                                <span class="texto-xs text-muted">anuladas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Listado de pacientes programados para la fecha consultada --}}
            <div class="panel-card-elevada overflow-hidden">
                <div class="px-4 pt-4 pb-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h2 class="h6 fw-bold titulo-panel-acento mb-0">
                            Pacientes del {{ $fechaFormateada }}
                        </h2>
                        <span class="texto-xs text-muted">
                            Ordenados por horario de atención en consultorio
                        </span>
                    </div>

                    @if ($totalDia > 0)
                        <div class="d-flex flex-wrap gap-2" role="group" aria-label="Filtrar citas por estado">
                            <button type="button" class="tab-estado-panel activo" data-estado="todos">
                                Todas ({{ $totalDia }})
                            </button>
                            <button type="button" class="tab-estado-panel" data-estado="Pendiente">
                                Pendientes ({{ $pendientesDia }})
                            </button>
                            <button type="button" class="tab-estado-panel" data-estado="Atendido">
                                Atendidas ({{ $atendidasDia }})
                            </button>
                            @if ($canceladasDia > 0)
                                <button type="button" class="tab-estado-panel" data-estado="Cancelado">
                                    Canceladas ({{ $canceladasDia }})
                                </button>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table tabla-panel-suave align-middle mb-0 small" id="tabla-agenda-medico">
                        <thead>
                            <tr>
                                <th class="ps-4">Hora</th>
                                <th>Paciente</th>
                                <th>Edad</th>
                                <th>Modalidad</th>
                                <th>Estado</th>
                                <th class="pe-4 text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($citasHoy as $cita)
                                @php
                                    $inicialesPac = mb_strtoupper(
                                        mb_substr(trim($cita->paciente->nombres), 0, 1, 'UTF-8') .
                                        mb_substr(trim($cita->paciente->apellido_paterno), 0, 1, 'UTF-8'),
                                        'UTF-8'
                                    );
                                    $edadTexto = $cita->paciente->fecha_nacimiento
                                        ? \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->age . ' años'
                                        : '—';
                                    $claseEstado = match ($cita->estado) {
                                        'Pendiente' => 'badge-agenda-pendiente',
                                        'Atendido' => 'badge-agenda-atendido',
                                        'Cancelado' => 'badge-agenda-cancelado',
                                        default => 'etiqueta-sin-cupos-mate',
                                    };
                                @endphp
                                <tr class="fila-agenda-medico {{ $cita->estado === 'Cancelado' ? 'fila-cita-inactiva' : '' }}"
                                    data-estado="{{ $cita->estado }}">
                                    <td class="ps-4 text-nowrap">
                                        <span class="hora-agenda-mate">
                                            {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-mini-panel flex-shrink-0">{{ $inicialesPac }}</div>
                                            <div class="min-w-0">
                                                <div class="fw-bold text-hrc-primario">
                                                    {{ strtoupper($cita->paciente->apellido_paterno . ' ' . $cita->paciente->apellido_materno) }},
                                                    <span class="fw-semibold text-dark">{{ $cita->paciente->nombres }}</span>
                                                </div>
                                                <div class="texto-xs text-muted">
                                                    {{ $cita->paciente->tipo_documento ?? 'DNI' }}: {{ $cita->paciente->numero_documento }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-dark text-nowrap">{{ $edadTexto }}</td>

                                    <td>
                                        @if($cita->codigo_referencia_sis)
                                            <span class="badge-sis-mate">SIS</span>
                                            <div class="texto-xs text-muted mt-1">Ref: {{ $cita->codigo_referencia_sis }}</div>
                                        @else
                                            <span class="badge-particular-mate">Particular</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="ticket-pill-estado {{ $claseEstado }} py-1 px-2 texto-xs">
                                            {{ $cita->estado }}
                                        </span>
                                    </td>

                                    <td class="pe-4 text-end text-nowrap">
                                        <div class="d-inline-flex align-items-center gap-2">
                                            <a href="{{ route('ticket', $cita->id) }}"
                                               class="btn btn-icono-panel btn-sm"
                                               title="Ver ticket de la cita"
                                               aria-label="Ver ticket">
                                                <i class="bi bi-ticket-detailed"></i>
                                            </a>

                                            @if($cita->estado === 'Pendiente')
                                                <form action="{{ route('medico.citas.atender', $cita->id) }}"
                                                      method="POST"
                                                      class="m-0 form-atender-cita">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-hrc-acento btn-sm rounded-pill px-3">
                                                        Atender
                                                    </button>
                                                </form>
                                            @elseif($cita->estado === 'Atendido')
                                                <span class="small fw-semibold text-hrc-salvia">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Atendido
                                                </span>
                                            @else
                                                <span class="small text-muted">Anulada</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="icono-vacio-mate mx-auto mb-3">
                                            <i class="bi bi-calendar2-check"></i>
                                        </div>
                                        <div class="fw-bold text-hrc-primario mb-1">Sin pacientes programados</div>
                                        <p class="small text-muted mb-0">
                                            No existen citas registradas para el {{ $fechaFormateada }}.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div id="sin-resultados-agenda" class="text-center py-4 text-muted small d-none">
                    No hay citas en este estado para la fecha consultada.
                </div>
            </div>

        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('js/medico-panel.js') }}"></script>
@endsection