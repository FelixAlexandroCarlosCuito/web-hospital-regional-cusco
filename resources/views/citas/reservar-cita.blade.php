@extends('layouts.main')

@section('titulo', 'Inicio')

@section('css')
    <link rel="stylesheet" href="{{asset('css/reservar-cita.css')}}">
@endsection

@section('contenido')
    <main class="contenedor-principal">
        <div class="cabecera-seccion">
            <h2 class="titulo-seccion">Reserva tu Cita Médica</h2>
            <p class="subtitulo-seccion">Complete los pasos para agendar su atención presencial</p>
        </div>
        
        <nav aria-label="Progreso de la reserva">
            <ol class="barra-pasos">
                <li class="activo"><span>1</span> Especialidad</li>
                <li><span>2</span> Fecha y Datos</li>
                <li><span>3</span> Confirmación</li>
            </ol>
        </nav>

        <form id="formulario-reserva" class="formulario-reserva" action="{{ route('citas.store') }}" method="POST">
            @csrf
            
            <!-- PASO 1: Especialidad y Médico -->
            <div id="paso-1" class="fase-reserva">
                <div class="fila-formulario">
                    <div class="form-group">
                        <label for="especialidad">Especialidad</label>
                        <select id="especialidad" name="especialidad_id" required>
                            <option value="" disabled selected>Seleccione Especialidad</option>
                            @foreach($especialidades as $especialidad)
                                <option value="{{ $especialidad->id }}">{{ $especialidad->nombre }}</option>
                            @endforeach
                        </select>
                        <span class="mensaje-error" id="error-especialidad"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="medico">Médico Tratante</label>
                        <select id="medico" name="medico_id" required disabled>
                            <option value="" disabled selected>Primero seleccione una especialidad</option>
                        </select>
                        <span class="mensaje-error" id="error-medico"></span>
                    </div>
                </div>
                <div class="acciones-pasos">
                    <button type="button" class="btn-primario btn-siguiente-1">Siguiente Paso</button>
                </div>
            </div>

            <!-- PASO 2: Fecha, Hora y Datos Adicionales (Requisitos del Hospital) -->
            <div id="paso-2" class="fase-reserva" style="display: none;">
                <div class="fila-formulario">
                    <div class="form-group">
                        <label for="fecha_cita">Fecha de la Cita</label>
                        <input type="date" id="fecha_cita" name="fecha" required>
                        <span class="mensaje-error" id="error-fecha_cita"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="hora_cita">Hora Disponible</label>
                        <select id="hora_cita" name="hora" required disabled>
                            <option value="" disabled selected>Seleccione una fecha primero</option>
                        </select>
                        <span class="mensaje-error" id="error-hora_cita"></span>
                    </div>
                </div>

                <div class="fila-formulario" style="margin-top: 20px;">
                    <div class="form-group">
                        <label for="telefono">Teléfono de Contacto Actual</label>
                        <input type="tel" id="telefono" name="telefono" value="{{ Auth::user()->telefono }}" required placeholder="Ej: 987654321">
                        <span class="mensaje-error" id="error-telefono"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="referencia_sis">Hoja de Referencia (Solo SIS)</label>
                        <input type="text" id="referencia_sis" name="codigo_referencia_sis" placeholder="Opcional: Código del centro de salud">
                    </div>
                </div>

                <div class="acciones-pasos">
                    <button type="button" class="btn-secundario btn-atras-2">Atrás</button>
                    <button type="button" class="btn-primario btn-siguiente-2">Revisar Resumen</button>
                </div>
            </div>

            <!-- PASO 3: Resumen Final -->
            <div id="paso-3" class="fase-reserva" style="display: none;">
                <div class="tarjeta-resumen">
                    <h3>Resumen de su Cita</h3>
                    <ul class="lista-resumen">
                        <li><strong>Paciente:</strong> <span id="resumen-paciente"></span></li>
                        <li><strong>Documento:</strong> <span id="resumen-documento"></span></li>
                        <li><strong>Especialidad:</strong> <span id="resumen-especialidad"></span></li>
                        <li><strong>Médico:</strong> <span id="resumen-medico"></span></li>
                        <li><strong>Fecha:</strong> <span id="resumen-fecha"></span></li>
                        <li><strong>Hora:</strong> <span id="resumen-hora"></span></li>
                        <li><strong>Teléfono de contacto:</strong> <span id="resumen-telefono"></span></li>
                        <li><strong>Referencia SIS:</strong> <span id="resumen-sis"></span></li>
                    </ul>
                </div>
                <div class="acciones-pasos">
                    <button type="button" class="btn-secundario btn-atras-3">Atrás</button>
                    <button type="submit" class="btn-primario btn-registro">Confirmar y Generar Ticket</button>
                </div>
            </div>
        </form>
    </main>
@endsection

@section('js')
    <!-- PUENTE BLADE -> JAVASCRIPT -->
    <script>
        window.CatalogoHospital = @json($especialidades);
        window.PacienteActual = {
            nombres: "{{ Auth::user()->nombres }}",
            apellidos: "{{ Auth::user()->apellido_paterno }} {{ Auth::user()->apellido_materno }}",
            documento: "{{ Auth::user()->tipo_documento }} {{ Auth::user()->numero_documento }}"
        };
    </script>
    
    <!-- Cargamos el script con la lógica del Wizard -->
    <script src="{{ asset('js/reservar-cita.js') }}"></script>
@endsection