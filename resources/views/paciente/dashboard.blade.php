@extends('layouts.main')

@section('titulo', 'Dashboard')

@section('contenido')
    <div class="container py-5">
        <div class="row g-4">
            
            <!-- Panel Lateral: Perfil y Estadísticas -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-4">
                        
                        <!-- Avatar generado dinámicamente con las iniciales del usuario -->
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm" 
                             style="width: 80px; height: 80px; background-color: var(--hrc-azul-primario); color: white; font-size: 2rem; font-weight: bold;">
                            {{ strtoupper(substr(Auth::user()->nombres, 0, 1) . substr(Auth::user()->apellido_paterno, 0, 1)) }}
                        </div>
                        
                        <h4 class="fw-bold text-hrc-primario mb-1">{{ Auth::user()->nombres }} {{ Auth::user()->apellido_paterno }}</h4>
                        <p class="text-muted mb-4">{{ Auth::user()->tipo_documento }}: {{ Auth::user()->numero_documento }}</p>

                        <div class="row border-top pt-3">
                            <div class="col-6 border-end">
                                <h3 class="fw-bold text-hrc-primario mb-0">{{ $citasActivas->count() }}</h3>
                                <small class="text-muted">Citas Activas</small>
                            </div>
                            <div class="col-6">
                                <h3 class="fw-bold text-hrc-primario mb-0">{{ $historialCitas->count() }}</h3>
                                <small class="text-muted">Historial</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal: Citas Transaccionales -->
            <div class="col-lg-8">
                
                <h3 class="fw-bold text-hrc-primario mb-3">Mis Citas Próximas</h3>
                
                <div id="contenedor-citas-activas" class="mb-5">
                    @forelse($citasActivas as $cita)
                        <div class="card border-0 shadow-sm mb-3" id="cita-{{ $cita->id }}">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    
                                    <!-- Bloque Fecha -->
                                    <div class="col-md-3 text-center border-end mb-3 mb-md-0">
                                        <div class="display-6 fw-bold text-hrc-acento">{{ \Carbon\Carbon::parse($cita->fecha)->format('d') }}</div>
                                        <div class="text-uppercase fw-bold text-muted">{{ \Carbon\Carbon::parse($cita->fecha)->translatedFormat('M') }}</div>
                                        <div class="small fw-bold text-secondary">{{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}</div>
                                    </div>
                                    
                                    <!-- Bloque Detalles Clínicos -->
                                    <div class="col-md-6 mb-3 mb-md-0 ps-md-4">
                                        <span class="badge bg-warning text-dark mb-2">{{ $cita->estado }}</span>
                                        <h5 class="fw-bold text-hrc-primario mb-1">{{ $cita->medico->especialidad->nombre }}</h5>
                                        <p class="mb-1 text-muted"><strong>Médico:</strong> Dr. {{ $cita->medico->nombres }} {{ $cita->medico->apellidos }}</p>
                                        <p class="mb-0 text-muted"><strong>Lugar:</strong> {{ $cita->medico->consultorio }}</p>
                                    </div>
                                    
                                    <!-- Bloque Acciones -->
                                    <div class="col-md-3 text-md-end text-center">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('ticket', $cita->id) }}" class="btn btn-outline-hrc-primario btn-sm">Ver Ticket</a>
                                            
                                            <!-- Intercepción JS para confirmación de cancelación -->
                                            <form action="{{ route('citas.cancelar', $cita->id) }}" method="POST" class="formulario-cancelar m-0">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 btn-cancelar-cita">Cancelar</button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert bg-white text-center py-4 border text-muted shadow-sm rounded-3">
                            No tienes citas médicas próximas programadas.
                        </div>
                    @endforelse
                </div>

                <h3 class="fw-bold text-hrc-primario mb-3 mt-4">Historial Médico Reciente</h3>
                
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th class="ps-4 py-3">Fecha</th>
                                    <th class="py-3">Especialidad</th>
                                    <th class="py-3">Médico</th>
                                    <th class="pe-4 py-3">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historialCitas as $historial)
                                    <tr>
                                        <td class="ps-4 py-3">{{ \Carbon\Carbon::parse($historial->fecha)->format('d/m/Y') }}</td>
                                        <td class="fw-bold text-hrc-primario py-3">{{ $historial->medico->especialidad->nombre }}</td>
                                        <td class="py-3">Dr. {{ $historial->medico->apellidos }}</td>
                                        <td class="pe-4 py-3">
                                            <!-- Renderizado de insignias (badges) basado en lógica de estados -->
                                            @if($historial->estado === 'Atendido')
                                                <span class="badge bg-success">Atendido</span>
                                            @elseif($historial->estado === 'Cancelado')
                                                <span class="badge bg-danger">Cancelado</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $historial->estado }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Aún no hay registros en su historial médico.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection