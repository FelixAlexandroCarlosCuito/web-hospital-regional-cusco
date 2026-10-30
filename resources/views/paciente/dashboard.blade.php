@extends('layouts.main')

@section('titulo', 'Dashboard')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('contenido')
<main class="contenedor-dashboard">
        <aside class="perfil-paciente">
            <div class="avatar-paciente">
                {{ strtoupper(substr(Auth::user()->nombres, 0, 1) . substr(Auth::user()->apellido_paterno, 0, 1)) }}
            </div>
            <h2 class="nombre-paciente">{{ Auth::user()->nombres }} {{ Auth::user()->apellido_paterno }}</h2>
            <p class="documento-paciente">{{ Auth::user()->tipo_documento }}: {{ Auth::user()->numero_documento }}</p>
            
            <div class="estadisticas-paciente">
                <div class="stat-item">
                    <!-- Conteo real desde la base de datos -->
                    <span class="stat-numero">{{ $citasActivas->count() }}</span>
                    <span class="stat-texto">Citas Activas</span>
                </div>
                <div class="stat-item">
                    <!-- Conteo real del historial -->
                    <span class="stat-numero">{{ $historialCitas->count() }}</span>
                    <span class="stat-texto">Historial</span>
                </div>
            </div>
        </aside>

        <section class="contenido-dashboard">
            <div class="cabecera-dashboard">
                <h2>Mis Citas Próximas</h2>
            </div>

            <div id="contenedor-citas-activas">
                @forelse($citasActivas as $cita)
                    <div class="tarjeta-cita" id="cita-{{ $cita->id }}">
                        <div class="cita-fecha-hora">
                            <!-- Parseamos la fecha para extraer día y mes -->
                            <div class="cita-dia">{{ \Carbon\Carbon::parse($cita->fecha)->format('d') }}</div>
                            <div class="cita-mes">{{ strtoupper(\Carbon\Carbon::parse($cita->fecha)->translatedFormat('M')) }}</div>
                            <div class="cita-hora">{{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}</div>
                        </div>
                        <div class="cita-detalles">
                            <span class="estado-insignia estado-pendiente">{{ $cita->estado }}</span>
                            <h3>{{ $cita->medico->especialidad->nombre }}</h3>
                            <p><strong>Médico:</strong> Dr. {{ $cita->medico->nombres }} {{ $cita->medico->apellidos }}</p>
                            <p><strong>Lugar:</strong> {{ $cita->medico->consultorio }}</p>
                        </div>
                        <div class="cita-acciones">
                            <a href="{{ route('ticket', $cita->id) }}" class="btn-secundario">Ver Ticket</a>                            
                            <!-- Formulario preparado para la lógica de cancelación -->                
                            <form action="{{ route('citas.cancelar', $cita->id) }}" method="POST" class="formulario-cancelar" style="display: inline;">
                                @csrf
                                <!-- PATCH indica que solo actualizaremos el estado, no borraremos la fila -->
                                @method('PATCH')
                                <button type="submit" class="btn-peligro btn-cancelar-cita">Cancelar Cita</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div style="padding: 20px; text-align: center; color: #64748b; background: #f8fafc; border-radius: 8px;">
                        No tienes citas médicas próximas programadas.
                    </div>
                @endforelse
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
                        @forelse($historialCitas as $historial)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($historial->fecha)->format('d/m/Y') }}</td>
                                <td>{{ $historial->medico->especialidad->nombre }}</td>
                                <td>Dr. {{ $historial->medico->apellidos }}</td>
                                <td>
                                    <!-- Dinamizamos el color según el estado -->
                                    <span class="estado-texto estado-{{ strtolower($historial->estado) }}">
                                        {{ $historial->estado }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 15px;">Aún no hay registros en su historial médico.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection

@section('js')
    <script src="{{asset('js/dashboard.js')}}"></script>
@endsection