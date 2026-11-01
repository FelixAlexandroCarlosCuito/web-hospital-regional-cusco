@extends('layouts.main')

@section('contenido')
    <main class="contenedor-dashboard">
        
        <div class="cabecera-seccion" style="background-color: #0f766e; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #f0fdfa;">Centro de Comando - Admisión y Ventanilla</h2>
            <p style="margin: 5px 0 0 0; color: #ccfbf1;">Gestión global de citas y control de agendas médicas</p>
        </div>

        <section class="contenido-dashboard">
            <!-- Panel de Búsqueda y Filtros -->
            <div style="background-color: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
                <form action="{{ route('admision.panel') }}" method="GET" style="display: flex; gap: 15px; align-items: flex-end;">
                    
                    <div style="flex: 1;">
                        <label for="medico_id" style="font-weight: bold; display: block; margin-bottom: 5px;">Consultar Agenda de:</label>
                        <select name="medico_id" id="medico_id" class="input-formulario" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1;">
                            <option value="" disabled {{ !$medicoFiltro ? 'selected' : '' }}>Seleccione un médico</option>
                            @foreach($medicos as $medico)
                                <option value="{{ $medico->id }}" {{ $medicoFiltro == $medico->id ? 'selected' : '' }}>
                                    Dr. {{ $medico->apellidos }} {{ $medico->nombres }} ({{ $medico->especialidad->nombre }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="fecha" style="font-weight: bold; display: block; margin-bottom: 5px;">Para la fecha:</label>
                        <input type="date" name="fecha" id="fecha" value="{{ $fechaFiltro }}" class="input-formulario" required style="padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    </div>

                    <div>
                        <button type="submit" class="btn-primario" style="padding: 10px 20px; background-color: #0f766e; height: 100%;">Buscar Agenda</button>
                    </div>
                </form>
            </div>

            <!-- Resultados de la Agenda -->
            @if($medicoFiltro)
                <div class="tabla-historial-contenedor">
                    <h3 style="margin-bottom: 15px;">Pacientes Programados ({{ $citas->count() }})</h3>
                    <table class="tabla-historial">
                        <thead style="background-color: #f1f5f9;">
                            <tr>
                                <th>Orden</th>
                                <th>Hora</th>
                                <th>Paciente (DNI)</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Gestión</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($citas as $index => $cita)
                                <tr>
                                    <td style="font-weight: bold;">#{{ $index + 1 }}</td>
                                    <td style="color: #0284c7; font-weight: bold;">
                                        {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                                    </td>
                                    <td>
                                        <strong>{{ strtoupper($cita->paciente->apellido_paterno . ' ' . $cita->paciente->apellido_materno) }}</strong>, {{ $cita->paciente->nombres }}
                                        <br>
                                        <span style="font-size: 0.85em; color: #64748b;">{{ $cita->paciente->numero_documento }}</span>
                                    </td>
                                    <td>
                                        {{ $cita->codigo_referencia_sis ? 'SIS' : 'Particular' }}
                                    </td>
                                    <td>
                                        <span class="estado-texto estado-{{ strtolower($cita->estado) }}">
                                            {{ $cita->estado }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('ticket', $cita->id) }}" target="_blank" class="btn-outline" style="padding: 5px 10px; font-size: 0.85em;">Imprimir Ticket</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 30px; color: #64748b;">
                                        No hay pacientes programados para este médico en la fecha seleccionada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 50px; color: #94a3b8; background-color: #f8fafc; border-radius: 8px;">
                    Seleccione un médico y una fecha en el panel superior para visualizar la agenda completa.
                </div>
            @endif
        </section>
    </main>
@endsection