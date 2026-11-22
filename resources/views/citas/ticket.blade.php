@extends('layouts.main')

@section('titulo', 'Constancia de Cita')

@section('contenido')
    @php
        $anioEmision = optional($cita->created_at)->format('Y') ?? now()->format('Y');
        $codigoTicket = 'HRC-' . $anioEmision . '-' . str_pad($cita->id, 5, '0', STR_PAD_LEFT);
        $esSis = !empty($cita->codigo_referencia_sis);
        $fechaCita = \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y');
        $horaCita = \Carbon\Carbon::parse($cita->hora)->format('h:i A');

        $configEstado = match ($cita->estado) {
            'Atendido' => [
                'sello' => 'ATENDIDA',
                'claseSello' => 'doc-sello-atendida',
            ],
            'Cancelado' => [
                'sello' => 'CANCELADA',
                'claseSello' => 'doc-sello-cancelada',
            ],
            default => [
                'sello' => 'VIGENTE',
                'claseSello' => 'doc-sello-vigente',
            ],
        };

        $rutaPanel = match (Auth::user()->rol) {
            'admin' => route('admin.panel'),
            'admision' => route('admision.panel'),
            'medico' => route('medico.panel'),
            default => route('dashboard'),
        };
    @endphp

    <div class="container py-4 py-md-5 contenedor-ticket">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9 col-xl-8">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 oculta-impresion" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                @endif

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2 mb-3 oculta-impresion">
                    <a href="{{ $rutaPanel }}" class="btn btn-outline-hrc-primario btn-sm rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Volver a mi Panel
                    </a>
                    <button type="button" id="btn-imprimir-ticket" class="btn btn-hrc-acento btn-sm rounded-pill px-4">
                        <i class="bi bi-printer-fill me-2"></i> Imprimir / Guardar PDF
                    </button>
                </div>

                <article class="doc-constancia bg-white" id="tarjeta-ticket">

                    {{-- Membrete institucional con escudo oficial --}}
                    <header class="doc-membrete p-4">
                        <div class="row align-items-center g-3">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ asset('img/logo-hospital.png') }}"
                                         alt="Escudo Hospital Regional del Cusco"
                                         class="doc-logo flex-shrink-0">
                                    <div>
                                        <div class="doc-entidad-superior text-uppercase">
                                            Gobierno Regional del Cusco &middot; Gerencia Regional de Salud
                                        </div>
                                        <h1 class="h4 fw-bold text-hrc-primario mb-1">
                                            Hospital Regional del Cusco
                                        </h1>
                                        <div class="doc-subtitulo-oficial text-uppercase">
                                            Constancia de Cita &mdash; Consulta Externa
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 text-md-end">
                                <div class="doc-folio-caja d-inline-block text-start text-md-end">
                                    <span class="doc-etiqueta d-block">N° de Constancia</span>
                                    <strong class="doc-codigo d-block">{{ $codigoTicket }}</strong>
                                    <span class="doc-sello-estado {{ $configEstado['claseSello'] }} mt-1">
                                        {{ $configEstado['sello'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </header>

                    @if ($cita->estado === 'Cancelado')
                        <div class="doc-banda-anulada px-4 py-2 small">
                            <i class="bi bi-x-octagon-fill me-2"></i>
                            <strong>Documento sin validez operativa:</strong> esta cita fue cancelada y el cupo fue liberado.
                        </div>
                    @endif

                    <div class="doc-cuerpo p-4">

                        <h2 class="doc-seccion-titulo">I. Datos de la Atención Programada</h2>
                        <div class="doc-grilla mb-4">
                            <div class="row g-0">
                                <div class="col-6 col-md-4 doc-celda">
                                    <span class="doc-etiqueta">Fecha de Atención</span>
                                    <span class="doc-valor-destacado">{{ $fechaCita }}</span>
                                </div>
                                <div class="col-6 col-md-4 doc-celda">
                                    <span class="doc-etiqueta">Hora Programada</span>
                                    <span class="doc-valor-destacado text-hrc-acento">{{ $horaCita }}</span>
                                </div>
                                <div class="col-12 col-md-4 doc-celda">
                                    <span class="doc-etiqueta">Consultorio</span>
                                    <span class="doc-valor fw-bold">{{ $cita->medico->consultorio }}</span>
                                </div>
                                <div class="col-12 col-md-4 doc-celda">
                                    <span class="doc-etiqueta">Especialidad</span>
                                    <span class="doc-valor fw-bold text-hrc-primario">{{ $cita->medico->especialidad->nombre }}</span>
                                </div>
                                <div class="col-12 col-md-5 doc-celda">
                                    <span class="doc-etiqueta">Médico Asignado</span>
                                    <span class="doc-valor">Dr. {{ $cita->medico->nombres }} {{ $cita->medico->apellidos }}</span>
                                </div>
                                <div class="col-12 col-md-3 doc-celda">
                                    <span class="doc-etiqueta">Orden de Atención</span>
                                    <span class="doc-valor fw-bold">
                                        @if ($cita->estado === 'Cancelado')
                                            Anulado
                                        @else
                                            Turno {{ $ordenAtencion }} de {{ $totalCitasDia }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <h2 class="doc-seccion-titulo">II. Datos del Paciente y Modalidad</h2>
                        <div class="doc-grilla mb-4">
                            <div class="row g-0">
                                <div class="col-12 col-md-8 doc-celda">
                                    <span class="doc-etiqueta">Apellidos y Nombres</span>
                                    <span class="doc-valor fw-bold text-uppercase text-hrc-primario">
                                        {{ $cita->paciente->apellido_paterno }} {{ $cita->paciente->apellido_materno }}, {{ $cita->paciente->nombres }}
                                    </span>
                                </div>
                                <div class="col-12 col-md-4 doc-celda">
                                    <span class="doc-etiqueta">Documento de Identidad</span>
                                    <span class="doc-valor fw-semibold">
                                        {{ $cita->paciente->tipo_documento ?? 'DNI' }} {{ $cita->paciente->numero_documento }}
                                    </span>
                                </div>
                                <div class="col-12 col-md-6 doc-celda">
                                    <span class="doc-etiqueta">Modalidad de Atención</span>
                                    <span class="doc-valor fw-semibold">
                                        {{ $esSis ? 'Seguro Integral de Salud (SIS - Referido)' : 'Paciente Particular (Pago en Caja)' }}
                                    </span>
                                </div>
                                <div class="col-12 col-md-6 doc-celda">
                                    <span class="doc-etiqueta">
                                        {{ $esSis ? 'Código de Referencia SIS' : 'Condición de Pago' }}
                                    </span>
                                    <span class="doc-valor">
                                        @if ($esSis)
                                            <strong class="doc-codigo-inline">{{ $cita->codigo_referencia_sis }}</strong>
                                        @else
                                            Presencial en ventanilla de Caja del hospital
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <h2 class="doc-seccion-titulo">III. Indicaciones y Verificación de Autenticidad</h2>
                        <div class="row g-3 align-items-stretch">
                            <div class="col-md-8">
                                <div class="doc-indicaciones p-3 h-100 small">
                                    @if ($cita->estado === 'Cancelado')
                                        <p class="mb-0">
                                            Esta constancia corresponde a una reserva cancelada y no habilita el ingreso a consultorio. Si requiere atención médica, registre una nueva cita desde el portal institucional.
                                        </p>
                                    @else
                                        <ul class="doc-lista-indicaciones mb-0 ps-3">
                                            <li>
                                                Presente esta constancia (impresa o en celular) junto con su <strong>documento de identidad original</strong>.
                                            </li>
                                            @if ($esSis)
                                                <li>
                                                    Preséntese en Admisión <strong>15 minutos antes</strong> de su horario portando copia de su DNI y su <strong>Hoja de Referencia SIS vigente</strong> ({{ $cita->codigo_referencia_sis }}).
                                                </li>
                                                <li>
                                                    Atención cubierta por el Seguro Integral de Salud; no requiere realizar pago en Caja.
                                                </li>
                                            @else
                                                <li>
                                                    Acérquese con <strong>20 a 30 minutos de anticipación</strong> a las ventanillas de <strong>Caja del hospital</strong> indicando su código de constancia (<strong>{{ $codigoTicket }}</strong>) para efectuar el pago presencial.
                                                </li>
                                                <li>
                                                    El Hospital Regional del Cusco no recibe pagos en línea ni en bancos externos. Se recomienda llevar <strong>dinero en efectivo</strong> (sencillo).
                                                </li>
                                                <li>
                                                    Conserve la <strong>boleta de venta emitida por Caja</strong>; su presentación es obligatoria para ingresar al consultorio asignado.
                                                </li>
                                            @endif
                                        </ul>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="doc-qr-panel p-3 h-100 d-flex flex-column align-items-center justify-content-center text-center">
                                    <div id="qr-constancia"
                                         class="doc-qr-caja mb-2"
                                         role="img"
                                         aria-label="Código QR de verificación de la constancia {{ $codigoTicket }}"
                                         data-url="{{ $urlVerificacion }}">
                                    </div>
                                    <div class="doc-qr-titulo">Verificación Digital</div>
                                    <div class="doc-qr-nota">
                                        Escanear en Admisión o Caja para validar vigencia en tiempo real
                                    </div>
                                    <a href="{{ $urlVerificacion }}"
                                       target="_blank"
                                       rel="noopener"
                                       class="enlace-mate texto-xs fw-semibold text-decoration-none mt-2 oculta-impresion">
                                        Abrir ficha de verificación <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>

                    <footer class="doc-pie px-4 py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <span>Av. de La Cultura S/N, Cusco &middot; Central Telefónica: (084) 231131</span>
                        <span>Emitido: {{ now()->format('d/m/Y h:i A') }}</span>
                    </footer>

                </article>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.js"></script>
    <script src="{{ asset('js/ticket.js') }}"></script>
@endsection