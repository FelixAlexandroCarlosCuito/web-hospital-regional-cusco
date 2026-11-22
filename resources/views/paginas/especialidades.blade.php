@extends('layouts.main')

@section('titulo', 'Cartera de Especialidades')
@section('clase_main', 'p-0')

@section('contenido')
    @php
        $totalEspecialidades = $especialidades->count();
        $totalHabilitadas = $especialidades->where('tiene_turnos', true)->count();

        // Asigna iconos vectoriales por palabra clave como respaldo cuando no hay fotografía cargada
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

    <section class="cabecera-interna-mate seccion-pizarra py-5">
        <div class="container py-2 text-center">
            <span class="sobretitulo-mate d-block mb-2">Consulta externa</span>
            <h1 class="fw-bold text-hrc-primario mb-2">Cartera de Especialidades</h1>
            <p class="text-muted mx-auto subtitulo-acotado mb-4">
                Servicios ambulatorios disponibles en el Hospital Regional del Cusco. El médico y consultorio se asignan automáticamente al confirmar tu cita.
            </p>

            <div class="row justify-content-center mb-3">
                <div class="col-md-8 col-lg-6">
                    <div class="buscador-mate d-flex align-items-center">
                        <i class="bi bi-search text-muted ms-3"></i>
                        <input type="text"
                               id="buscador-especialidades"
                               class="form-control border-0 bg-transparent shadow-none py-2 px-3"
                               placeholder="Buscar especialidad o servicio (ej. Cardiología, Pediatría)..."
                               aria-label="Buscar especialidad">
                        <button type="button"
                                id="btn-limpiar-input"
                                class="btn btn-sm text-muted border-0 me-2 d-none"
                                aria-label="Limpiar búsqueda">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-center gap-2">
                <button type="button" class="filtro-pildora-mate border-0 btn-filtro-esp activo" data-filtro="todas">
                    Todas ({{ $totalEspecialidades }})
                </button>
                <button type="button" class="filtro-pildora-mate border-0 btn-filtro-esp" data-filtro="disponibles">
                    Con turnos habilitados ({{ $totalHabilitadas }})
                </button>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            @if ($especialidades->isEmpty())
                <div class="card border-0 tarjeta-mate text-center py-5 px-4">
                    <div class="card-body">
                        <div class="icono-vacio-mate mx-auto mb-3">
                            <i class="bi bi-hospital"></i>
                        </div>
                        <h5 class="fw-bold text-hrc-primario mb-2">No hay especialidades activas en este momento</h5>
                        <p class="text-muted small mb-0">
                            Consulte nuevamente más tarde o comuníquese con la central de Admisión.
                        </p>
                    </div>
                </div>
            @else
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
                    <p class="small text-muted mb-0">
                        Mostrando <strong class="text-hrc-primario" id="contador-especialidades">{{ $totalEspecialidades }}</strong> de {{ $totalEspecialidades }} especialidades
                    </p>
                    <span class="texto-xs text-muted">
                        <i class="bi bi-clock me-1 text-hrc-acento"></i>Consultorios externos: Lun a Vie, 8:00 a. m. - 1:00 p. m.
                    </span>
                </div>

                <div class="row g-4" id="lista-especialidades">
                    @foreach ($especialidades as $esp)
                        @php
                            $disponible = (bool) $esp->tiene_turnos;
                            $indiceBusqueda = trim("{$esp->nombre} {$esp->descripcion}");
                        @endphp

                        <div class="col-md-6 col-lg-4 item-especialidad"
                             data-busqueda="{{ $indiceBusqueda }}"
                             data-disponible="{{ $disponible ? '1' : '0' }}">
                            <article class="card border-0 h-100 overflow-hidden tarjeta-mate text-start {{ $disponible ? 'tarjeta-interactiva' : 'tarjeta-especialidad-pausa' }}">
                                {{-- Cabecera multimedia con respaldo vectorial cuando no existe imagen cargada --}}
                                <div class="esp-card-media position-relative">
                                    @if ($esp->imagen)
                                        <img src="{{ asset('storage/' . $esp->imagen) }}"
                                             alt="Especialidad de {{ $esp->nombre }}"
                                             class="esp-card-img"
                                             loading="lazy">
                                    @else
                                        <div class="esp-card-placeholder d-flex align-items-center justify-content-center">
                                            <div class="icono-interactivo {{ $disponible ? '' : 'icono-interactivo-pausa' }}">
                                                <i class="bi {{ $obtenerIcono($esp->nombre) }}"></i>
                                            </div>
                                        </div>
                                    @endif

                                    <span class="esp-card-badge {{ $disponible ? 'etiqueta-medicos-mate' : 'etiqueta-sin-cupos-mate' }}">
                                        {{ $disponible ? 'Turnos habilitados' : 'Sin turnos activos' }}
                                    </span>
                                </div>

                                <div class="card-body p-4 d-flex flex-column">
                                    <h5 class="fw-bold text-hrc-primario mb-2">{{ $esp->nombre }}</h5>

                                    <p class="text-muted small mb-4 flex-grow-1">
                                        {{ $esp->descripcion ?: 'Atención ambulatoria especializada en consulta externa previa reserva de cita.' }}
                                    </p>

                                    <div class="pie-tarjeta-especialidad pt-3 mt-auto">
                                        <div class="texto-xs text-muted mb-2">
                                            <i class="bi bi-shield-check me-1 text-hrc-salvia"></i>
                                            Asignación automática de médico y consultorio
                                        </div>

                                        @if ($disponible)
                                            <a href="{{ route('reservar_cita', ['especialidad' => $esp->id]) }}" class="enlace-mate fw-bold small text-decoration-none stretched-link">
                                                Reservar en esta especialidad <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        @else
                                            <span class="small fw-semibold text-muted">
                                                No disponible temporalmente
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                <div id="sin-resultados-especialidades" class="card border-0 tarjeta-mate text-center py-5 px-4 d-none">
                    <div class="card-body">
                        <div class="icono-vacio-mate mx-auto mb-3">
                            <i class="bi bi-search"></i>
                        </div>
                        <h5 class="fw-bold text-hrc-primario mb-2">Sin coincidencias</h5>
                        <p class="text-muted small mb-3">
                            No se encontraron especialidades que coincidan con el filtro aplicado.
                        </p>
                        <button type="button" id="btn-reset-filtros" class="btn btn-outline-hrc-primario btn-sm rounded-pill px-4">
                            Mostrar todas las especialidades
                        </button>
                    </div>
                </div>
            @endif

            {{-- Aviso operativo sobre la asignación automática y confidencialidad del rol de turnos --}}
            <div class="franja-asignacion-mate mt-5 p-4 p-md-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="sobretitulo-mate d-block mb-1">Asignación institucional</span>
                        <h2 class="h4 fw-bold text-hrc-primario mb-2">¿Por qué no se elige un médico específico al reservar?</h2>
                        <p class="text-muted small mb-0">
                            Para asegurar una distribución equitativa de cupos y evitar inconvenientes por cambios de guardia o emergencias hospitalarias, el sistema asigna automáticamente al especialista disponible en el horario elegido. El profesional y consultorio asignados aparecerán en tu ticket de confirmación.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('guia.contacto') }}" class="btn btn-outline-hrc-primario rounded-pill px-4">
                            Ver Guía de Atención
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('js/especialidades.js') }}"></script>
@endsection