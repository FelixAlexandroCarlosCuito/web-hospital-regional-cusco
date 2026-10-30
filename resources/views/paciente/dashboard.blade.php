@extends('layouts.main')

@section('titulo', 'Dashboard')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('contenido')
    <main class="contenedor-dashboard">
        <aside class="perfil-paciente">
            <div class="avatar-paciente">JC</div>
            <h2 class="nombre-paciente">Juan Carlos Pérez Gonzales</h2>
            <p class="documento-paciente">DNI: 76543210</p>
            <div class="estadisticas-paciente">
                <div class="stat-item">
                    <span class="stat-numero">1</span>
                    <span class="stat-texto">Citas Activas</span>
                </div>
                <div class="stat-item">
                    <span class="stat-numero">3</span>
                    <span class="stat-texto">Historial</span>
                </div>
            </div>
        </aside>

        <section class="contenido-dashboard">
            <div class="cabecera-dashboard">
                <h2>Mis Citas Próximas</h2>
            </div>

            <div id="contenedor-citas-activas">
                <div class="tarjeta-cita" id="cita-1">
                    <div class="cita-fecha-hora">
                        <div class="cita-dia">25</div>
                        <div class="cita-mes">OCT</div>
                        <div class="cita-hora">09:30 AM</div>
                    </div>
                    <div class="cita-detalles">
                        <span class="estado-insignia estado-pendiente">Pendiente</span>
                        <h3>Cardiología</h3>
                        <p><strong>Médico:</strong> Dr. Carlos Mendoza</p>
                        <p><strong>Lugar:</strong> Consultorio 12 - Piso 2</p>
                    </div>
                    <div class="cita-acciones">
                        <a href="{{route('ticket')}}" class="btn-secundario">Ver Ticket</a>
                        <button type="button" class="btn-peligro btn-cancelar-cita" data-id="1">Cancelar Cita</button>
                    </div>
                </div>
            </div>

            <div class="cabecera-dashboard margen-superior">
                <h2>Historial Médico Reciente</h2>
            </div>

            <div class="tabla-historial-contenedor">
                <table class="tabla-historial">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Especialidad</th>
                            <th>Médico</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>10/05/2026</td>
                            <td>Medicina General</td>
                            <td>Dra. Ana Silva</td>
                            <td><span class="estado-texto estado-atendido">Atendido</span></td>
                        </tr>
                        <tr>
                            <td>15/02/2026</td>
                            <td>Pediatría</td>
                            <td>Dr. Luis Paredes</td>
                            <td><span class="estado-texto estado-atendido">Atendido</span></td>
                        </tr>
                        <tr>
                            <td>08/11/2025</td>
                            <td>Cardiología</td>
                            <td>Dr. Carlos Mendoza</td>
                            <td><span class="estado-texto estado-cancelado">Cancelado por paciente</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection

@section('js')
    <script src="{{asset('js/dashboard.js')}}"></script>
@endsection