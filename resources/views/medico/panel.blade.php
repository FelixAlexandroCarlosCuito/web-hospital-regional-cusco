@extends('layouts.main')

@section('contenido')
    <main class="contenedor-dashboard">
        
        <div class="cabecera-seccion" style="background-color: #0f172a; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #f8fafc;">Panel Clínico - Hospital Regional del Cusco</h2>
            <p style="margin: 5px 0 0 0; color: #94a3b8;">
                Dr. {{ $medico->nombres }} {{ $medico->apellidos }} | {{ $medico->especialidad->nombre }} | {{ $medico->consultorio }}
            </p>
        </div>

        <section class="contenido-dashboard">
            <div class="cabecera-dashboard" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2 style="margin: 0;">Agenda Clínica</h2>
                
                <!-- Formulario de filtro de fecha (PHP puro) -->
                <form action="{{ route('medico.panel') }}" method="GET" style="display: flex; align-items: center; gap: 10px;">
                    <label for="fecha" style="font-weight: bold; color: #475569;">Fecha a consultar:</label>
                    <input type="date" id="fecha" name="fecha" value="{{ $fechaFiltro }}" 
                           class="input-formulario" 
                           style="padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1;"
                           onchange="this.form.submit()">
                </form>
            </div>

            <div class="tabla-historial-contenedor">
                <table class="tabla-historial">
                    <thead style="background-color: #f1f5f9;">
                        <tr>
                            <th>Hora</th>
                            <th>Paciente</th>
                            <th>Edad</th>
                            <th>Condición</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($citasHoy as $cita)
                            <tr>
                                <td style="font-weight: bold; color: #0284c7;">
                                    {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                                </td>
                                
                                <td>
                                    <!-- Apellidos en mayúscula por estándar médico -->
                                    <strong>{{ strtoupper($cita->paciente->apellido_paterno . ' ' . $cita->paciente->apellido_materno) }}</strong>, 
                                    {{ $cita->paciente->nombres }}
                                    <br>
                                    <span style="font-size: 0.85em; color: #64748b;">DNI: {{ $cita->paciente->numero_documento }}</span>
                                </td>
                                
                                <td>
                                    <!-- Calculamos la edad al vuelo -->
                                    {{ \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->age }} años
                                </td>

                                <td>
                                    @if($cita->codigo_referencia_sis)
                                        <span style="color: #059669; font-weight: bold;">SIS - Ref: {{ $cita->codigo_referencia_sis }}</span>
                                    @else
                                        <span style="color: #475569;">Particular</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="estado-texto estado-{{ strtolower($cita->estado) }}">
                                        {{ $cita->estado }}
                                    </span>
                                </td>

                                <td>
                                    @if($cita->estado === 'Pendiente')
                                        <form action="{{ route('medico.citas.atender', $cita->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-primario" style="padding: 8px 15px; font-size: 0.9em; background-color: #0284c7;">
                                                Atender
                                            </button>
                                        </form>
                                    @else
                                        <span style="color: #059669; font-weight: bold;">✓ Completado</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px; color: #64748b;">
                                    <strong>No tiene pacientes programados para el día de hoy.</strong>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection