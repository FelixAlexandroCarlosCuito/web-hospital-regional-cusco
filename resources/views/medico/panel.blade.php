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
                      class="d-flex flex-wrap align-items-center gap-2">
                    <label for="fecha" class="small fw-semibold text-hrc-primario text-nowrap mb-0">
                        Fecha de consulta:
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

            {{-- Indicadores operativos de la jornada seleccionada --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="kpi-panel-mate p-3 h-100">
                        <span class="kpi-etiqueta d-block mb-1">Programadas</span>
                        <div class="h3 fw-bold text-hrc-primario mb-0">{{ $totalDia }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="kpi-panel-mate p-3 h-100">
                        <span class="kpi-etiqueta d-block mb-1">En espera</span>
                        <div class="h3 fw-bold text-hrc-ocre mb-0">{{ $pendientesDia }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="kpi-panel-mate p-3 h-100">
                        <span class="kpi-etiqueta d-block mb-1">Atendidas</span>
                        <div class="h3 fw-bold text-hrc-salvia mb-0">{{ $atendidasDia }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="kpi-panel-mate p-3 h-100">
                        <span class="kpi-etiqueta d-block mb-1">Canceladas</span>
                        <div class="h3 fw-bold text-muted mb-0">{{ $canceladasDia }}</div>
                    </div>
                </div>
            </div>

            {{-- Listado principal de pacientes del día --}}
            <div class="tarjeta-panel-mate overflow-hidden">
                <div class="panel-cabecera-tabla px-4 py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h2 class="h5 fw-bold text-hrc-primario mb-0">Pacientes del {{ $fechaFormateada }}</h2>
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
                        </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table tabla-mate align-middle mb-0" id="tabla-agenda-medico">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3">Hora</th>
                                <th class="py-3">Paciente</th>
                                <th class="py-3">Edad</th>
                                <th class="py-3">Modalidad</th>
                                <th class="py-3">Estado</th>
                                <th class="pe-4 py-3 text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($citasHoy as $cita)
                                @php
                                    $claseEstado = match ($cita->estado) {
                                        'Pendiente' => 'badge-agenda-pendiente',
                                        'Atendido' => 'badge-agenda-atendido',
                                        'Cancelado' => 'badge-agenda-cancelado',
                                        default => 'etiqueta-sin-cupos-mate',
                                    };
                                @endphp
                                <tr class="fila-agenda-medico {{ $cita->estado === 'Cancelado' ? 'fila-cita-inactiva' : '' }}"
                                    data-estado="{{ $cita->estado }}">
                                    <td class="ps-4 py-3 text-nowrap">
                                        <span class="hora-agenda-mate">
                                            {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                                        </span>
                                    </td>

                                    <td class="py-3">
                                        <div class="fw-bold text-hrc-primario small">
                                            {{ strtoupper($cita->paciente->apellido_paterno . ' ' . $cita->paciente->apellido_materno) }},
                                            <span class="fw-semibold text-dark">{{ $cita->paciente->nombres }}</span>
                                        </div>
                                        <div class="texto-xs text-muted">
                                            {{ $cita->paciente->tipo_documento ?? 'DNI' }}: {{ $cita->paciente->numero_documento }}
                                        </div>
                                    </td>

                                    <td class="py-3 small text-dark text-nowrap">
                                        {{ \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->age }} años
                                    </td>

                                    <td class="py-3">
                                        @if($cita->codigo_referencia_sis)
                                            <span class="badge-sis-mate">SIS</span>
                                            <div class="texto-xs text-muted mt-1">Ref: {{ $cita->codigo_referencia_sis }}</div>
                                        @else
                                            <span class="badge-particular-mate">Particular</span>
                                        @endif
                                    </td>

                                    <td class="py-3">
                                        <span class="ticket-pill-estado {{ $claseEstado }}">
                                            {{ $cita->estado }}
                                        </span>
                                    </td>

                                    <td class="pe-4 py-3 text-end text-nowrap">
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