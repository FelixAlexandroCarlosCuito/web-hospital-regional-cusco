@extends('layouts.main')

@section('titulo', 'Ticket de Cita')

@section('contenido')
    <div class="container py-5 contenedor-ticket">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-7">
                
                <!-- Tarjeta principal del Ticket -->
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden tarjeta-ticket" id="tarjeta-ticket">
                    
                     <!-- Cabecera de Confirmación -->
                    <div class="text-white text-center p-4 p-md-5 ticket-cabecera" style="background-color: var(--hrc-azul-primario);">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3 shadow-sm icono-exito" 
                             style="width: 70px; height: 70px; font-size: 35px; font-weight: bold; color: var(--hrc-purpura-acento);">
                            ✓
                        </div>
                        <h2 class="fw-bold mb-1">Cita Confirmada</h2>
                        <p class="mb-0 opacity-75">Hospital Regional del Cusco</p>
                    </div>

                    <!-- Cuerpo del Ticket -->
                    <div class="card-body p-4 p-md-5 bg-white ticket-cuerpo">
                        
                        <!-- Identificador Único -->
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center pb-3 mb-4 ticket-id" 
                             style="border-bottom: 2px dashed #cbd5e1;">
                            <span class="text-muted fw-bold small mb-2 mb-sm-0">AUTOGENERADO (N° TICKET):</span>
                            <strong class="text-hrc-primario fs-5" style="letter-spacing: 1px;">
                                HRC-{{ date('Y', strtotime($cita->created_at)) }}-{{ str_pad($cita->id, 5, '0', STR_PAD_LEFT) }}
                            </strong>
                        </div>
                        
                        <!-- Grid de Detalles Clínicos -->                        
                        <div class="row g-4 mb-4">
                            <!-- span 12 columnas -->
                            <div class="col-12">
                                <span class="d-block text-muted small text-uppercase mb-1">Paciente</span>
                                <span class="d-block fw-bold text-dark text-uppercase fs-6">
                                    {{ $cita->paciente->apellido_paterno }} {{ $cita->paciente->apellido_materno }} {{ $cita->paciente->nombres }}
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="d-block text-muted small text-uppercase mb-1">DNI / CE</span>
                                <span class="d-block text-dark">{{ $cita->paciente->numero_documento }}</span>
                            </div>

                            <div class="col-6">
                                <span class="d-block text-muted small text-uppercase mb-1">Tipo Paciente</span>
                                <span class="d-block text-dark">
                                    {{ $cita->codigo_referencia_sis ? 'SIS - REFERIDO' : 'PARTICULAR' }}
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="d-block text-muted small text-uppercase mb-1">Especialidad</span>
                                <span class="d-block fw-bold text-hrc-primario fs-6">{{ $cita->medico->especialidad->nombre }}</span>
                            </div>

                            <div class="col-6">
                                <span class="d-block text-muted small text-uppercase mb-1">Médico Tratante</span>
                                <span class="d-block text-dark">{{ $cita->medico->nombres }} {{ $cita->medico->apellidos }}</span>
                            </div>

                            <div class="col-6">
                                <span class="d-block text-muted small text-uppercase mb-1">Consultorio</span>
                                <span class="d-block text-dark">{{ $cita->medico->consultorio }}</span>
                            </div>

                            <div class="col-6">
                                <span class="d-block text-muted small text-uppercase mb-1">Orden / Total</span>
                                <span class="d-block text-dark fs-6">{{ $ordenAtencion }} / {{ $totalCitasDia }}</span>
                            </div>
                            
                            <!-- Bloque de Fecha y Hora -->
                            <div class="col-12">
                                <div class="bg-light p-3 rounded-3 border d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="d-block text-muted small text-uppercase mb-1">Fecha de la cita</span>
                                        <span class="d-block fw-bold text-dark fs-5">{{ date('d/m/Y', strtotime($cita->fecha)) }}</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="d-block text-muted small text-uppercase mb-1">Hora de la cita</span>
                                        <span class="d-block fw-bold text-dark fs-5">{{ date('H:i', strtotime($cita->hora)) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advertencia Dinámica -->
                        <div class="alert text-center small mb-0" style="background-color: #fffbeb; color: var(--hrc-dorado-alerta); border: 1px solid #fde68a;">
                            <strong style="color: #b45309;">Importante:</strong> Presente este ticket impreso o digital en Admisión 15 minutos antes de su cita.
                            @if(!$cita->codigo_referencia_sis)
                                Por favor, acérquese a caja para realizar el pago correspondiente antes de su atención.
                            @else
                                Traer copia de DNI y Hoja de Referencia ({{ $cita->codigo_referencia_sis }}).
                            @endif
                        </div>
                        
                    </div>
                    
                    <!-- Pie del Ticket -->
                    <div class="card-footer bg-light p-4 p-md-5 text-center ticket-pie" style="border-top: 2px dashed #cbd5e1;">
                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 oculta-impresion acciones-ticket">
                            <button type="button" onclick="window.print()" class="btn btn-hrc-acento btn-lg px-4 shadow-sm">Imprimir Constancia</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">Ir a mi Panel</a>
                        </div>
                    </div>
                    
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/ticket.js') }}"></script>
@endsection