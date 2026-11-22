@extends('layouts.main')

@section('titulo', 'Especialidades y Personal Médico')

@section('contenido')
<div class="container">

    <div class="text-center mb-4">
        <h1 class="fw-bold text-hrc-primario mb-2">Especialidades y Personal Médico</h1>
        <p class="text-muted mb-0">
            Consultorios externos activos y médicos disponibles para reserva en línea
        </p>
    </div>

    <div class="row justify-content-center mb-4">
        <div class="col-md-7 col-lg-5">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text"
                       id="buscador-especialidades"
                       class="form-control border-start-0"
                       placeholder="Buscar por especialidad o nombre del médico..."
                       aria-label="Buscar especialidad o médico">
            </div>
        </div>
    </div>

    @if ($especialidades->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <h5 class="fw-bold text-secondary mb-2">No hay especialidades activas en este momento</h5>
                <p class="text-muted mb-0">Consulte nuevamente más tarde o comuníquese con Admisión.</p>
            </div>
        </div>
    @else
        <div class="row g-4" id="lista-especialidades">
            @foreach ($especialidades as $esp)
                @php
                    $textoMedicos = $esp->medicos->map(fn ($m) => "{$m->nombres} {$m->apellidos}")->implode(' ');
                    $indiceBusqueda = trim("{$esp->nombre} {$esp->descripcion} {$textoMedicos}");
                @endphp

                <div class="col-md-6 col-lg-4 item-especialidad" data-busqueda="{{ $indiceBusqueda }}">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column p-4">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <h5 class="fw-bold text-hrc-primario mb-0">{{ $esp->nombre }}</h5>
                                <span class="badge bg-light text-dark border">
                                    {{ $esp->medicos->count() }} {{ $esp->medicos->count() === 1 ? 'médico' : 'médicos' }}
                                </span>
                            </div>

                            <p class="text-muted small mb-3">
                                {{ $esp->descripcion ?: 'Atención ambulatoria en consulta externa previa reserva de cita.' }}
                            </p>

                            <div class="border-top pt-3 mb-3 flex-grow-1">
                                <div class="small fw-bold text-secondary mb-2">
                                    <i class="bi bi-person-badge me-1"></i> Personal activo
                                </div>

                                @forelse ($esp->medicos as $medico)
                                    <div class="small mb-2">
                                        <div class="fw-bold text-dark">Dr. {{ $medico->nombres }} {{ $medico->apellidos }}</div>
                                        <div class="text-muted">{{ $medico->consultorio }}</div>
                                    </div>
                                @empty
                                    <p class="small text-muted mb-0">Sin médicos asignados temporalmente.</p>
                                @endforelse
                            </div>

                            @if ($esp->medicos->isNotEmpty())
                                <a href="{{ route('reservar_cita') }}" class="btn btn-outline-hrc-primario btn-sm mt-auto">
                                    Reservar en esta especialidad
                                </a>
                            @else
                                <button type="button" class="btn btn-outline-secondary btn-sm mt-auto" disabled>
                                    Sin turnos habilitados
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="sin-resultados-especialidades" class="card border-0 shadow-sm text-center py-5 d-none">
            <div class="card-body">
                <h5 class="fw-bold text-secondary mb-2">Sin coincidencias</h5>
                <p class="text-muted mb-0">No se encontraron especialidades ni médicos con ese criterio de búsqueda.</p>
            </div>
        </div>
    @endif

</div>
@endsection

@section('js')
    <script src="{{ asset('js/especialidades.js') }}"></script>
@endsection