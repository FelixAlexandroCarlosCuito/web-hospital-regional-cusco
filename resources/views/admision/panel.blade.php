@extends('layouts.main')

@section('titulo', 'Panel Admisión')

@section('contenido')
    <div class="container py-5">
        
        <!-- Cabecera Institucional del Módulo -->
        <div class="card border-0 shadow-sm mb-4" style="background-color: var(--hrc-azul-primario);">
            <div class="card-body p-4 p-md-5 text-white">
                <h2 class="fw-bold mb-2">Centro de Comando - Admisión y Ventanilla</h2>
                <p class="mb-0 fs-5 opacity-75">Gestión global de citas y control de agendas médicas</p>
            </div>
        </div>

        <!-- Motor de Búsqueda y Filtros -->
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-body p-4">
                <form action="{{ route('admision.panel') }}" method="GET" class="row g-3 align-items-end">
                    
                    <div class="col-md-5">
                        <label for="medico_id" class="form-label fw-bold text-secondary">Consultar Agenda de:</label>
                        <select name="medico_id" id="medico_id" class="form-select form-select-lg" required>
                            <option value="" disabled {{ !$medicoFiltro ? 'selected' : '' }}>Seleccione un médico...</option>
                            @foreach($medicos as $medico)
                                <option value="{{ $medico->id }}" {{ $medicoFiltro == $medico->id ? 'selected' : '' }}>
                                    Dr. {{ $medico->apellidos }} {{ $medico->nombres }} ({{ $medico->especialidad->nombre }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="fecha" class="form-label fw-bold text-secondary">Para la fecha:</label>
                        <input type="date" name="fecha" id="fecha" value="{{ $fechaFiltro }}" class="form-control form-control-lg" required>
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-hrc-acento btn-lg w-100 fw-bold">Buscar Agenda</button>
                    </div>
                    
                </form>
            </div>
        </div>

        <!-- Renderizado Condicional: Resultados de la Agenda -->
        @if($medicoFiltro)
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <h4 class="fw-bold text-hrc-primario mb-0">Pacientes Programados ({{ $citas->count() }})</h4>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="ps-4 py-3">Orden</th>
                                <th class="py-3">Hora</th>
                                <th class="py-3">Paciente (DNI)</th>
                                <th class="py-3">Tipo</th>
                                <th class="py-3">Estado</th>
                                <th class="pe-4 py-3 text-center">Gestión operativa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($citas as $index => $cita)
                                <tr>
                                    <td class="ps-4 py-3 fw-bold text-secondary">#{{ $index + 1 }}</td>
                                    
                                    <td class="py-3 fw-bold text-hrc-primario">
                                        {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                                    </td>
                                    
                                    <td class="py-3">
                                        <span class="d-block text-dark">
                                            <strong>{{ strtoupper($cita->paciente->apellido_paterno . ' ' . $cita->paciente->apellido_materno) }}</strong>, 
                                            {{ $cita->paciente->nombres }}
                                        </span>
                                        <small class="text-muted">DNI: {{ $cita->paciente->numero_documento }}</small>
                                    </td>
                                    
                                    <td class="py-3">
                                        @if($cita->codigo_referencia_sis)
                                            <span class="badge bg-success">SIS</span>
                                        @else
                                            <span class="badge bg-secondary">Particular</span>
                                        @endif
                                    </td>

                                    <td class="py-3">
                                        @if($cita->estado === 'Pendiente')
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        @elseif($cita->estado === 'Atendido')
                                            <span class="badge bg-primary">Atendido</span>
                                        @elseif($cita->estado === 'Cancelado')
                                            <span class="badge bg-danger">Cancelado</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $cita->estado }}</span>
                                        @endif
                                    </td>

                                    <td class="pe-4 py-3">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('ticket', $cita->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm fw-bold">
                                                Imprimir
                                            </a>
                                            
                                            <!-- Intercepción de lógica de negocio: Cancelación operativa por Admisión -->
                                            @if($cita->estado === 'Pendiente')
                                                <form action="{{ route('admision.citas.cancelar', $cita->id) }}" method="POST" class="m-0" onsubmit="return confirm('¿Confirma la cancelación de esta cita médica desde Ventanilla? El cupo será liberado en la base de datos.');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm fw-bold">
                                                        Cancelar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <div class="d-flex flex-column align-items-center">
                                            <!-- SVG: Bandeja Vacía -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-inbox opacity-50 mb-3" viewBox="0 0 16 16">
                                                <path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 8h4.46l-3.05-3.812A.5.5 0 0 0 11.02 4zM3.81.563A1.5 1.5 0 0 1 4.98 0h6.04a1.5 1.5 0 0 1 1.17.563l3.7 4.625a.5.5 0 0 1 .106.374l-.39 3.124A1.5 1.5 0 0 1 14.117 10H1.883a1.5 1.5 0 0 1-1.489-1.314l-.39-3.124a.5.5 0 0 1 .106-.374zM.125 11.17A.5.5 0 0 1 .5 11H6a.5.5 0 0 1 .5.5 1.5 1.5 0 0 0 3 0 .5.5 0 0 1 .5-.5h5.5a.5.5 0 0 1 .496.562l-.39 3.124A1.5 1.5 0 0 1 14.117 16H1.883a1.5 1.5 0 0 1-1.489-1.314l-.39-3.124a.5.5 0 0 1 .121-.392z"/>
                                            </svg>
                                            <strong class="fs-5">Sin agenda asignada</strong>
                                            <p class="mb-0">El médico seleccionado no tiene pacientes programados en esta fecha.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Estado Inicial: Esperando parámetros de búsqueda -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center py-5 text-muted bg-light">
                    <div class="d-flex flex-column align-items-center">
                        <!-- SVG: Búsqueda / Interfaz -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-search opacity-50 mb-3" viewBox="0 0 16 16">
                          <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                        <strong class="fs-5">Seleccione parámetros de búsqueda</strong>
                        <p class="mb-0">Indique un médico y una fecha en el panel superior para visualizar y gestionar la agenda.</p>
                    </div>
                </div>
            </div>
        @endif
        
    </div>
@endsection