@extends('layouts.main')

@section('titulo', 'Verificación de Constancia de Cita')

@section('contenido')
    @php
        $anioEmision = optional($cita->created_at)->format('Y') ?? now()->format('Y');
        $codigoTicket = 'HRC-' . $anioEmision . '-' . str_pad($cita->id, 5, '0', STR_PAD_LEFT);
        $esSis = !empty($cita->codigo_referencia_sis);
        $fechaCita = \Carbon\Carbon::parse($cita->fecha);
        $horaCita = \Carbon\Carbon::parse($cita->hora)->format('h:i A');
        $estaVencida = $cita->estado === 'Pendiente' && $fechaCita->lt(now()->startOfDay());

        if ($cita->estado === 'Cancelado') {
            $estadoVerificacion = [
                'titulo' => 'Constancia Anulada — Sin Validez',
                'detalle' => 'Esta cita fue cancelada en el sistema y el turno ya no se encuentra reservado.',
                'icono' => 'bi-x-octagon-fill',
                'clase' => 'verif-estado-anulada',
                'sello' => 'CANCELADA',
                'claseSello' => 'doc-sello-cancelada',
            ];
        } elseif ($cita->estado === 'Atendido') {
            $estadoVerificacion = [
                'titulo' => 'Cita Registrada como Atendida',
                'detalle' => 'El registro es auténtico y figura como atención ya completada en consultorio.',
                'icono' => 'bi-check2-circle',
                'clase' => 'verif-estado-atendida',
                'sello' => 'ATENDIDA',
                'claseSello' => 'doc-sello-atendida',
            ];
        } elseif ($estaVencida) {
            $estadoVerificacion = [
                'titulo' => 'Constancia con Fecha Vencida',
                'detalle' => 'El registro es auténtico, pero la fecha programada ya transcurrió sin registrar atención.',
                'icono' => 'bi-exclamation-triangle-fill',
                'clase' => 'verif-estado-vencida',
                'sello' => 'VENCIDA',
                'claseSello' => 'doc-sello-vencida',
            ];
        } else {
            $estadoVerificacion = [
                'titulo' => 'Constancia Auténtica y Vigente',
                'detalle' => 'La cita se encuentra activa en la base de datos oficial del Hospital Regional del Cusco.',
                'icono' => 'bi-shield-fill-check',
                'clase' => 'verif-estado-vigente',
                'sello' => 'VIGENTE',
                'claseSello' => 'doc-sello-vigente',
            ];
        }

        // Prefiltra la agenda operativa para reducir pasos del personal al escanear en ventanilla
        $accesoOperativo = null;
        if ($esPersonalInterno) {
            $rolActual = Auth::user()->rol;
            $accesoOperativo = match ($rolActual) {
                'admision' => [
                    'url' => route('admision.panel', [
                        'medico_id' => $cita->medico_id,
                        'fecha' => $fechaCita->toDateString(),
                    ]),
                    'texto' => 'Abrir agenda en Admisión',
                    'icono' => 'bi-clipboard2-pulse',
                ],
                'medico' => [
                    'url' => route('medico.panel', ['fecha' => $fechaCita->toDateString()]),
                    'texto' => 'Abrir mi agenda de ese día',
                    'icono' => 'bi-calendar2-week',
                ],
                default => [
                    'url' => route('admin.panel'),
                    'texto' => 'Ir al Panel Admin',
                    'icono' => 'bi-speedometer2',
                ],
            };
        }
    @endphp

    <div class="container py-4 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-7">

                <article class="doc-constancia bg-white">
                    <header class="doc-membrete p-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ asset('img/logo-hospital.png') }}"
                                     alt="Escudo Hospital Regional del Cusco"
                                     class="doc-logo flex-shrink-0">
                                <div>
                                    <div class="doc-entidad-superior text-uppercase">
                                        Hospital Regional del Cusco &middot; Sistema de Citas
                                    </div>
                                    <h1 class="h5 fw-bold text-hrc-primario mb-0">
                                        Verificación de Constancia
                                    </h1>
                                </div>
                            </div>

                            <div class="text-sm-end">
                                <span class="doc-etiqueta d-block">Código</span>
                                <strong class="doc-codigo">{{ $codigoTicket }}</strong>
                            </div>
                        </div>
                    </header>

                    <div class="p-4 border-bottom {{ $estadoVerificacion['clase'] }}">
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi {{ $estadoVerificacion['icono'] }} fs-3 flex-shrink-0"></i>
                            <div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <h2 class="h6 fw-bold mb-0">{{ $estadoVerificacion['titulo'] }}</h2>
                                    <span class="doc-sello-estado {{ $estadoVerificacion['claseSello'] }}">
                                        {{ $estadoVerificacion['sello'] }}
                                    </span>
                                </div>
                                <p class="small mb-0">{{ $estadoVerificacion['detalle'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="doc-cuerpo p-4">
                        <div class="doc-grilla mb-3">
                            <div class="row g-0">
                                <div class="col-6 doc-celda">
                                    <span class="doc-etiqueta">Fecha</span>
                                    <span class="doc-valor-destacado">{{ $fechaCita->format('d/m/Y') }}</span>
                                </div>
                                <div class="col-6 doc-celda">
                                    <span class="doc-etiqueta">Hora</span>
                                    <span class="doc-valor-destacado text-hrc-acento">{{ $horaCita }}</span>
                                </div>
                                <div class="col-12 col-sm-6 doc-celda">
                                    <span class="doc-etiqueta">Especialidad</span>
                                    <span class="doc-valor fw-bold text-hrc-primario">{{ $cita->medico->especialidad->nombre }}</span>
                                </div>
                                <div class="col-12 col-sm-6 doc-celda">
                                    <span class="doc-etiqueta">Consultorio</span>
                                    <span class="doc-valor fw-bold">{{ $cita->medico->consultorio }}</span>
                                </div>
                                <div class="col-12 col-sm-8 doc-celda">
                                    <span class="doc-etiqueta">Médico Asignado</span>
                                    <span class="doc-valor">Dr. {{ $cita->medico->nombres }} {{ $cita->medico->apellidos }}</span>
                                </div>
                                <div class="col-12 col-sm-4 doc-celda">
                                    <span class="doc-etiqueta">Turno</span>
                                    <span class="doc-valor fw-bold">
                                        @if ($cita->estado === 'Cancelado')
                                            Anulado
                                        @else
                                            {{ $ordenAtencion }} de {{ $totalCitasDia }}
                                        @endif
                                    </span>
                                </div>
                                <div class="col-12 col-sm-8 doc-celda">
                                    <span class="doc-etiqueta">Paciente</span>
                                    <span class="doc-valor fw-bold text-uppercase text-hrc-primario">
                                        {{ $cita->paciente->apellido_paterno }} {{ $cita->paciente->apellido_materno }}, {{ $cita->paciente->nombres }}
                                    </span>
                                </div>
                                <div class="col-12 col-sm-4 doc-celda">
                                    <span class="doc-etiqueta">Documento</span>
                                    <span class="doc-valor fw-semibold">
                                        {{ $cita->paciente->tipo_documento ?? 'DNI' }} {{ $documentoVisible }}
                                    </span>
                                </div>
                                <div class="col-12 doc-celda">
                                    <span class="doc-etiqueta">Modalidad de Atención</span>
                                    <span class="doc-valor">
                                        @if ($esSis)
                                            <strong>SIS - Referido</strong> &middot; Ref: <span class="doc-codigo-inline">{{ $cita->codigo_referencia_sis }}</span>
                                        @else
                                            <strong>Paciente Particular</strong> &middot; Requiere boleta de pago emitida en Caja del HRC
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if ($esPersonalInterno && $accesoOperativo)
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2 pt-2">
                                <a href="{{ route('ticket', $cita->id) }}" class="btn btn-outline-hrc-primario btn-sm rounded-pill px-4">
                                    <i class="bi bi-file-earmark-text me-1"></i> Ver constancia completa
                                </a>
                                <a href="{{ $accesoOperativo['url'] }}" class="btn btn-hrc-acento btn-sm rounded-pill px-4">
                                    <i class="bi {{ $accesoOperativo['icono'] }} me-1"></i> {{ $accesoOperativo['texto'] }}
                                </a>
                            </div>
                        @else
                            <p class="texto-xs text-muted mb-0">
                                <i class="bi bi-lock-fill me-1"></i>
                                Por protección de datos personales, el documento se muestra parcialmente oculto para consultas sin sesión institucional.
                            </p>
                        @endif
                    </div>

                    <footer class="doc-pie px-4 py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <span>Verificación en tiempo real &middot; HRC</span>
                        <span>Consultado: {{ now()->format('d/m/Y h:i:s A') }}</span>
                    </footer>
                </article>

            </div>
        </div>
    </div>
@endsection