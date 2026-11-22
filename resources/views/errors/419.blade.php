@extends('layouts.main')

@section('titulo', 'Sesión Expirada')

@section('contenido')
    @php
        $urlAnterior = url()->previous();
        $urlReintento = ($urlAnterior && $urlAnterior !== url()->current())
            ? $urlAnterior
            : route('inicio');
    @endphp

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-6">
                <div class="panel-card-elevada p-4 p-md-5 text-center">
                    <div class="kpi-icono-cuadro kpi-icono-azul mx-auto mb-3 error-icono-grande">
                        <i class="bi bi-clock-history"></i>
                    </div>

                    <span class="sobretitulo-mate d-block mb-1">Validación de seguridad &middot; Error 419</span>

                    <h1 class="h4 fw-bold text-hrc-primario mb-2">
                        La sesión del formulario ha expirado
                    </h1>

                    <p class="small text-muted mb-4">
                        Por seguridad, la solicitud fue rechazada debido a que el token de verificación CSRF venció o no es válido. Recargue la página anterior e intente enviar nuevamente sus datos.
                    </p>

                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="{{ $urlReintento }}" class="btn btn-hrc-acento rounded-pill px-4">
                            Volver e intentar de nuevo
                        </a>
                        <a href="{{ route('inicio') }}" class="btn btn-outline-hrc-primario rounded-pill px-4">
                            Ir al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection