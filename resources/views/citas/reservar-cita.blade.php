@extends('layouts.main')

@section('titulo', 'Reservar Cita')
@section('clase_main', 'p-0')

@section('contenido')
    @php
        $limiteAlcanzado = ($citasPendientesTotal ?? 0) >= 3;
        $espSeleccionada = old('especialidad_id', $especialidadPreseleccionada ?? '');
    @endphp

    <section class="cabecera-interna-mate seccion-pizarra py-5">
        <div class="container py-2 text-center">
            <span class="sobretitulo-mate d-block mb-2">Consulta externa</span>
            <h1 class="fw-bold text-hrc-primario mb-2">Reserva tu Cita Médica</h1>
            <p class="text-muted mx-auto subtitulo-acotado mb-0">
                Elige la especialidad y el horario que mejor te convenga. El profesional y consultorio se asignarán automáticamente al confirmar tu turno.
            </p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">

                    <nav aria-label="Progreso de la reserva" class="mb-4">
                        <ol class="barra-pasos barra-pasos-mate list-unstyled d-flex flex-row justify-content-between gap-2 gap-md-3 mb-0">
                            <li class="activo flex-fill text-center p-3">
                                <div class="badge-paso d-inline-flex align-items-center justify-content-center rounded-circle mb-2 fw-bold">1</div>
                                <span class="d-block fw-bold small">Especialidad</span>
                            </li>
                            <li class="flex-fill text-center p-3">
                                <div class="badge-paso d-inline-flex align-items-center justify-content-center rounded-circle mb-2 fw-bold">2</div>
                                <span class="d-block fw-bold small">Fecha y Datos</span>
                            </li>
                            <li class="flex-fill text-center p-3">
                                <div class="badge-paso d-inline-flex align-items-center justify-content-center rounded-circle mb-2 fw-bold">3</div>
                                <span class="d-block fw-bold small">Confirmación</span>
                            </li>
                        </ol>
                    </nav>

                    <div class="card border-0 tarjeta-articulo-mate">
                        <div class="card-body p-4 p-md-5">

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                                    <strong class="d-block mb-1">No se pudo completar la reserva:</strong>
                                    <ul class="mb-0 ps-3 small">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                                </div>
                            @endif

                            @if ($limiteAlcanzado)
                                <div class="alerta-mate-aviso d-flex align-items-start gap-3 p-3 mb-4" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill fs-5 mt-1"></i>
                                    <div class="small">
                                        <strong class="d-block mb-1">Límite de citas activas alcanzado</strong>
                                        Ya cuentas con {{ $citasPendientesTotal }} citas pendientes. Debes asistir o cancelar una desde
                                        <a href="{{ route('dashboard') }}" class="fw-bold text-hrc-primario">Mis Citas</a> antes de programar una nueva.
                                    </div>
                                </div>
                            @endif

                            <form id="formulario-reserva" action="{{ route('citas.store') }}" method="POST" novalidate>
                                @csrf

                                {{-- Paso 1: Selección de especialidad y ficha descriptiva dinámica --}}
                                <div id="paso-1" class="fase-reserva">
                                    <div class="pb-3 mb-4 border-bottom">
                                        <span class="paso-etiqueta mb-1">Paso 01</span>
                                        <h4 class="fw-bold text-hrc-primario mb-0">Área de Atención</h4>
                                    </div>

                                    <div class="row g-4 mb-4 align-items-stretch">
                                        <div class="col-md-6 d-flex flex-column">
                                            <label for="especialidad" class="form-label fw-bold text-hrc-primario">
                                                Especialidad médica
                                            </label>
                                            <select id="especialidad"
                                                    name="especialidad_id"
                                                    class="form-select form-select-lg input-mate"
                                                    required
                                                    {{ $limiteAlcanzado ? 'disabled' : '' }}>
                                                <option value="" disabled {{ $espSeleccionada ? '' : 'selected' }}>
                                                    Seleccione una especialidad
                                                </option>
                                                @foreach($especialidades as $especialidad)
                                                    @php
                                                        $ocupada = in_array($especialidad->id, $especialidadesOcupadas ?? []);
                                                        $sinTurnos = !($especialidad->tiene_turnos ?? true);
                                                        $deshabilitada = $ocupada || $sinTurnos;
                                                        $etiquetaExtra = $ocupada
                                                            ? ' (Ya tienes una cita pendiente)'
                                                            : ($sinTurnos ? ' (Sin turnos habilitados)' : '');
                                                    @endphp
                                                    <option value="{{ $especialidad->id }}"
                                                            {{ $deshabilitada ? 'disabled' : '' }}
                                                            {{ !$deshabilitada && (string) $espSeleccionada === (string) $especialidad->id ? 'selected' : '' }}>
                                                        {{ $especialidad->nombre }}{{ $etiquetaExtra }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="mensaje-error d-block text-danger small mt-1" id="error-especialidad"></span>

                                            <div class="nota-asignacion-reserva mt-3 p-3 d-flex align-items-start gap-2">
                                                <i class="bi bi-calendar2-check-fill text-hrc-salvia mt-1 flex-shrink-0"></i>
                                                <div class="texto-xs text-muted">
                                                    <strong class="text-hrc-primario d-block mb-1">Asignación automática por horario</strong>
                                                    Al confirmar tu reserva, el sistema te asignará automáticamente un especialista y consultorio disponibles para el turno que elijas.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div id="ficha-especialidad" class="ficha-especialidad-reserva h-100 p-4 d-flex flex-column justify-content-center">
                                                <div id="ficha-estado-inicial" class="text-muted small">
                                                    <div class="d-flex align-items-center gap-2 text-hrc-primario fw-bold mb-2">
                                                        <i class="bi bi-info-circle-fill text-hrc-acento"></i>
                                                        <span>¿Sobre qué trata cada especialidad?</span>
                                                    </div>
                                                    <p class="mb-0">
                                                        Selecciona un área médica en la lista de la izquierda para ver qué atiende y confirmar que corresponde a tu consulta.
                                                    </p>
                                                </div>

                                                <div id="ficha-estado-detalle" class="d-none">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                        <span class="sobretitulo-mate">Descripción del servicio</span>
                                                        <span class="etiqueta-medicos-mate">Turnos habilitados</span>
                                                    </div>
                                                    <h5 id="ficha-esp-nombre" class="fw-bold text-hrc-primario mb-2"></h5>
                                                    <p id="ficha-esp-descripcion" class="small text-muted mb-3"></p>
                                                    <div class="texto-xs text-muted pt-2 border-top">
                                                        <i class="bi bi-clock me-1 text-hrc-acento"></i>
                                                        Atención presencial de lunes a viernes, de 8:00 a. m. a 1:00 p. m.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end pt-2">
                                        <button type="button"
                                                class="btn btn-hrc-acento btn-lg rounded-pill px-5 btn-siguiente-1"
                                                {{ $limiteAlcanzado ? 'disabled' : '' }}>
                                            Siguiente Paso <i class="bi bi-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Paso 2: Selección de fecha, bloques horarios dinámicos y contacto --}}                                
                                <div id="paso-2" class="fase-reserva" style="display: none;">
                                    <div class="pb-3 mb-4 border-bottom">
                                        <span class="paso-etiqueta mb-1">Paso 02</span>
                                        <h4 class="fw-bold text-hrc-primario mb-0">Fecha, Horario y Datos de Contacto</h4>
                                    </div>

                                    <div class="row g-4 mb-4">
                                        <div class="col-md-6">
                                            <label for="fecha_cita" class="form-label fw-bold text-hrc-primario">Fecha de la Cita</label>
                                            <input type="date"
                                                id="fecha_cita"
                                                name="fecha"
                                                value="{{ old('fecha') }}"
                                                min="{{ now()->toDateString() }}"
                                                class="form-control form-control-lg input-mate"
                                                required>
                                            <div class="form-text texto-xs">Atención en días hábiles (lunes a viernes).</div>
                                            <span class="mensaje-error d-block text-danger small mt-1" id="error-fecha_cita"></span>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-hrc-primario">Especialidad Seleccionada</label>
                                            <div class="campo-lectura-mate d-flex align-items-center justify-content-between gap-2">
                                                <span id="paso2-especialidad-nombre" class="fw-bold text-hrc-primario text-truncate">—</span>
                                                <span class="etiqueta-medicos-mate flex-shrink-0">Asignación automática</span>
                                            </div>
                                            <div class="form-text texto-xs">El médico y consultorio aparecerán en tu ticket de confirmación.</div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-hrc-primario">Horarios Disponibles</label>
                                        <input type="hidden" id="hora_cita" name="hora" value="{{ old('hora') }}" required>

                                        <div id="contenedor-horarios" class="caja-horarios-mate p-3">
                                            <div class="small text-muted">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                Selecciona primero una fecha hábil para ver los cupos disponibles.
                                            </div>
                                        </div>
                                        <span class="mensaje-error d-block text-danger small mt-1" id="error-hora_cita"></span>
                                    </div>

                                    <div class="row g-4 mb-5">
                                        <div class="col-md-6">
                                            <label for="telefono" class="form-label fw-bold text-hrc-primario">Teléfono de Contacto Actual</label>
                                            <input type="tel"
                                                id="telefono"
                                                name="telefono"
                                                value="{{ old('telefono', Auth::user()->telefono) }}"
                                                maxlength="9"
                                                class="form-control form-control-lg input-mate"
                                                required
                                                placeholder="Ej: 987654321">
                                            <span class="mensaje-error d-block text-danger small mt-1" id="error-telefono"></span>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="referencia_sis" class="form-label fw-bold text-hrc-primario">
                                                Hoja de Referencia <span class="fw-normal text-muted">(Solo SIS)</span>
                                            </label>
                                            <input type="text"
                                                id="referencia_sis"
                                                name="codigo_referencia_sis"
                                                value="{{ old('codigo_referencia_sis') }}"
                                                maxlength="50"
                                                class="form-control form-control-lg input-mate"
                                                placeholder="Opcional: Código del centro de salud">
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap justify-content-between gap-2">
                                        <button type="button" class="btn btn-outline-hrc-primario btn-lg rounded-pill px-4 btn-atras-2">
                                            <i class="bi bi-arrow-left me-1"></i> Atrás
                                        </button>
                                        <button type="button" class="btn btn-hrc-acento btn-lg rounded-pill px-5 btn-siguiente-2">
                                            Revisar Resumen <i class="bi bi-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Paso 3: Verificación previa al registro --}}
                                <div id="paso-3" class="fase-reserva" style="display: none;">
                                    <div class="pb-3 mb-4 border-bottom">
                                        <span class="paso-etiqueta mb-1">Paso 03</span>
                                        <h4 class="fw-bold text-hrc-primario mb-0">Confirmación de Datos</h4>
                                    </div>

                                    <div class="resumen-reserva-mate p-4 mb-4">
                                        <ul class="list-group list-group-flush lista-resumen bg-transparent">
                                            <li class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between align-items-center gap-3">
                                                <span class="text-muted small fw-semibold">Paciente</span>
                                                <span id="resumen-paciente" class="text-hrc-primario fw-bold text-end"></span>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between align-items-center gap-3">
                                                <span class="text-muted small fw-semibold">Documento</span>
                                                <span id="resumen-documento" class="text-dark text-end"></span>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between align-items-center gap-3">
                                                <span class="text-muted small fw-semibold">Especialidad</span>
                                                <span id="resumen-especialidad" class="text-hrc-acento fw-bold text-end"></span>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between align-items-center gap-3">
                                                <span class="text-muted small fw-semibold">Médico y consultorio</span>
                                                <span class="etiqueta-medicos-mate text-end">Asignación automática al confirmar</span>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between align-items-center gap-3">
                                                <span class="text-muted small fw-semibold">Fecha programada</span>
                                                <span id="resumen-fecha" class="text-dark fw-semibold text-end"></span>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between align-items-center gap-3">
                                                <span class="text-muted small fw-semibold">Horario</span>
                                                <span id="resumen-hora" class="text-dark fw-semibold text-end"></span>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between align-items-center gap-3">
                                                <span class="text-muted small fw-semibold">Teléfono de contacto</span>
                                                <span id="resumen-telefono" class="text-dark text-end"></span>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between align-items-center gap-3">
                                                <span class="text-muted small fw-semibold">Referencia SIS</span>
                                                <span id="resumen-sis" class="text-dark text-end"></span>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="nota-asignacion-reserva p-3 d-flex align-items-start gap-3 mb-4">
                                        <i class="bi bi-info-circle-fill text-hrc-primario fs-5 flex-shrink-0"></i>
                                        <div class="small text-muted">
                                            Al confirmar, el sistema asignará automáticamente al especialista disponible para tu horario y generará tu ticket con el número de consultorio.
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap justify-content-between gap-2">
                                        <button type="button" class="btn btn-outline-hrc-primario btn-lg rounded-pill px-4 btn-atras-3">
                                            <i class="bi bi-arrow-left me-1"></i> Atrás
                                        </button>
                                        <button type="submit"
                                                class="btn btn-hrc-acento btn-lg rounded-pill px-4 px-md-5 btn-registro"
                                                {{ $limiteAlcanzado ? 'disabled' : '' }}>
                                            Confirmar y Generar Ticket
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script id="datos-reserva-cita" type="application/json">@json($datosReserva)</script>
    <script src="{{ asset('js/reservar-cita.js') }}"></script>
@endsection