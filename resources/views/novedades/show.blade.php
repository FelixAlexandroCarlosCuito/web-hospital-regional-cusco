@extends('layouts.main')

@section('titulo', $novedad->titulo)

@section('contenido')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('novedades.index') }}" class="enlace-volver-mate text-decoration-none small fw-bold d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Volver a Novedades</span>
        </a>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <article class="card border-0 tarjeta-articulo-mate overflow-hidden">
                @if ($novedad->imagen)
                    <div class="articulo-portada-wrap">
                        <img src="{{ asset('storage/' . $novedad->imagen) }}"
                             alt="{{ $novedad->titulo }}"
                             class="w-100 h-100 img-cover">
                    </div>
                @endif

                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                        <span class="badge-categoria-mate">{{ $novedad->categoria }}</span>
                        <span class="text-muted small d-inline-flex align-items-center gap-1">
                            <i class="bi bi-calendar3"></i>
                            <span>{{ optional($novedad->fecha_publicacion)->format('d/m/Y h:i A') }}</span>
                        </span>
                        @if ($novedad->autor)
                            <span class="text-muted small d-inline-flex align-items-center gap-1">
                                <i class="bi bi-building"></i>
                                <span>Administración HRC</span>
                            </span>
                        @endif
                    </div>

                    <h1 class="h2 fw-bold text-hrc-primario mb-4 lh-sm">{{ $novedad->titulo }}</h1>

                    <div class="articulo-resumen-mate p-3 p-md-4 mb-4">
                        <p class="mb-0 text-hrc-primario">{{ $novedad->resumen }}</p>
                    </div>

                    {{-- Se escapa el texto plano antes de convertir saltos de línea para prevenir inyección XSS --}}
                    <div class="articulo-cuerpo-mate">
                        {!! nl2br(e($novedad->contenido)) !!}
                    </div>
                </div>
            </article>
        </div>

        <div class="col-lg-4">
            <div class="sidebar-pegajoso-hrc d-flex flex-column gap-4">
                <div class="card border-0 tarjeta-articulo-mate overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold text-hrc-primario mb-0">Otras Novedades Recientes</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse ($recientes as $item)
                            <a href="{{ route('novedades.show', $item->slug) }}" class="list-group-item list-group-item-action item-reciente-mate py-3 px-4">
                                <div class="d-flex align-items-center gap-3">
                                    @if ($item->imagen)
                                        <img src="{{ asset('storage/' . $item->imagen) }}"
                                             alt="{{ $item->titulo }}"
                                             class="miniatura-reciente img-cover flex-shrink-0">
                                    @else
                                        <div class="miniatura-reciente miniatura-placeholder d-flex align-items-center justify-content-center flex-shrink-0">
                                            <i class="bi bi-newspaper text-hrc-primario"></i>
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <div class="texto-xs text-muted mb-1">
                                            {{ $item->categoria }} &middot; {{ optional($item->fecha_publicacion)->format('d/m/Y') }}
                                        </div>
                                        <div class="fw-bold text-hrc-primario small lh-sm">{{ $item->titulo }}</div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted small">
                                No hay más comunicados publicados.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="panel-dividido-azul text-white p-4 tarjeta-cta-lateral">
                    <span class="etiqueta-panel-oscuro d-block mb-2">Consulta externa</span>
                    <h6 class="fw-bold mb-2">¿Necesitas programar una atención?</h6>
                    <p class="texto-claro-mate small mb-3">
                        Reserva tu cita en línea en las especialidades activas sin acudir de madrugada al hospital.
                    </p>
                    <a href="{{ route('reservar_cita') }}" class="btn btn-hrc-acento btn-sm rounded-pill w-100">
                        Reservar Cita Ahora
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection