@extends('layouts.main')

@section('titulo', 'Reservar Cita')

@section('contenido')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-hrc-primario">Reserva tu Cita Médica</h2>
                    <p class="text-muted">Complete los pasos para agendar su atención presencial</p>
                </div>
                
                <!-- Indicador de Progreso -->
                <nav aria-label="Progreso de la reserva" class="mb-5">
                    <ol class="barra-pasos list-unstyled d-flex flex-row justify-content-between gap-2 gap-md-3 mb-0">
                        <!-- Paso 1 -->
                        <li class="activo flex-fill text-center rounded-3 shadow-sm p-3 border">
                            <div class="badge-paso d-inline-flex align-items-center justify-content-center rounded-circle mb-2 fw-bold" style="width: 32px; height: 32px;">1</div>
                            <span class="d-block fw-bold">Especialidad</span>
                        </li>
                        <!-- Paso 2 -->
                        <li class="flex-fill text-center rounded-3 shadow-sm p-3 border bg-white text-muted">
                            <div class="badge-paso d-inline-flex align-items-center justify-content-center rounded-circle mb-2 fw-bold bg-secondary text-white" style="width: 32px; height: 32px;">2</div>
                            <span class="d-block fw-bold">Fecha y Datos</span>
                        </li>
                        <!-- Paso 3 -->
                        <li class="flex-fill text-center rounded-3 shadow-sm p-3 border bg-white text-muted">
                            <div class="badge-paso d-inline-flex align-items-center justify-content-center rounded-circle mb-2 fw-bold bg-secondary text-white" style="width: 32px; height: 32px;">3</div>
                            <span class="d-block fw-bold">Confirmación</span>
                        </li>
                    </ol>
                </nav>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        
                        <form id="formulario-reserva" action="{{ route('citas.store') }}" method="POST">
                            @csrf
                            
                            <!-- PASO 1: Selección del nodo clínico (Especialidad y Médico) -->
                            <div id="paso-1" class="fase-reserva">
                                <h4 class="fw-bold text-hrc-primario mb-4 border-bottom pb-2">1. Área de Atención</h4>
                                
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label for="especialidad" class="form-label fw-bold text-secondary">Especialidad</label>
                                        <select id="especialidad" name="especialidad_id" class="form-select form-select-lg" required>
                                            <option value="" disabled selected>Seleccione Especialidad</option>
                                            @foreach($especialidades as $especialidad)
                                                <option value="{{ $especialidad->id }}">{{ $especialidad->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <span class="mensaje-error d-block text-danger small mt-1" id="error-especialidad"></span>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="medico" class="form-label fw-bold text-secondary">Médico Tratante</label>
                                        <!-- El select se mantiene deshabilitado hasta que el fetch() de JS reciba los médicos disponibles -->
                                        <select id="medico" name="medico_id" class="form-select form-select-lg" required disabled>
                                            <option value="" disabled selected>Primero seleccione una especialidad</option>
                                        </select>
                                        <span class="mensaje-error d-block text-danger small mt-1" id="error-medico"></span>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="button" class="btn btn-hrc-acento btn-lg px-5 btn-siguiente-1">Siguiente Paso</button>
                                </div>
                            </div>

                            <!-- PASO 2: Restricciones temporales e información de contacto -->
                            <div id="paso-2" class="fase-reserva" style="display: none;">
                                <h4 class="fw-bold text-hrc-primario mb-4 border-bottom pb-2">2. Programación de la Cita</h4>
                                
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label for="fecha_cita" class="form-label fw-bold text-secondary">Fecha de la Cita</label>
                                        <input type="date" id="fecha_cita" name="fecha" class="form-control form-control-lg" required>
                                        <span class="mensaje-error d-block text-danger small mt-1" id="error-fecha_cita"></span>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="hora_cita" class="form-label fw-bold text-secondary">Hora Disponible</label>
                                        <!-- Opciones generadas asíncronamente para prevenir colisiones en la jornada -->
                                        <select id="hora_cita" name="hora" class="form-select form-select-lg" required disabled>
                                            <option value="" disabled selected>Seleccione una fecha primero</option>
                                        </select>
                                        <span class="mensaje-error d-block text-danger small mt-1" id="error-hora_cita"></span>
                                    </div>
                                </div>

                                <div class="row g-4 mb-5">
                                    <div class="col-md-6">
                                        <label for="telefono" class="form-label fw-bold text-secondary">Teléfono de Contacto Actual</label>
                                        <input type="tel" id="telefono" name="telefono" value="{{ Auth::user()->telefono }}" class="form-control form-control-lg" required placeholder="Ej: 987654321">
                                        <span class="mensaje-error d-block text-danger small mt-1" id="error-telefono"></span>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="referencia_sis" class="form-label fw-bold text-secondary">Hoja de Referencia (Solo SIS)</label>
                                        <input type="text" id="referencia_sis" name="codigo_referencia_sis" class="form-control form-control-lg" placeholder="Opcional: Código del centro de salud">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary btn-lg px-4 btn-atras-2">Atrás</button>
                                    <button type="button" class="btn btn-hrc-acento btn-lg px-5 btn-siguiente-2">Revisar Resumen</button>
                                </div>
                            </div>

                            <!-- PASO 3: Validación del Payload (Pre-Submit) -->
                            <div id="paso-3" class="fase-reserva" style="display: none;">
                                <h4 class="fw-bold text-hrc-primario mb-4 border-bottom pb-2">3. Confirmación de Datos</h4>
                                
                                <div class="card bg-light border-0 mb-4">
                                    <div class="card-body p-4">
                                        <ul class="list-group list-group-flush lista-resumen bg-transparent">
                                            <li class="list-group-item bg-transparent px-0 d-flex justify-content-between align-items-center">
                                                <span class="text-secondary fw-bold">Paciente:</span>
                                                <span id="resumen-paciente" class="text-dark fw-bold text-end"></span>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 d-flex justify-content-between align-items-center">
                                                <span class="text-secondary fw-bold">Documento:</span>
                                                <span id="resumen-documento" class="text-dark text-end"></span>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 d-flex justify-content-between align-items-center">
                                                <span class="text-secondary fw-bold">Especialidad:</span>
                                                <span id="resumen-especialidad" class="text-hrc-primario fw-bold text-end"></span>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 d-flex justify-content-between align-items-center">
                                                <span class="text-secondary fw-bold">Médico:</span>
                                                <span id="resumen-medico" class="text-dark text-end"></span>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 d-flex justify-content-between align-items-center">
                                                <span class="text-secondary fw-bold">Fecha programada:</span>
                                                <span id="resumen-fecha" class="text-dark text-end"></span>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 d-flex justify-content-between align-items-center">
                                                <span class="text-secondary fw-bold">Hora:</span>
                                                <span id="resumen-hora" class="text-dark text-end"></span>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 d-flex justify-content-between align-items-center">
                                                <span class="text-secondary fw-bold">Teléfono:</span>
                                                <span id="resumen-telefono" class="text-dark text-end"></span>
                                            </li>
                                            <li class="list-group-item bg-transparent px-0 d-flex justify-content-between align-items-center">
                                                <span class="text-secondary fw-bold">Referencia SIS:</span>
                                                <span id="resumen-sis" class="text-dark text-end"></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="alert alert-warning small d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-info-circle me-2 flex-shrink-0" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/></svg>
                                    <div>Al confirmar, el sistema registrará la cita y generará su ticket de atención física.</div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary btn-lg px-4 btn-atras-3">Atrás</button>
                                    <button type="submit" class="btn btn-hrc-acento btn-lg px-4 px-md-5 btn-registro">Confirmar y Generar Ticket</button>
                                </div>
                            </div>
                            
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- PUENTE BLADE -> JAVASCRIPT: Hidratación de datos del backend al frontend -->
    <script>
        window.CatalogoHospital = @json($especialidades);
        window.PacienteActual = {
            nombres: "{{ Auth::user()->nombres }}",
            apellidos: "{{ Auth::user()->apellido_paterno }} {{ Auth::user()->apellido_materno }}",
            documento: "{{ Auth::user()->tipo_documento }} {{ Auth::user()->numero_documento }}"
        };
    </script>
    
    <script src="{{ asset('js/reservar-cita.js') }}"></script>
@endsection