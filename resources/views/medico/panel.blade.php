@extends('layouts.main')

@section('titulo', 'Panel Clínico')

@section('contenido')
    <div class="container py-5">
        
        <div class="card border-0 shadow-sm mb-4" style="background-color: var(--hrc-azul-primario);">
            <div class="card-body p-4 p-md-5 text-white">
                <h2 class="fw-bold mb-2">Panel Clínico - Hospital Regional del Cusco</h2>
                <p class="mb-0 fs-5 opacity-75">
                    Dr. {{ $medico->nombres }} {{ $medico->apellidos }} | {{ $medico->especialidad->nombre }} | Consultorio: {{ $medico->consultorio }}
                </p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            
            <div class="card-header bg-white border-bottom p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <h3 class="fw-bold text-hrc-primario mb-0">Agenda Clínica</h3>
                
                <form action="{{ route('medico.panel') }}" method="GET" class="d-flex align-items-center gap-2">
                    <label for="fecha" class="fw-bold text-secondary text-nowrap mb-0">Fecha a consultar:</label>
                    <input type="date" id="fecha" name="fecha" value="{{ $fechaFiltro }}" 
                           class="form-control form-control-sm border-secondary" 
                           onchange="this.form.submit()">
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4 py-3">Hora</th>
                            <th class="py-3">Paciente</th>
                            <th class="py-3">Edad</th>
                            <th class="py-3">Condición</th>
                            <th class="py-3">Estado</th>
                            <th class="pe-4 py-3 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($citasHoy as $cita)
                            <tr>
                                <td class="ps-4 py-3 fw-bold text-hrc-primario">
                                    {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                                </td>
                                
                                <td class="py-3">
                                    <span class="d-block text-dark">
                                        <strong>{{ strtoupper($cita->paciente->apellido_paterno . ' ' . $cita->paciente->apellido_materno) }}</strong>, 
                                        {{ $cita->paciente->nombres }}
                                    </span>
                                    <small class="text-muted">DNI: {{ $cita->paciente->numero_documento }}</small>
                                </td>
                                
                                <td class="py-3 text-dark">
                                    {{ \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->age }} años
                                </td>

                                <td class="py-3">
                                    @if($cita->codigo_referencia_sis)
                                        <span class="badge bg-success">SIS - Ref: {{ $cita->codigo_referencia_sis }}</span>
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

                                <td class="pe-4 py-3 text-center">
                                    @if($cita->estado === 'Pendiente')
                                        <form action="{{ route('medico.citas.atender', $cita->id) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-hrc-acento btn-sm px-3 fw-bold">
                                                Atender
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-success fw-bold small">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill me-1" viewBox="0 0 16 16">
                                              <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                            </svg>
                                            Completado
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-inbox opacity-50 mb-3" viewBox="0 0 16 16">                            
                                        <path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 8h4.46l-3.05-3.812A.5.5 0 0 0 11.02 4zM3.81.563A1.5 1.5 0 0 1 4.98 0h6.04a1.5 1.5 0 0 1 1.17.563l3.7 4.625a.5.5 0 0 1 .106.374l-.39 3.124A1.5 1.5 0 0 1 14.117 10H1.883a1.5 1.5 0 0 1-1.489-1.314l-.39-3.124a.5.5 0 0 1 .106-.374zM.125 11.17A.5.5 0 0 1 .5 11H6a.5.5 0 0 1 .5.5 1.5 1.5 0 0 0 3 0 .5.5 0 0 1 .5-.5h5.5a.5.5 0 0 1 .496.562l-.39 3.124A1.5 1.5 0 0 1 14.117 16H1.883a1.5 1.5 0 0 1-1.489-1.314l-.39-3.124a.5.5 0 0 1 .121-.392z"/>
                                        </svg>
                                        <strong class="fs-5">Día libre o sin asignaciones</strong>
                                        <p class="mb-0">No tiene pacientes programados para la fecha seleccionada.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
@endsection