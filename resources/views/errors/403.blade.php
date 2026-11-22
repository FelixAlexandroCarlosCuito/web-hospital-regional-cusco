@extends('layouts.main')

@section('titulo', 'Acceso No Autorizado')

@section('contenido')
    @php
        $mensajeOriginal = isset($exception) ? (string) $exception->getMessage() : '';
        $esFirmaInvalida = request()->is('verificar-cita*')
            || str_contains(strtolower($mensajeOriginal), 'signature');

        $rutaDestino = route('inicio');
        $textoDestino = 'Volver al Inicio';

        if (Auth::check()) {
            $rutaDestino = match (Auth::user()->rol) {
                'admin' => route('admin.panel'),
                'admision' => route('admision.panel'),
                'medico' => route('medico.panel'),
                default => route('dashboard'),
            };
            $textoDestino = 'Ir a mi Panel';
        }
    @endphp

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-6">
                <div class="panel-card-elevada p-4 p-md-5 text-center">
                    <div class="kpi-icono-cuadro kpi-icono-terracota mx-auto mb-3 error-icono-grande">
                        <i class="bi {{ $esFirmaInvalida ? 'bi-shield-x' : 'bi-lock-fill' }}"></i>
                    </div>

                    <span class="sobretitulo-mate d-block mb-1">
                        {{ $esFirmaInvalida ? 'Verificación rechazada · Error 403' : 'Acceso restringido · Error 403' }}
                    </span>

                    <h1 class="h4 fw-bold text-hrc-primario mb-2">
                        {{ $esFirmaInvalida ? 'Enlace de constancia inválido o alterado' : 'No tiene permisos para acceder a este recurso' }}
                    </h1>

                    <p class="small text-muted mb-4">
                        @if ($esFirmaInvalida)
                            La firma digital del código QR no coincide con un registro oficial del Hospital Regional del Cusco. Solicite al paciente presentar su constancia emitida desde el portal.
                        @else
                            Su cuenta no cuenta con autorización para visualizar esta sección o documento. Si considera que se trata de un error, comuníquese con Admisión.
                        @endif
                    </p>

                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="{{ $rutaDestino }}" class="btn btn-hrc-acento rounded-pill px-4">
                            {{ $textoDestino }}
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