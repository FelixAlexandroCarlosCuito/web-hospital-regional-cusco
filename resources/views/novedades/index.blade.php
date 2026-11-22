@extends('layouts.main')

@section('titulo', 'Novedades del Hospital')

@section('contenido')
<div class="container">

    <div class="text-center mb-5">
        <h1 class="fw-bold text-hrc-primario mb-2">Novedades y Comunicados</h1>
        <p class="text-muted mb-0">Campañas de salud, horarios especiales y avisos oficiales del Hospital Regional del Cusco</p>
    </div>

    <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
        <a href="{{ route('novedades.index') }}"
           class="btn btn-sm {{ !$categoriaActual ? 'btn-hrc-acento' : 'btn-outline-secondary' }}">
            Todas
        </a>
        @foreach ($categorias as $cat)
            <a href="{{ route('novedades.index', ['categoria' => $cat]) }}"
               class="btn btn-sm {{ $categoriaActual === $cat ? 'btn-hrc-acento' : 'btn-outline-secondary' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>

    @if ($novedades->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <h5 class="fw-bold text-secondary mb-2">No hay novedades publicadas en esta sección</h5>
                <p class="text-muted mb-0">Vuelve pronto para conocer las próximas campañas y comunicados del hospital.</p>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach ($novedades as $novedad)
                <div class="col-md-6 col-lg-4">
                    <article class="card border-0 shadow-sm h-100 overflow-hidden">
                        @if ($novedad->imagen)
                            <img src="{{ asset('storage/' . $novedad->imagen) }}"
                                 class="card-img-top"
                                 alt="{{ $novedad->titulo }}"
                                 style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center text-muted border-bottom"
                                 style="height: 200px;">
                                <span class="fw-bold text-hrc-primario">Hospital Regional del Cusco</span>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-info text-dark">{{ $novedad->categoria }}</span>
                                <small class="text-muted">
                                    {{ optional($novedad->fecha_publicacion)->format('d/m/Y') }}
                                </small>
                            </div>

                            <h5 class="card-title fw-bold text-hrc-primario mb-2">{{ $novedad->titulo }}</h5>
                            <p class="card-text text-muted small flex-grow-1">{{ $novedad->resumen }}</p>

                            <a href="{{ route('novedades.show', $novedad->slug) }}" class="btn btn-outline-hrc-primario btn-sm mt-3">
                                Leer comunicado &rarr;
                            </a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $novedades->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection