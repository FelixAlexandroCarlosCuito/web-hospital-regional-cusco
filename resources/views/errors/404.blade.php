@extends('layouts.main')

@section('titulo', 'Página No Encontrada')

@section('contenido')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-6">
                <div class="panel-card-elevada p-4 p-md-5 text-center">
                    <div class="kpi-icono-cuadro kpi-icono-azul mx-auto mb-3 error-icono-grande">
                        <i class="bi bi-compass"></i>
                    </div>

                    <span class="sobretitulo-mate d-block mb-1">Recurso no encontrado &middot; Error 404</span>

                    <h1 class="h4 fw-bold text-hrc-primario mb-2">
                        No encontramos la página solicitada
                    </h1>

                    <p class="small text-muted mb-4">
                        La dirección ingresada puede estar incompleta o el registro consultado ya no se encuentra disponible en el portal del Hospital Regional del Cusco.
                    </p>

                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="{{ route('inicio') }}" class="btn btn-hrc-acento rounded-pill px-4">
                            Volver al Inicio
                        </a>
                        <a href="{{ route('especialidades') }}" class="btn btn-outline-hrc-primario rounded-pill px-4">
                            Ver Especialidades
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection