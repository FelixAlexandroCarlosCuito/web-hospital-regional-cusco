@extends('layouts.main')

@section('titulo', 'Demasiadas Solicitudes')

@section('contenido')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-6">
                <div class="panel-card-elevada p-4 p-md-5 text-center">
                    <div class="kpi-icono-cuadro kpi-icono-terracota mx-auto mb-3 error-icono-grande">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>

                    <span class="sobretitulo-mate d-block mb-1">Protección contra abuso &middot; Error 429</span>

                    <h1 class="h4 fw-bold text-hrc-primario mb-2">
                        Demasiados intentos en poco tiempo
                    </h1>

                    <p class="small text-muted mb-4">
                        Se ha superado el límite temporal de solicitudes permitidas por minuto. Por medidas de seguridad del portal, espere un momento antes de volver a intentarlo.
                    </p>

                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="{{ route('inicio') }}" class="btn btn-hrc-acento rounded-pill px-4">
                            Volver al Inicio
                        </a>
                        <a href="{{ route('guia.contacto') }}" class="btn btn-outline-hrc-primario rounded-pill px-4">
                            Guía y Contacto
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection