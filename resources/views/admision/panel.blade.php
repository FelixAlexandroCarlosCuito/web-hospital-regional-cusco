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
                    <div class="text-md-end small text-muted">
                        <div class="fw-bold text-hrc-primario">
                            Dr. {{ $medicoSeleccionado->apellidos }} {{ $medicoSeleccionado->nombres }}
                        </div>
                        <div>
                            {{ $medicoSeleccionado->especialidad->nombre }} &middot; {{ $medicoSeleccionado->consultorio }}
                        </div>
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

            {{-- Barra de consulta por médico y fecha --}}
            <div class="tarjeta-panel-mate p-4 mb-4">
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

                {{-- Resumen numérico de la agenda consultada --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="kpi-panel-mate p-3 h-100">
                            <span class="kpi-etiqueta d-block mb-1">Total turnos</span>
                            <div class="h3 fw-bold text-hrc-primario mb-0">{{ $totalAgenda }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="kpi-panel-mate p-3 h-100">
                            <span class="kpi-etiqueta d-block mb-1">Pendientes</span>
                            <div class="h3 fw-bold text-hrc-ocre mb-0">{{ $pendientesAgenda }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="kpi-panel-mate p-3 h-100">
                            <span class="kpi-etiqueta d-block mb-1">Atendidas</span>
                            <div class="h3 fw-bold text-hrc-salvia mb-0">{{ $atendidasAgenda }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="kpi-panel-mate p-3 h-100">
                            <span class="kpi-etiqueta d-block mb-1">Canceladas</span>
                            <div class="h3 fw-bold text-muted mb-0">{{ $canceladasAgenda }}</div>
                        </div>
                    </div>
                </div>

                {{-- Tabla operativa de citas --}}
                <div class="tarjeta-panel-mate overflow-hidden">
                    <div class="panel-cabecera-tabla px-4 py-3 d-flex flex-column gap-3">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <h2 class="h5 fw-bold text-hrc-primario mb-0">
                                    Pacientes programados &middot; {{ $fechaFormateada }}
                                </h2>
                                @if ($medicoSeleccionado)
                                    <span class="texto-xs text-muted">
                                        Consultorio: {{ $medicoSeleccionado->consultorio }}
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
                        <table class="table tabla-mate align-middle mb-0" id="tabla-agenda-admision">
                            <thead>
                                <tr>
                                    <th class="ps-4 py-3">Orden</th>
                                    <th class="py-3">Hora</th>
                                    <th class="py-3">Paciente</th>
                                    <th class="py-3">Modalidad</th>
                                    <th class="py-3">Estado</th>
                                    <th class="pe-4 py-3 text-end">Gestión operativa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($citas as $index => $cita)
                                    @php
                                        $nombreCompleto = strtoupper($cita->paciente->apellido_paterno . ' ' . $cita->paciente->apellido_materno) . ', ' . $cita->paciente->nombres;
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
                                        <td class="ps-4 py-3 small fw-bold text-muted text-nowrap">
                                            #{{ $index + 1 }}
                                        </td>

                                        <td class="py-3 text-nowrap">
                                            <span class="hora-agenda-mate">{{ $horaTexto }}</span>
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
                <div class="tarjeta-panel-mate text-center py-5 px-4">
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