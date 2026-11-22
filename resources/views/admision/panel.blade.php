@extends('layouts.main')

@section('titulo', 'Panel de Admisión')
@section('clase_main', 'p-0')

@section('contenido')
    @php
        $medicoSeleccionado = $medicoFiltro
            ? $medicos->first(fn ($m) => (string) $m->id === (string) $medicoFiltro)
            : null;
        $fechaFormateada = $fechaFiltro
            ? \Carbon\Carbon::parse($fechaFiltro)->format('d/m/Y')
            : null;
    @endphp

    <section class="cabecera-interna-mate seccion-pizarra py-4">
        <div class="container py-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <span class="sobretitulo-mate d-block mb-1">Admisión y ventanilla</span>
                    <h1 class="h3 fw-bold text-hrc-primario mb-1">Control de Agendas Médicas</h1>
                    <p class="text-muted small mb-0">
                        Consulta la programación diaria por especialista, emite constancias y gestiona cancelaciones operativas.
                    </p>
                </div>

                @if ($medicoSeleccionado)
                    <div class="pill-fecha-panel align-self-start align-self-md-center">
                        <i class="bi bi-person-badge me-2 text-hrc-acento"></i>
                        <span>
                            Dr. {{ $medicoSeleccionado->apellidos }} {{ $medicoSeleccionado->nombres }}
                            &middot; {{ $medicoSeleccionado->consultorio }}
                        </span>
                    </div>
                @else
                    <div class="pill-fecha-panel align-self-start align-self-md-center">
                        <i class="bi bi-calendar3 me-2 text-hrc-acento"></i>
                        <span>Hoy: {{ now()->format('d/m/Y') }}</span>
                    </div>
                @endif
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

            {{-- Bloque de consulta de agenda por médico y fecha --}}
            <div class="panel-card-elevada p-4 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h2 class="h6 fw-bold titulo-panel-acento mb-0">Consulta de Agenda</h2>
                        <span class="texto-xs text-muted">Seleccione especialista y día de atención para cargar la programación</span>
                    </div>
                    @if ($medicoSeleccionado)
                        <span class="pill-periodo-panel">{{ $medicoSeleccionado->especialidad->nombre }}</span>
                    @endif
                </div>

                <form action="{{ route('admision.panel') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-lg-6">
                        <label for="medico_id" class="form-label small fw-bold text-hrc-primario">
                            Especialista a consultar
                        </label>
                        <select name="medico_id" id="medico_id" class="form-select input-panel-mate" required>
                            <option value="" disabled {{ !$medicoFiltro ? 'selected' : '' }}>
                                Seleccione un médico...
                            </option>
                            @foreach($medicos as $medico)
                                <option value="{{ $medico->id }}" {{ (string) $medicoFiltro === (string) $medico->id ? 'selected' : '' }}>
                                    Dr. {{ $medico->apellidos }} {{ $medico->nombres }} — {{ $medico->especialidad->nombre }} ({{ $medico->consultorio }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-7 col-lg-3">
                        <label for="fecha" class="form-label small fw-bold text-hrc-primario">
                            Fecha de atención
                        </label>
                        <input type="date"
                               name="fecha"
                               id="fecha"
                               value="{{ $fechaFiltro }}"
                               class="form-control input-panel-mate"
                               required>
                    </div>

                    <div class="col-md-5 col-lg-3">
                        <button type="submit" class="btn btn-hrc-acento rounded-pill w-100 py-2">
                            <i class="bi bi-search me-1"></i> Consultar Agenda
                        </button>
                    </div>
                </form>
            </div>

            @if($medicoFiltro)
                @php
                    $totalAgenda = $citas->count();
                    $pendientesAgenda = $citas->where('estado', 'Pendiente')->count();
                    $atendidasAgenda = $citas->where('estado', 'Atendido')->count();
                    $canceladasAgenda = $citas->where('estado', 'Cancelado')->count();
                @endphp

                {{-- Resumen agrupado de indicadores de la agenda consultada --}}
                <div class="panel-card-elevada p-4 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <h2 class="h6 fw-bold titulo-panel-acento mb-0">Resumen de la Jornada</h2>
                            <span class="texto-xs text-muted">
                                @if ($medicoSeleccionado)
                                    Dr. {{ $medicoSeleccionado->apellidos }} {{ $medicoSeleccionado->nombres }} &middot; {{ $medicoSeleccionado->consultorio }}
                                @else
                                    Estado actual de los turnos asignados
                                @endif
                            </span>
                        </div>
                        <span class="pill-periodo-panel">{{ $fechaFormateada }}</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-6 col-lg-3">
                            <div class="kpi-bloque-interno p-3 h-100">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="kpi-icono-cuadro kpi-icono-azul">
                                        <i class="bi bi-calendar2-week"></i>
                                    </div>
                                    <div class="small fw-bold text-hrc-primario lh-sm">Total Turnos</div>
                                </div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <span class="h3 fw-bold text-hrc-primario mb-0">{{ $totalAgenda }}</span>
                                    <span class="texto-xs text-muted">programados</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3">
                            <div class="kpi-bloque-interno p-3 h-100">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="kpi-icono-cuadro kpi-icono-ocre">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
                                    <div class="small fw-bold text-hrc-primario lh-sm">Pendientes</div>
                                </div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <span class="h3 fw-bold text-hrc-primario mb-0">{{ $pendientesAgenda }}</span>
                                    <span class="texto-xs text-muted">en espera</span>
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
                                    <span class="h3 fw-bold text-hrc-primario mb-0">{{ $atendidasAgenda }}</span>
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
                                    <span class="h3 fw-bold text-hrc-primario mb-0">{{ $canceladasAgenda }}</span>
                                    <span class="texto-xs text-muted">cupos liberados</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabla operativa de citas con búsqueda y pestañas locales --}}
                <div class="panel-card-elevada overflow-hidden">
                    <div class="px-4 pt-4 pb-3 border-bottom d-flex flex-column gap-3">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <h2 class="h6 fw-bold titulo-panel-acento mb-0">
                                    Pacientes Programados &middot; {{ $fechaFormateada }}
                                </h2>
                                @if ($medicoSeleccionado)
                                    <span class="texto-xs text-muted">
                                        {{ $medicoSeleccionado->especialidad->nombre }} &middot; {{ $medicoSeleccionado->consultorio }}
                                    </span>
                                @endif
                            </div>

                            @if ($totalAgenda > 0)
                                <div class="buscador-tabla-panel d-flex align-items-center">
                                    <i class="bi bi-search text-muted ms-3 small"></i>
                                    <input type="text"
                                           id="buscador-paciente-admision"
                                           class="form-control form-control-sm border-0 bg-transparent shadow-none px-2"
                                           placeholder="Buscar paciente o DNI..."
                                           aria-label="Buscar paciente por nombre o documento">
                                </div>
                            @endif
                        </div>

                        @if ($totalAgenda > 0)
                            <div class="d-flex flex-wrap gap-2" role="group" aria-label="Filtrar agenda por estado">
                                <button type="button" class="tab-estado-panel activo" data-estado="todos">
                                    Todas ({{ $totalAgenda }})
                                </button>
                                <button type="button" class="tab-estado-panel" data-estado="Pendiente">
                                    Pendientes ({{ $pendientesAgenda }})
                                </button>
                                <button type="button" class="tab-estado-panel" data-estado="Atendido">
                                    Atendidas ({{ $atendidasAgenda }})
                                </button>
                                <button type="button" class="tab-estado-panel" data-estado="Cancelado">
                                    Canceladas ({{ $canceladasAgenda }})
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table tabla-panel-suave align-middle mb-0 small" id="tabla-agenda-admision">
                            <thead>
                                <tr>
                                    <th class="ps-4">Orden</th>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Modalidad</th>
                                    <th>Estado</th>
                                    <th class="pe-4 text-end">Gestión operativa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($citas as $index => $cita)
                                    @php
                                        $nombreCompleto = strtoupper($cita->paciente->apellido_paterno . ' ' . $cita->paciente->apellido_materno) . ', ' . $cita->paciente->nombres;
                                        $inicialesPac = mb_strtoupper(
                                            mb_substr(trim($cita->paciente->nombres), 0, 1, 'UTF-8') .
                                            mb_substr(trim($cita->paciente->apellido_paterno), 0, 1, 'UTF-8'),
                                            'UTF-8'
                                        );
                                        $horaTexto = \Carbon\Carbon::parse($cita->hora)->format('h:i A');
                                        $claseEstado = match ($cita->estado) {
                                            'Pendiente' => 'badge-agenda-pendiente',
                                            'Atendido' => 'badge-agenda-atendido',
                                            'Cancelado' => 'badge-agenda-cancelado',
                                            default => 'etiqueta-sin-cupos-mate',
                                        };
                                        $indiceBusqueda = trim("{$nombreCompleto} {$cita->paciente->numero_documento} {$cita->codigo_referencia_sis}");
                                    @endphp
                                    <tr class="fila-agenda-admision {{ $cita->estado === 'Cancelado' ? 'fila-cita-inactiva' : '' }}"
                                        data-estado="{{ $cita->estado }}"
                                        data-busqueda="{{ $indiceBusqueda }}">
                                        <td class="ps-4 fw-bold text-muted text-nowrap">
                                            #{{ $index + 1 }}
                                        </td>

                                        <td class="text-nowrap">
                                            <span class="hora-agenda-mate">{{ $horaTexto }}</span>
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
                                                   target="_blank"
                                                   rel="noopener"
                                                   class="btn btn-icono-panel btn-sm"
                                                   title="Abrir constancia para impresión">
                                                    <i class="bi bi-printer me-1"></i>Ticket
                                                </a>

                                                @if($cita->estado === 'Pendiente')
                                                    <button type="button"
                                                            class="btn btn-cancelar-mate btn-sm rounded-pill px-3 btn-abrir-cancelar-admision"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalCancelarAdmision"
                                                            data-action="{{ route('admision.citas.cancelar', $cita->id) }}"
                                                            data-paciente="{{ $nombreCompleto }}"
                                                            data-documento="{{ $cita->paciente->numero_documento }}"
                                                            data-fecha="{{ $fechaFormateada }}"
                                                            data-hora="{{ $horaTexto }}">
                                                        Cancelar
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="icono-vacio-mate mx-auto mb-3">
                                                <i class="bi bi-calendar2-x"></i>
                                            </div>
                                            <div class="fw-bold text-hrc-primario mb-1">Sin agenda asignada</div>
                                            <p class="small text-muted mb-0">
                                                El especialista seleccionado no tiene pacientes programados para el {{ $fechaFormateada }}.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div id="sin-resultados-admision" class="text-center py-4 text-muted small d-none">
                        No se encontraron pacientes que coincidan con el filtro aplicado.
                    </div>
                </div>
            @else
                <div class="panel-card-elevada text-center py-5 px-4">
                    <div class="icono-vacio-mate mx-auto mb-3">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>
                    <h2 class="h5 fw-bold text-hrc-primario mb-1">Seleccione un médico y una fecha</h2>
                    <p class="small text-muted mx-auto subtitulo-acotado mb-0">
                        Utilice el buscador superior para desplegar la lista de pacientes programados, imprimir constancias o liberar cupos desde ventanilla.
                    </p>
                </div>
            @endif

        </div>
    </section>

    {{-- Modal de confirmación para anulación operativa desde Admisión --}}
    <div class="modal fade" id="modalCancelarAdmision" tabindex="-1" aria-labelledby="modalCancelarAdmisionTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow modal-mate">
                <form id="formCancelarAdmision" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold text-hrc-primario" id="modalCancelarAdmisionTitulo">
                            Confirmar cancelación desde ventanilla
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <p class="small text-muted mb-3">
                            Al confirmar, la cita pasará a estado <strong>Cancelado</strong> y el horario volverá a quedar disponible para nuevos pacientes.
                        </p>
                        <div class="resumen-reserva-mate p-3 small">
                            <div class="fw-bold text-hrc-primario mb-1" id="admision-modal-paciente">—</div>
                            <div class="text-muted mb-1">
                                Documento: <strong id="admision-modal-documento">—</strong>
                            </div>
                            <div class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i><span id="admision-modal-fecha">—</span>
                                &middot;
                                <i class="bi bi-clock me-1"></i><span id="admision-modal-hora">—</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-2">
                        <button type="button" class="btn btn-outline-hrc-primario rounded-pill px-4" data-bs-dismiss="modal">
                            Mantener cita
                        </button>
                        <button type="submit" class="btn btn-hrc-acento rounded-pill px-4" id="btn-confirmar-cancelar-admision">
                            Anular y liberar cupo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/admision-panel.js') }}"></script>
@endsection