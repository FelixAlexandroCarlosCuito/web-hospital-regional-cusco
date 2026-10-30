@extends('layouts.main')

@section('titulo', 'Registro de paciente')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/ticket.css') }}">
@endsection

@section('contenido')
    <main class="contenedor-ticket">
        <div class="tarjeta-ticket" id="tarjeta-ticket">
            
            <div class="ticket-cabecera" style="background-color: #059669;">
                <div class="icono-exito" style="color: #059669;">✓</div>
                <h2>Cita Confirmada</h2>
                <p>Hospital Regional del Cusco</p>
            </div>

            <div class="ticket-cuerpo">
                <div class="ticket-id" style="border-bottom: 2px dashed #cbd5e1; padding-bottom: 15px; margin-bottom: 15px;">
                    <span>AUTOGENERADO (N° TICKET):</span>
                    <strong>HRC-{{ date('Y', strtotime($cita->created_at)) }}-{{ str_pad($cita->id, 5, '0', STR_PAD_LEFT) }}</strong>
                </div>

                <div class="ticket-detalles" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    
                    <div class="detalle-grupo" style="grid-column: 1 / -1;">
                        <span class="detalle-etiqueta">PACIENTE</span>
                        <span class="detalle-valor" style="text-transform: uppercase;">{{ Auth::user()->apellido_paterno }} {{ Auth::user()->apellido_materno }} {{ Auth::user()->nombres }}</span>
                    </div>

                    <div class="detalle-grupo">
                        <span class="detalle-etiqueta">DNI / CE</span>
                        <span class="detalle-valor">{{ Auth::user()->numero_documento }}</span>
                    </div>

                    <div class="detalle-grupo">
                        <span class="detalle-etiqueta">TIPO PACIENTE</span>
                        <span class="detalle-valor">
                            {{ $cita->codigo_referencia_sis ? 'SIS - REFERIDO' : 'PARTICULAR' }}
                        </span>
                    </div>

                    <div class="detalle-grupo">
                        <span class="detalle-etiqueta">ESPECIALIDAD</span>
                        <span class="detalle-valor" style="font-weight: bold;">{{ $cita->medico->especialidad->nombre }}</span>
                    </div>

                    <div class="detalle-grupo">
                        <span class="detalle-etiqueta">MÉDICO TRATANTE</span>
                        <span class="detalle-valor">{{ $cita->medico->nombres }} {{ $cita->medico->apellidos }}</span>
                    </div>

                    <div class="detalle-grupo">
                        <span class="detalle-etiqueta">CONSULTORIO</span>
                        <span class="detalle-valor">{{ $cita->medico->consultorio }}</span>
                    </div>

                    <div class="detalle-grupo">
                        <span class="detalle-etiqueta">ORDEN / TOTAL</span>
                        <span class="detalle-valor" style="font-size: 1.1em; font-weight: bold;">{{ $ordenAtencion }} / {{ $totalCitasDia }}</span>
                    </div>

                    <div class="detalle-grupo" style="grid-column: 1 / -1; background: #f8fafc; padding: 10px; border-radius: 6px;">
                        <div style="display: flex; justify-content: space-between;">
                            <div>
                                <span class="detalle-etiqueta">FECHA DE LA CITA</span>
                                <span class="detalle-valor" style="color: #0f172a; font-weight: bold;">{{ date('d/m/Y', strtotime($cita->fecha)) }}</span>
                            </div>
                            <div style="text-align: right;">
                                <span class="detalle-etiqueta">HORA DE LA CITA</span>
                                <span class="detalle-valor" style="color: #0f172a; font-weight: bold;">{{ date('H:i', strtotime($cita->hora)) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ticket-advertencia" style="margin-top: 20px; font-size: 0.9em;">
                    <strong>Importante:</strong> Presente este ticket impreso o digital en Admisión 15 minutos antes de su cita. 
                    @if(!$cita->codigo_referencia_sis)
                        Por favor, acérquese a caja para realizar el pago correspondiente antes de su atención.
                    @else
                        Traer copia de DNI y Hoja de Referencia ({{ $cita->codigo_referencia_sis }}).
                    @endif
                </div>
            </div>

            <div class="ticket-pie">
                <div class="acciones-ticket oculta-impresion" style="margin-top: 20px;">
                    <button type="button" onclick="window.print()" class="btn-primario" style="margin-right: 10px;">Imprimir Constancia</button>
                    <a href="{{ route('dashboard') }}" class="btn-outline">Ir a mi Panel</a>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('js')
    <script src="{{asset('js/ticket.js')}}"></script>
@endsection