@extends('layouts.main')

@section('titulo', 'Novedades del Hospital')
@section('clase_main', 'p-0')

@section('contenido')
    <section class="cabecera-interna-mate seccion-pizarra py-5">
        <div class="container py-2 text-center">
            <span class="sobretitulo-mate d-block mb-2">Actualidad institucional</span>
            <h1 class="fw-bold text-hrc-primario mb-2">Novedades y Comunicados</h1>
            <p class="text-muted mx-auto subtitulo-acotado mb-4">
                Campañas de salud, horarios especiales y avisos oficiales publicados por el Hospital Regional del Cusco.
            </p>

            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="{{ route('novedades.index') }}"
                   class="filtro-pildora-mate {{ !$categoriaActual ? 'activo' : '' }}">
                    Todas
                </a>
                @foreach ($categorias as $cat)
                    <a href="{{ route('novedades.index', ['categoria' => $cat]) }}"
                       class="filtro-pildora-mate {{ $categoriaActual === $cat ? 'activo' : '' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            @if ($novedades->isEmpty())
                <div class="card border-0 tarjeta-mate text-center py-5 px-4">
                    <div class="card-body">
                        <div class="icono-vacio-mate mx-auto mb-3">
                            <i class="bi bi-newspaper"></i>
                        </div>
                        <h5 class="fw-bold text-hrc-primario mb-2">No hay novedades publicadas en esta categoría</h5>
                        <p class="text-muted small mb-3">
                            Vuelve pronto para conocer las próximas campañas y comunicados del hospital.
                        </p>
                        @if ($categoriaActual)
                            <a href="{{ route('novedades.index') }}" class="btn btn-outline-hrc-primario btn-sm rounded-pill px-4">
                                Ver todas las categorías
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="row g-4">
                    @foreach ($novedades as $novedad)
                        <div class="col-md-6 col-lg-4">
                            @include('novedades._tarjeta', ['novedad' => $novedad])
                        </div>
                    @endforeach
                </div>

                @if ($novedades->hasPages())
                    <div class="d-flex justify-content-center mt-5 paginacion-hrc">
                        {{ $novedades->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection