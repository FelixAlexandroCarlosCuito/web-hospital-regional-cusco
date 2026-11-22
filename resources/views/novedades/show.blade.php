@extends('layouts.main')

@section('titulo', $novedad->titulo)

@section('contenido')
<div class="container">
    <div class="row g-4">

        <div class="col-lg-8">
            <a href="{{ route('novedades.index') }}" class="text-decoration-none text-hrc-primario fw-bold d-inline-block mb-3">
                &larr; Volver a Novedades
            </a>

            <article class="card border-0 shadow-sm overflow-hidden">
                @if ($novedad->imagen)
                    <img src="{{ asset('storage/' . $novedad->imagen) }}"
                         alt="{{ $novedad->titulo }}"
                         class="w-100"
                         style="max-height: 400px; object-fit: cover;">
                @endif

                <div class="card-body p-4 p-md-5">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <span class="badge bg-info text-dark">{{ $novedad->categoria }}</span>
                        <span class="text-muted small">
                            Publicado el {{ optional($novedad->fecha_publicacion)->format('d/m/Y h:i A') }}
                        </span>
                    </div>

                    <h1 class="h3 fw-bold text-hrc-primario mb-3">{{ $novedad->titulo }}</h1>
                    <p class="lead text-secondary fs-6 mb-4">{{ $novedad->resumen }}</p>

                    <hr class="my-4">

                    {{-- Se escapa el texto antes de convertir saltos de línea para prevenir inyección XSS --}}
                    <div class="text-dark" style="line-height: 1.8;">
                        {!! nl2br(e($novedad->contenido)) !!}
                    </div>
                </div>
            </article>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-hrc-primario mb-0">Otras Novedades Recientes</h6>
                </div>
                <div class="list-group list-group-flush">
                    @forelse ($recientes as $item)
                        <a href="{{ route('novedades.show', $item->slug) }}" class="list-group-item list-group-item-action py-3">
                            <div class="small text-muted mb-1">
                                {{ $item->categoria }} &middot; {{ optional($item->fecha_publicacion)->format('d/m/Y') }}
                            </div>
                            <div class="fw-bold text-hrc-primario small">{{ $item->titulo }}</div>
                        </a>
                    @empty
                        <div class="p-4 text-center text-muted small">
                            No hay más comunicados publicados.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection