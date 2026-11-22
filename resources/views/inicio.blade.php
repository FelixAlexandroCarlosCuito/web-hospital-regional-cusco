@extends('layouts.main')

@section('titulo', 'Inicio')
@section('clase_main', 'p-0')

@section('contenido')
    <section class="hero-cusco position-relative overflow-hidden">
        <div class="container hero-cusco-contenedor position-relative">
            {{-- Motivos geométricos decorativos con desplazamiento continuo de baja amplitud --}}
            <div class="adorno-puntos adorno-hero-superior" aria-hidden="true"></div>
            <div class="adorno-cruz adorno-hero-inferior" aria-hidden="true">
                <i class="bi bi-plus-lg"></i>
            </div>

            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <p class="sobretitulo-mate mb-3 revelar-scroll">
                        Atención integral, oportuna y sin colas
                    </p>

                    <h1 class="hero-titulo-mate fw-bold mb-4 revelar-scroll">
                        Tu cita médica en el Hospital Regional del Cusco
                    </h1>

                    <p class="hero-descripcion-mate mb-4 revelar-scroll">
                        consulta la cartera de servicios vigente y reserva tu turno presencial desde Cusco o cualquier provincia en pocos pasos.
                    </p>

                    <div class="d-flex flex-wrap gap-3 revelar-scroll">
                        <a href="{{ route('reservar_cita') }}" class="btn btn-hrc-acento btn-lg rounded-pill px-4">
                            Reservar Cita Ahora
                        </a>
                        <a href="{{ route('especialidades') }}" class="btn btn-outline-hrc-primario btn-lg rounded-pill px-4">
                            Ver Especialidades
                        </a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="hero-foto-contenedor position-relative ms-lg-auto revelar-scroll">
                        <div class="hero-foto-marco shadow">
                            <img src="{{ asset('img/hospital-regional-foto.png') }}"
                                 alt="Fachada del Hospital Regional del Cusco"
                                 class="w-100 h-100 img-cover">
                        </div>

                        <div class="insignia-foto-flotante d-flex align-items-center gap-3">
                            <div class="insignia-icono-terracota">
                                <i class="bi bi-calendar2-check-fill"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-hrc-primario lh-sm">Consulta Externa</div>
                                <div class="text-muted texto-xs">Lun a Vie &middot; 8:00 a. m. - 1:00 p. m.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Barra unificada de indicadores operativos reales --}}
            <div class="barra-metricas-mate bg-white mt-5 revelar-scroll">
                <div class="row g-4 text-center">
                    <div class="col-6 col-md-3">
                        <div class="metrica-item">
                            <div class="metrica-icono metrica-icono-azul mx-auto mb-2">
                                <i class="bi bi-grid-1x2-fill"></i>
                            </div>
                            <div class="h3 fw-bold text-hrc-primario mb-1">{{ $totalEspecialidades }}</div>
                            <div class="small text-muted">Especialidades activas</div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="metrica-item">
                            <div class="metrica-icono metrica-icono-terracota mx-auto mb-2">
                                <i class="bi bi-person-badge-fill"></i>
                            </div>
                            <div class="h3 fw-bold text-hrc-primario mb-1">{{ $totalMedicos }}</div>
                            <div class="small text-muted">Médicos en consulta</div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="metrica-item">
                            <div class="metrica-icono metrica-icono-salvia mx-auto mb-2">
                                <i class="bi bi-laptop"></i>
                            </div>
                            <div class="h3 fw-bold text-hrc-primario mb-1">24/7</div>
                            <div class="small text-muted">Reservas en línea</div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="metrica-item">
                            <div class="metrica-icono metrica-icono-ocre mx-auto mb-2">
                                <i class="bi bi-hospital-fill"></i>
                            </div>
                            <div class="h3 fw-bold text-hrc-primario mb-1">24 hrs</div>
                            <div class="small text-muted">Servicio de Emergencias</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="seccion-pizarra py-5 position-relative overflow-hidden">
        <div class="container py-4 position-relative">
            <div class="adorno-puntos adorno-esquina-izq" aria-hidden="true"></div>

            <div class="text-center mb-5 revelar-scroll">
                <span class="sobretitulo-mate d-block mb-2">Consulta externa</span>
                <h2 class="fw-bold text-hrc-primario mb-2">Nuestras Especialidades</h2>
                <p class="text-muted mx-auto subtitulo-acotado mb-0">
                    Áreas clínicas disponibles actualmente en el sistema de reservas del hospital.
                </p>
            </div>

            @php
                // Asigna iconos vectoriales por palabra clave para soportar nuevas especialidades sin depender de archivos PNG
                $obtenerIcono = function (string $nombre): string {
                    $clave = mb_strtolower($nombre, 'UTF-8');
                    if (str_contains($clave, 'cardio')) return 'bi-heart-pulse-fill';
                    if (str_contains($clave, 'pediatr')) return 'bi-emoji-smile-fill';
                    if (str_contains($clave, 'trauma')) return 'bi-bandaid-fill';
                    if (str_contains($clave, 'gineco')) return 'bi-shield-plus';
                    if (str_contains($clave, 'oftalmo')) return 'bi-eye-fill';
                    if (str_contains($clave, 'medicina')) return 'bi-clipboard2-pulse-fill';
                    return 'bi-hospital-fill';
                };
            @endphp

            <div class="row g-4">
                @foreach ($especialidadesDestacadas as $index => $esp)
                    <div class="col-md-6 col-lg-4 revelar-scroll" data-delay="{{ ($index % 3) * 80 }}">
                        <div class="card border-0 h-100 p-4 tarjeta-mate tarjeta-interactiva text-start">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                <div class="icono-interactivo">
                                    <i class="bi {{ $obtenerIcono($esp->nombre) }}"></i>
                                </div>
                                <span class="etiqueta-medicos-mate">
                                    {{ $esp->medicos_activos_count > 0 ? 'Turnos habilitados' : 'Sin turnos activos' }}
                                </span>
                            </div>

                            <h5 class="fw-bold text-hrc-primario mb-2">{{ $esp->nombre }}</h5>

                            <p class="text-muted small mb-4 flex-grow-1">
                                {{ $esp->descripcion ?: 'Atención ambulatoria especializada con reserva previa en consultorios externos.' }}
                            </p>

                            <a href="{{ route('reservar_cita', ['especialidad' => $esp->id]) }}" class="enlace-mate fw-bold small text-decoration-none stretched-link">
                                Solicitar turno <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-5 revelar-scroll">
                <a href="{{ route('especialidades') }}" class="btn btn-outline-hrc-primario rounded-pill px-4">
                    Explorar catálogo completo
                </a>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white position-relative overflow-hidden">
        <div class="container py-4 position-relative">
            <div class="adorno-puntos adorno-esquina-der" aria-hidden="true"></div>
            <div class="adorno-anillo adorno-pasos-izq" aria-hidden="true"></div>

            <div class="text-center mb-5 revelar-scroll">
                <span class="sobretitulo-mate d-block mb-2">Flujo de reserva</span>
                <h2 class="fw-bold text-hrc-primario mb-2">¿Cómo sacar tu cita?</h2>
                <p class="text-muted mx-auto subtitulo-acotado mb-0">
                    Un procedimiento directo para confirmar tu turno antes de acercarte al hospital.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4 revelar-scroll" data-delay="0">
                    <div class="card border-0 h-100 p-4 tarjeta-mate tarjeta-interactiva tarjeta-paso-mate text-start">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="icono-interactivo icono-interactivo-azul">
                                <i class="bi bi-person-vcard-fill"></i>
                            </div>
                            <span class="paso-etiqueta">Paso 01</span>
                        </div>
                        <h5 class="fw-bold text-hrc-primario mb-2">Valida tu identidad</h5>
                        <p class="text-muted small mb-0">
                            Crea tu cuenta una sola vez con los datos de tu DNI físico para asegurar tu registro clínico.
                        </p>
                    </div>
                </div>

                <div class="col-md-4 revelar-scroll" data-delay="100">
                    <div class="card border-0 h-100 p-4 tarjeta-mate tarjeta-interactiva tarjeta-paso-mate text-start">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="icono-interactivo icono-interactivo-terracota">
                                <i class="bi bi-clock-fill"></i>
                            </div>
                            <span class="paso-etiqueta">Paso 02</span>
                        </div>
                        <h5 class="fw-bold text-hrc-primario mb-2">Elige especialidad y turno</h5>
                        <p class="text-muted small mb-0">
                            Selecciona la especialidad y el horario disponible.
                        </p>
                    </div>
                </div>

                <div class="col-md-4 revelar-scroll" data-delay="200">
                    <div class="card border-0 h-100 p-4 tarjeta-mate tarjeta-interactiva tarjeta-paso-mate text-start">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="icono-interactivo icono-interactivo-salvia">
                                <i class="bi bi-ticket-detailed-fill"></i>
                            </div>
                            <span class="paso-etiqueta">Paso 03</span>
                        </div>
                        <h5 class="fw-bold text-hrc-primario mb-2">Acude con tu ticket</h5>
                        <p class="text-muted small mb-0">
                            Presenta tu comprobante digital o impreso 15 minutos antes junto con tu DNI y referencia SIS si aplica.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($ultimasNovedades->isNotEmpty())
        <section class="seccion-piedra py-5">
            <div class="container py-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5 gap-3 revelar-scroll">
                    <div>
                        <span class="sobretitulo-mate d-block mb-2">Actualidad institucional</span>
                        <h2 class="fw-bold text-hrc-primario mb-1">Últimas Novedades y Campañas</h2>
                        <p class="text-muted mb-0">Comunicados oficiales publicados por la administración del hospital.</p>
                    </div>
                    <a href="{{ route('novedades.index') }}" class="btn btn-outline-hrc-primario rounded-pill px-4">
                        Ver todas las novedades
                    </a>
                </div>

                <div class="row g-4">
                    @foreach ($ultimasNovedades as $index => $novedad)
                        <div class="col-md-6 col-lg-4 revelar-scroll" data-delay="{{ $index * 90 }}">
                            @include('novedades._tarjeta', ['novedad' => $novedad])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="bloque-dividido-mate overflow-hidden revelar-scroll">
                <div class="row g-0">
                    <div class="col-lg-5 panel-dividido-azul text-white p-4 p-lg-5 d-flex flex-column justify-content-center">
                        <span class="etiqueta-panel-oscuro mb-3">Información al paciente</span>
                        <h2 class="h3 fw-bold mb-3">Atención y canales oficiales</h2>
                        <p class="texto-claro-mate small mb-4">
                            Preséntate en el área de admisión 15 minutos antes de tu cita programada portando tu documento de identidad original.
                        </p>

                        <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small">
                            <li class="d-flex align-items-start gap-3">
                                <i class="bi bi-telephone-fill icono-panel-oscuro mt-1"></i>
                                <div>
                                    <div class="texto-claro-mate">Central Telefónica</div>
                                    <div class="fw-bold">(084) 231131</div>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3">
                                <i class="bi bi-geo-alt-fill icono-panel-oscuro mt-1"></i>
                                <div>
                                    <div class="texto-claro-mate">Ubicación</div>
                                    <div class="fw-bold">Av. de La Cultura S/N, Cusco</div>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3">
                                <i class="bi bi-clock-fill icono-panel-oscuro mt-1"></i>
                                <div>
                                    <div class="texto-claro-mate">Emergencias</div>
                                    <div class="fw-bold">Lunes a Domingo &middot; 24 horas</div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg-7 bg-white p-4 p-lg-5 d-flex flex-column justify-content-center">
                        <span class="sobretitulo-mate mb-2">Antes de acudir</span>
                        <h3 class="h4 fw-bold text-hrc-primario mb-3">Requisitos esenciales para tu consulta</h3>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="caja-requisito-mate p-3 h-100">
                                    <div class="fw-bold text-hrc-primario small mb-1">
                                        <i class="bi bi-check-circle-fill text-hrc-acento me-1"></i> Pacientes SIS
                                    </div>
                                    <p class="text-muted texto-xs mb-0">
                                        DNI vigente, hoja de referencia emitida por tu establecimiento de origen y código de referencia.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="caja-requisito-mate p-3 h-100">
                                    <div class="fw-bold text-hrc-primario small mb-1">
                                        <i class="bi bi-check-circle-fill text-hrc-salvia me-1"></i> Pacientes Regulares
                                    </div>
                                    <p class="text-muted texto-xs mb-0">
                                        DNI o Carnet de Extranjería original y ticket de reserva en el celular o impreso.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('reservar_cita') }}" class="btn btn-hrc-acento rounded-pill px-4">
                                Reservar Cita Ahora
                            </a>
                            <a href="{{ route('guia.contacto') }}" class="btn btn-outline-hrc-primario rounded-pill px-4">
                                Ver Guía y Preguntas Frecuentes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('js/inicio.js') }}"></script>
@endsection