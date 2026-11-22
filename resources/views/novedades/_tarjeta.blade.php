@php
    // Abreviaturas locales para no depender de la configuración regional del servidor
    $mesesCortos = [
        1 => 'ENE', 2 => 'FEB', 3 => 'MAR', 4 => 'ABR',
        5 => 'MAY', 6 => 'JUN', 7 => 'JUL', 8 => 'AGO',
        9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC',
    ];
    $fechaItem = $novedad->fecha_publicacion ?? $novedad->created_at;
    $diaItem = $fechaItem->format('d');
    $mesItem = $mesesCortos[(int) $fechaItem->format('n')];
@endphp

<article class="tarjeta-relevante">
    <div class="relevante-media">
        @if ($novedad->imagen)
            <img src="{{ asset('storage/' . $novedad->imagen) }}"
                 alt="{{ $novedad->titulo }}"
                 class="relevante-img">
        @else
            <div class="relevante-placeholder d-flex align-items-center justify-content-center">
                <i class="bi bi-hospital fs-1 text-white opacity-50"></i>
            </div>
        @endif

        <div class="relevante-fecha-badge" aria-label="Publicado el {{ $fechaItem->format('d/m/Y') }}">
            <span class="relevante-dia">{{ $diaItem }}</span>
            <span class="relevante-mes">{{ $mesItem }}</span>
        </div>
    </div>

    <div class="relevante-cuerpo">
        <span class="relevante-categoria">{{ $novedad->categoria }}</span>

        <h3 class="relevante-titulo h5 fw-bold text-hrc-primario mb-1">
            {{ $novedad->titulo }}
        </h3>

        <div class="relevante-subtitulo small fw-semibold mb-0">
            Hospital Regional del Cusco
        </div>

        <div class="relevante-desplegable">
            <div class="relevante-desplegable-interior">
                <p class="relevante-resumen small text-muted mb-2">
                    {{ \Illuminate\Support\Str::limit($novedad->resumen, 150) }}
                </p>
                <span class="relevante-cta small fw-bold">
                    Leer comunicado <i class="bi bi-arrow-right ms-1"></i>
                </span>
            </div>
        </div>
    </div>

    {{-- Capa de enlace directa sobre el artículo para hacer clickeable toda la tarjeta, incluida la imagen y la fecha --}}
    <a href="{{ route('novedades.show', $novedad->slug) }}"
       class="enlace-cobertura-tarjeta"
       aria-label="Leer comunicado: {{ $novedad->titulo }}"></a>
</article>