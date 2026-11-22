@extends('layouts.main')

@section('titulo', 'Ticket de Cita')

@section('contenido')
    @php
        $codigoTicket = 'HRC-' . date('Y', strtotime($cita->created_at)) . '-' . str_pad($cita->id, 5, '0', STR_PAD_LEFT);
        $esSis = !empty($cita->codigo_referencia_sis);

        // Adapta cabecera e icono al estado real del registro para evitar mostrar constancias válidas en citas canceladas
        $configEstado = match ($cita->estado) {
            'Atendido' => [
                'titulo' => 'Cita Atendida',
                'icono' => 'bi-check2-all',
                'claseCabecera' => 'ticket-cabecera-atendida',
                'claseBadge' => 'badge-estado-atendido',
            ],
            'Cancelado' => [
                'titulo' => 'Cita Cancelada',
                'icono' => 'bi-x-lg',
                'claseCabecera' => 'ticket-cabecera-cancelada',
                'claseBadge' => 'badge-estado-cancelado',
            ],
            default => [
                'titulo' => 'Cita Confirmada',
                'icono' => 'bi-check-lg',
                'claseCabecera' => 'ticket-cabecera-confirmada',
                'claseBadge' => 'badge-estado-pendiente',
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
            <div class="col-12 col-md-10 col-lg-7">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 oculta-impresion" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                @endif

                <div class="card border-0 overflow-hidden tarjeta-ticket" id="tarjeta-ticket">

                    {{-- Cabecera institucional con estado de la cita --}}
                    <div class="text-white p-4 p-md-5 ticket-cabecera {{ $configEstado['claseCabecera'] }}">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icono-exito d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0">
                                    <i class="bi {{ $configEstado['icono'] }}"></i>
                                </div>
                                <div>
                                    <span class="etiqueta-panel-oscuro d-block mb-1">Hospital Regional del Cusco</span>
                                    <h1 class="h3 fw-bold mb-0">{{ $configEstado['titulo'] }}</h1>
                                </div>
                            </div>

                            <div class="text-sm-end">
                                <span class="d-block texto-xs texto-claro-mate text-uppercase">N° de Ticket</span>
                                <strong class="ticket-codigo-cabecera">{{ $codigoTicket }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Bloque principal de programación para lectura rápida en admisión y consultorio --}}
                    <div class="card-body p-4 p-md-5 bg-white ticket-cuerpo">
                        <div class="ticket-franja-horario p-3 p-md-4 mb-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-6 col-sm-4">
                                    <span class="ticket-etiqueta d-block">Fecha</span>
                                    <span class="fw-bold text-hrc-primario fs-5">
                                        {{ date('d/m/Y', strtotime($cita->fecha)) }}
                                    </span>
                                </div>
                                <div class="col-6 col-sm-4">
                                    <span class="ticket-etiqueta d-block">Hora</span>
                                    <span class="fw-bold text-hrc-acento fs-5">
                                        {{ date('h:i A', strtotime($cita->hora)) }}
                                    </span>
                                </div>
                                <div class="col-12 col-sm-4 text-sm-end">
                                    <span class="ticket-etiqueta d-block">Consultorio</span>
                                    <span class="fw-bold text-hrc-primario fs-6">
                                        {{ $cita->medico->consultorio }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                            <span class="ticket-pill-estado {{ $configEstado['claseBadge'] }}">
                                Estado: {{ $cita->estado }}
                            </span>
                            <span class="badge-categoria-mate">
                                {{ $esSis ? 'SIS - Referido' : 'Paciente Particular' }}
                            </span>
                        </div>

                        {{-- Datos del paciente y asignación médica --}}
                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <span class="ticket-etiqueta d-block">Paciente</span>
                                <span class="d-block fw-bold text-hrc-primario text-uppercase fs-6">
                                    {{ $cita->paciente->apellido_paterno }} {{ $cita->paciente->apellido_materno }}, {{ $cita->paciente->nombres }}
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="ticket-etiqueta d-block">Documento</span>
                                <span class="d-block text-dark fw-medium">
                                    {{ $cita->paciente->tipo_documento ?? 'DNI' }} {{ $cita->paciente->numero_documento }}
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="ticket-etiqueta d-block">Orden de Atención</span>
                                <span class="d-block text-dark fw-bold">
                                    Turno {{ $ordenAtencion }} de {{ $totalCitasDia }}
                                </span>
                            </div>

                            <div class="col-sm-6">
                                <span class="ticket-etiqueta d-block">Especialidad</span>
                                <span class="d-block fw-bold text-hrc-primario">
                                    {{ $cita->medico->especialidad->nombre }}
                                </span>
                            </div>

                            <div class="col-sm-6">
                                <span class="ticket-etiqueta d-block">Médico Asignado</span>
                                <span class="d-block text-dark fw-medium">
                                    Dr. {{ $cita->medico->nombres }} {{ $cita->medico->apellidos }}
                                </span>
                            </div>

                            @if ($esSis)
                                <div class="col-12">
                                    <span class="ticket-etiqueta d-block">Código de Referencia SIS</span>
                                    <span class="d-block text-dark fw-semibold">
                                        {{ $cita->codigo_referencia_sis }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Indicaciones operativas según modalidad --}}
                        <div class="ticket-advertencia p-3 d-flex align-items-start gap-3">
                            <i class="bi bi-info-circle-fill fs-5 flex-shrink-0 mt-1"></i>
                            <div class="small">
                                <strong class="d-block mb-1">Indicaciones para tu atención</strong>
                                @if ($cita->estado === 'Cancelado')
                                    Esta cita fue cancelada y el cupo ya fue liberado. Si necesitas atenderte, genera una nueva reserva desde el portal.
                                @else
                                    Presenta esta constancia impresa o desde tu celular en Admisión <strong>15 minutos antes</strong> de tu horario junto con tu documento de identidad original.
                                    @if (!$esSis)
                                        Acércate previamente a Caja para realizar el pago correspondiente.
                                    @else
                                        Lleva contigo la copia de tu DNI y tu hoja de referencia vigente ({{ $cita->codigo_referencia_sis }}).
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Separador perforado tipo talón --}}
                    <div class="ticket-perforacion" aria-hidden="true"></div>

                    {{-- Acciones de pantalla y nota de validez impresa --}}
                    <div class="card-footer p-4 bg-white border-0 ticket-pie">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-3 oculta-impresion acciones-ticket">
                            <a href="{{ $rutaPanel }}" class="btn btn-outline-hrc-primario rounded-pill px-4">
                                <i class="bi bi-arrow-left me-1"></i> Volver a mi Panel
                            </a>
                            <button type="button" id="btn-imprimir-ticket" class="btn btn-hrc-acento rounded-pill px-4">
                                <i class="bi bi-printer-fill me-2"></i> Imprimir Constancia
                            </button>
                        </div>

                        <p class="d-none d-print-block text-center small mb-0">
                            Av. de La Cultura S/N, Cusco &middot; Central Telefónica: (084) 231131 &middot; Emitido el {{ now()->format('d/m/Y h:i A') }}
                        </p>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/ticket.js') }}"></script>
@endsection