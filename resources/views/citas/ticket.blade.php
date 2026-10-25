@extends('layouts.main')

@section('titulo', 'Registro de paciente')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/ticket.css') }}">
@endsection

@section('contenido')
    <main class="contenedor-ticket">
        <div class="tarjeta-ticket" id="tarjeta-ticket">
            
            <!-- Cabecera Dinámica -->
            <div class="ticket-cabecera" id="ticket-cabecera" style="background-color: #d97706;">
                <div class="icono-exito" id="icono-estado" style="color: #d97706;">!</div>
                <h2 id="titulo-estado">Cita Reservada</h2>
                <p id="mensaje-estado">Pendiente de pago en Caja</p>
            </div>

            <div class="ticket-cuerpo">
                <div class="ticket-id">
                    <span>N° de Ticket:</span>
                    <strong>HRC-2026-8472</strong>
                </div>

                <!-- Temporizador -->
                <div id="contenedor-temporizador" style="text-align: center; margin-bottom: 20px; padding: 15px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px;">
                    <p style="color: #b45309; font-size: 14px; margin-bottom: 5px;">Tiempo restante para pagar:</p>
                    <h3 id="reloj" style="color: #dc2626; font-size: 28px; margin: 0; font-family: monospace;">20:00</h3>
                </div>

                <div class="ticket-detalles">
                    <!-- Aquí van tus detalles del paciente, especialidad, etc... (los dejo igual) -->
                    <div class="detalle-grupo">
                        <span class="detalle-etiqueta">Paciente</span>
                        <span class="detalle-valor">Juan Carlos Pérez Gonzales</span>
                    </div>
                    <div class="detalle-grupo">
                        <span class="detalle-etiqueta">Especialidad</span>
                        <span class="detalle-valor">Cardiología</span>
                    </div>
                    <div class="detalle-grupo">
                        <span class="detalle-etiqueta">Monto a Pagar</span>
                        <span class="detalle-valor" id="ticket-monto" style="color: #dc2626;">S/ 6.00</span>
                    </div>
                </div>

                <div class="ticket-advertencia" id="ticket-advertencia">
                    Debe acercarse a Caja antes de que termine el tiempo. Si no realiza el pago, la cita se cancelará automáticamente para liberar el cupo.
                </div>
            </div>

            <div class="ticket-pie">
                <img src="../assets/qr-placeholder.png" alt="Código QR" class="codigo-qr">
                
                <div class="acciones-ticket oculta-impresion" style="margin-top: 20px;">
                    <!-- Este botón simula que el cajero cobró -->
                    <button type="button" id="btn-simular-pago" class="btn-exito" style="background-color: #059669; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: bold;">
                        💳 Simular Pago en Caja
                    </button>
                    <button type="button" id="btn-imprimir" class="btn-primario">Imprimir Constancia</button>
                    <a href="{{url('/inicio')}}" class="btn-outline">Volver al Inicio</a>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('js')
    <script src="{{asset('js/ticket.js')}}"></script>
@endsection