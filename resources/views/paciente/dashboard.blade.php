@extends('layouts.main')

@section('titulo', 'Mis Citas')
@section('clase_main', 'p-0')

@section('contenido')
    @php
        $usuario = Auth::user();
        $iniciales = mb_strtoupper(
            mb_substr(trim($usuario->nombres), 0, 1, 'UTF-8') .
            mb_substr(trim($usuario->apellido_paterno), 0, 1, 'UTF-8'),
            'UTF-8'
        );

        $totalActivas = $citasActivas->count();
        $totalHistorial = $historialCitas->count();
        $limiteCitas = 3;
        $porcentajeCupo = min(100, (int) round(($totalActivas / $limiteCitas) * 100));
        $limiteAlcanzado = $totalActivas >= $limiteCitas;

        // Abreviaturas en español independientes del locale del servidor
        $mesesCortos = [
            1 => 'ENE', 2 => 'FEB', 3 => 'MAR', 4 => 'ABR',
            5 => 'MAY', 6 => 'JUN', 7 => 'JUL', 8 => 'AGO',
            9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC',
        ];
    @endphp

    <section class="cabecera-interna-mate seccion-pizarra py-4">
        <div class="container py-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <span class="sobretitulo-mate d-block mb-1">Portal del paciente</span>
                    <h1 class="h2 fw-bold text-hrc-primario mb-1">
                        Hola, {{ explode(' ', trim($usuario->nombres))[0] }}
                    </h1>
                    <p class="text-muted small mb-0">
                        Consulta tus citas programadas, descarga tus constancias o revisa tu historial reciente.
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('guia.contacto') }}" class="btn btn-outline-hrc-primario rounded-pill px-4">
                        Guía de Atención
                    </a>
                    @if ($limiteAlcanzado)
                        <button type="button"
                                class="btn btn-hrc-acento rounded-pill px-4"
                                disabled
                                title="Ya tienes el máximo de {{ $limiteCitas }} citas pendientes activas.">
                            Límite de citas alcanzado
                        </button>
                    @else
                        <a href="{{ route('reservar_cita') }}" class="btn btn-hrc-acento rounded-pill px-4">
                            <i class="bi bi-calendar-plus me-1"></i> Nueva Cita
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4 align-items-start">

                {{-- Columna lateral: ficha del paciente y estado del límite de reservas --}}
                <div class="col-lg-4">
                    <div class="sidebar-pegajoso-hrc d-flex flex-column gap-4">
                        <div class="card border-0 tarjeta-articulo-mate">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div class="avatar-paciente-mate d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0">
                                        {{ $iniciales }}
                                    </div>
                                    <div class="min-w-0">
                                        <h2 class="h5 fw-bold text-hrc-primario mb-1 text-truncate">
                                            {{ $usuario->nombres }} {{ $usuario->apellido_paterno }}
                                        </h2>
                                        <div class="small text-muted">
                                            {{ $usuario->tipo_documento }}: {{ $usuario->numero_documento }}
                                        </div>
                                    </div>
                                </div>

                                @if (!empty($usuario->telefono) || !empty($usuario->correo))
                                    <ul class="list-unstyled small text-muted mb-4 pb-3 border-bottom d-flex flex-column gap-2">
                                        @if (!empty($usuario->telefono))
                                            <li class="d-flex align-items-center gap-2">
                                                <i class="bi bi-telephone text-hrc-acento"></i>
                                                <span>{{ $usuario->telefono }}</span>
                                            </li>
                                        @endif
                                        @if (!empty($usuario->correo))
                                            <li class="d-flex align-items-center gap-2 text-truncate">
                                                <i class="bi bi-envelope text-hrc-acento flex-shrink-0"></i>
                                                <span class="text-truncate">{{ $usuario->correo }}</span>
                                            </li>
                                        @endif
                                    </ul>
                                @endif

                                {{-- Indicador de cuota de citas pendientes simultáneas --}}
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center small mb-2">
                                        <span class="fw-semibold text-hrc-primario">Cupo de citas activas</span>
                                        <span class="fw-bold {{ $limiteAlcanzado ? 'text-hrc-acento' : 'text-hrc-salvia' }}">
                                            {{ $totalActivas }} / {{ $limiteCitas }}
                                        </span>
                                    </div>
                                    <div class="barra-cupo-mate" role="progressbar" aria-valuenow="{{ $totalActivas }}" aria-valuemin="0" aria-valuemax="{{ $limiteCitas }}">
                                        <div class="barra-cupo-relleno {{ $limiteAlcanzado ? 'al-limite' : '' }}" style="width: {{ $porcentajeCupo }}%;"></div>
                                    </div>
                                    <div class="texto-xs text-muted mt-2">
                                        Máximo 1 cita pendiente por especialidad y hasta {{ $limiteCitas }} en simultáneo.
                                    </div>
                                </div>

                                <div class="row g-2 text-center">
                                    <div class="col-6">
                                        <div class="resumen-contador-mate p-3">
                                            <div class="h4 fw-bold text-hrc-primario mb-0">{{ $totalActivas }}</div>
                                            <div class="texto-xs text-muted">Próximas</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="resumen-contador-mate p-3">
                                            <div class="h4 fw-bold text-hrc-primario mb-0">{{ $totalHistorial }}</div>
                                            <div class="texto-xs text-muted">Historial</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="nota-asignacion-reserva p-3 d-flex align-items-start gap-2">
                            <i class="bi bi-clock-history text-hrc-primario mt-1 flex-shrink-0"></i>
                            <div class="texto-xs text-muted">
                                <strong class="text-hrc-primario d-block mb-1">Política de cancelación</strong>
                                Puedes cancelar una cita en línea hasta <strong>{{ \App\Models\Cita::HORAS_MINIMAS_CANCELACION }} horas antes</strong> de tu turno para liberar el cupo a otro paciente.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Columna principal: citas próximas e historial --}}
                <div class="col-lg-8">

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

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h4 fw-bold text-hrc-primario mb-0">Mis Citas Próximas</h2>
                        <span class="badge-categoria-mate">{{ $totalActivas }} {{ $totalActivas === 1 ? 'activa' : 'activas' }}</span>
                    </div>

                    <div id="contenedor-citas-activas" class="d-flex flex-column gap-3 mb-5">
                        @forelse($citasActivas as $cita)
                            @php
                                $fechaCarbon = \Carbon\Carbon::parse($cita->fecha);
                                $diaCita = $fechaCarbon->format('d');
                                $mesCita = $mesesCortos[(int) $fechaCarbon->format('n')];
                                $fechaTexto = $fechaCarbon->format('d/m/Y');
                                $horaTexto = \Carbon\Carbon::parse($cita->hora)->format('h:i A');
                            @endphp

                            <article class="card border-0 tarjeta-mate tarjeta-cita-paciente" id="cita-{{ $cita->id }}">
                                <div class="card-body p-4">
                                    <div class="row align-items-center g-3">

                                        {{-- Bloque calendario --}}
                                        <div class="col-sm-3 col-md-3">
                                            <div class="caja-fecha-cita text-center p-3">
                                                <span class="d-block cita-mes-mate">{{ $mesCita }}</span>
                                                <span class="d-block cita-dia-mate">{{ $diaCita }}</span>
                                                <span class="d-block cita-hora-mate mt-1">{{ $horaTexto }}</span>
                                            </div>
                                        </div>

                                        {{-- Detalles clínicos asignados --}}
                                        <div class="col-sm-9 col-md-6">
                                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                                <span class="ticket-pill-estado badge-estado-pendiente">{{ $cita->estado }}</span>
                                                <span class="badge-categoria-mate">
                                                    {{ $cita->codigo_referencia_sis ? 'SIS - Referido' : 'Particular' }}
                                                </span>
                                            </div>

                                            <h3 class="h5 fw-bold text-hrc-primario mb-2">
                                                {{ $cita->medico->especialidad->nombre }}
                                            </h3>

                                            <div class="small text-muted d-flex flex-column gap-1">
                                                <div>
                                                    <i class="bi bi-person-badge me-1 text-hrc-primario"></i>
                                                    <strong>Médico:</strong> Dr. {{ $cita->medico->nombres }} {{ $cita->medico->apellidos }}
                                                </div>
                                                <div>
                                                    <i class="bi bi-geo-alt me-1 text-hrc-acento"></i>
                                                    <strong>Consultorio:</strong> {{ $cita->medico->consultorio }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Acciones operativas --}}
                                        <div class="col-md-3">
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('ticket', $cita->id) }}" class="btn btn-outline-hrc-primario btn-sm rounded-pill">
                                                    <i class="bi bi-ticket-detailed me-1"></i> Ver Ticket
                                                </a>

                                                @if ($cita->puedeCancelarsePorPaciente())
                                                    <button type="button"
                                                            class="btn btn-cancelar-mate btn-sm rounded-pill btn-abrir-cancelar"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalConfirmarCancelacion"
                                                            data-action="{{ route('citas.cancelar', $cita->id) }}"
                                                            data-especialidad="{{ $cita->medico->especialidad->nombre }}"
                                                            data-fecha="{{ $fechaTexto }}"
                                                            data-hora="{{ $horaTexto }}">
                                                        Cancelar cita
                                                    </button>
                                                @else
                                                    <button type="button"
                                                            class="btn btn-outline-secondary btn-sm rounded-pill"
                                                            disabled
                                                            title="Las cancelaciones en línea solo se permiten con al menos {{ \App\Models\Cita::HORAS_MINIMAS_CANCELACION }} horas de anticipación.">
                                                        Cancelación cerrada
                                                    </button>
                                                    <small class="text-muted text-center texto-xs">
                                                        Menos de {{ \App\Models\Cita::HORAS_MINIMAS_CANCELACION }}h restantes
                                                    </small>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="card border-0 tarjeta-articulo-mate text-center py-5 px-4">
                                <div class="card-body">
                                    <div class="icono-vacio-mate mx-auto mb-3">
                                        <i class="bi bi-calendar2-x"></i>
                                    </div>
                                    <h3 class="h5 fw-bold text-hrc-primario mb-2">No tienes citas próximas programadas</h3>
                                    <p class="text-muted small mb-4">
                                        Cuando necesites atención ambulatoria, puedes consultar las especialidades vigentes y agendar en línea.
                                    </p>
                                    <a href="{{ route('reservar_cita') }}" class="btn btn-hrc-acento rounded-pill px-4">
                                        Reservar mi primera cita
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Tabla de historial médico reciente --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h4 fw-bold text-hrc-primario mb-0">Historial Médico Reciente</h2>
                        <span class="small text-muted">{{ $totalHistorial }} {{ $totalHistorial === 1 ? 'registro' : 'registros' }}</span>
                    </div>

                    <div class="card border-0 tarjeta-articulo-mate overflow-hidden">
                        <div class="table-responsive">
                            <table class="table tabla-mate align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4 py-3">Fecha y Hora</th>
                                        <th class="py-3">Especialidad</th>
                                        <th class="py-3">Médico</th>
                                        <th class="py-3">Estado</th>
                                        <th class="pe-4 py-3 text-end">Constancia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($historialCitas as $historial)
                                        @php
                                            $claseBadgeHistorial = match ($historial->estado) {
                                                'Atendido' => 'badge-estado-atendido',
                                                'Cancelado' => 'badge-estado-cancelado',
                                                default => 'etiqueta-sin-cupos-mate',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="ps-4 py-3 text-nowrap">
                                                <div class="fw-semibold text-dark small">
                                                    {{ \Carbon\Carbon::parse($historial->fecha)->format('d/m/Y') }}
                                                </div>
                                                <div class="texto-xs text-muted">
                                                    {{ \Carbon\Carbon::parse($historial->hora)->format('h:i A') }}
                                                </div>
                                            </td>
                                            <td class="fw-bold text-hrc-primario py-3 small">
                                                {{ $historial->medico->especialidad->nombre }}
                                            </td>
                                            <td class="py-3 small text-muted">
                                                Dr. {{ $historial->medico->apellidos }}
                                            </td>
                                            <td class="py-3">
                                                <span class="ticket-pill-estado {{ $claseBadgeHistorial }}">
                                                    {{ $historial->estado }}
                                                </span>
                                            </td>
                                            <td class="pe-4 py-3 text-end">
                                                <a href="{{ route('ticket', $historial->id) }}" class="enlace-mate small fw-bold text-decoration-none">
                                                    Ver <i class="bi bi-arrow-right ms-1"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted small">
                                                Aún no hay registros en tu historial médico.
                                            </td>
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

    {{-- Modal de confirmación para evitar cancelaciones accidentales --}}
    <div class="modal fade" id="modalConfirmarCancelacion" tabindex="-1" aria-labelledby="modalCancelarTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow modal-mate">
                <form id="formConfirmarCancelacion" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold text-hrc-primario" id="modalCancelarTitulo">
                            ¿Cancelar esta cita médica?
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <p class="small text-muted mb-3">
                            Al confirmar, el turno quedará anulado de forma permanente y el cupo será liberado para otro paciente.
                        </p>
                        <div class="resumen-reserva-mate p-3 small">
                            <div class="fw-bold text-hrc-primario mb-1" id="modal-cancelar-especialidad">—</div>
                            <div class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i><span id="modal-cancelar-fecha">—</span>
                                &middot;
                                <i class="bi bi-clock me-1"></i><span id="modal-cancelar-hora">—</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-2">
                        <button type="button" class="btn btn-outline-hrc-primario rounded-pill px-4" data-bs-dismiss="modal">
                            Mantener cita
                        </button>
                        <button type="submit" class="btn btn-hrc-acento rounded-pill px-4" id="btn-confirmar-cancelacion">
                            Sí, cancelar cita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection