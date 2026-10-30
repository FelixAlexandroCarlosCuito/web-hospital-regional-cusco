@extends('layouts.main')

@section('titulo', 'Inicio')

@section('css')
    <link rel="stylesheet" href="{{asset('css/reservar-cita.css')}}">
@endsection

@section('contenido')
    <main class="contenedor-principal">
        <div class="cabecera-seccion">
            <h2 class="titulo-seccion">Reserva tu Cita Médica</h2>
            <p class="subtitulo-seccion">Completa los pasos para agendar tu atención presencial</p>
        </div>
        
        <nav aria-label="Progreso de la reserva">
            <ol class="barra-pasos">
                <li class="activo"><span>1</span> Paciente</li>
                <li><span>2</span> Especialidad</li>
                <li><span>3</span> Fecha y Hora</li>
                <li><span>4</span> Confirmación</li>
            </ol>
        </nav>

        <form id="formulario-reserva" class="formulario-citas" novalidate>
            
            <section id="paso-1" class="tarjeta-paso">
                <h3 class="titulo-paso">Identificación del Paciente</h3>
                
                <div class="fila-formulario">
                    <div class="form-group">
                        <label for="tipo_documento">Tipo de Documento</label>
                        <select name="tipo_documento" id="tipo_documento">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="DNI">DNI</option>
                            <option value="CE">Carnet de Extranjería</option>
                            <option value="PAS">Pasaporte</option>
                        </select>
                        <span class="mensaje-error" id="error-tipo_documento"></span>
                    </div>

                    <div class="form-group form-group-btn">
                        <div class="input-wrapper">
                            <label for="numero_documento">Número de Documento</label>
                            <input type="text" name="numero_documento" id="numero_documento" placeholder="Ingrese su documento">
                            <span class="mensaje-error" id="error-numero_documento"></span>
                        </div>
                        <button type="button" class="btn-secundario btn-validar">Validar</button>
                    </div>
                </div>

                <hr class="separador">

                <div class="fila-formulario">
                    <div class="form-group">
                        <label for="nombres">Nombres</label>
                        <input type="text" name="nombres" id="nombres" placeholder="Ingrese sus nombres">
                    </div>
                    <div class="form-group">
                        <label for="apellidos">Apellidos</label>
                        <input type="text" name="apellidos" id="apellidos" placeholder="Ingrese sus apellidos">
                    </div>
                </div>
                <span class="mensaje-error" id="error-validacion-general"></span>

                <div class="acciones-formulario alineacion-derecha">
                    <button type="button" class="btn-primario btn-siguiente-1">Continuar</button>
                </div>
            </section>

            <section id="paso-2" class="tarjeta-paso">
                <h3 class="titulo-paso">Selección de Especialidad</h3>
                <div class="fila-formulario">
                    <div class="form-group">
                        <label for="especialidad">Especialidad Requerida</label>
                        <select name="especialidad" id="especialidad">
                            <option value="" disabled selected>Cargando especialidades...</option>
                            <!-- El JS llenará esto automáticamente desde Laravel -->
                            </select>
                            <span class="mensaje-error" id="error-especialidad"></span>
                        </div>

                    <div class="form-group">
                        <label for="medico">Médico (Opcional)</label>
                        <select name="medico" id="medico" disabled>
                            <option value="" selected>Seleccione especialidad primero</option>
                        </select>
                    </div>
                </div>

                <div class="acciones-formulario">
                    <button type="button" class="btn-outline btn-atras-2">Atrás</button>
                    <button type="button" class="btn-primario btn-siguiente-2">Continuar</button>
                </div>
            </section>

            <section id="paso-3" class="tarjeta-paso">
                <h3 class="titulo-paso">Fecha y Hora de la Cita</h3>
                
                <div class="fila-formulario">
                    <div class="form-group">
                        <label for="fecha_cita">Seleccione la Fecha</label>
                        <input type="date" name="fecha_cita" id="fecha_cita">
                        <span class="mensaje-error" id="error-fecha_cita"></span>
                    </div>

                    <div class="form-group">
                        <label for="hora_cita">Horarios Disponibles</label>
                        <select name="hora_cita" id="hora_cita">
                            <option value="" disabled selected>Elija un turno</option>
                            <option value="08:00">08:00 AM</option>
                            <option value="08:30">08:30 AM</option>
                            <option value="09:00">09:00 AM</option>
                            <option value="09:30">09:30 AM</option>
                            <option value="10:00">10:00 AM</option>
                        </select>
                        <span class="mensaje-error" id="error-hora_cita"></span>
                    </div>
                </div>

                <div class="acciones-formulario">
                    <button type="button" class="btn-outline btn-atras-3">Atrás</button>
                    <button type="button" class="btn-primario btn-siguiente-3">Continuar</button>
                </div>
            </section>

            <section id="paso-4" class="tarjeta-paso">
                <h3 class="titulo-paso">Resumen y Confirmación</h3>
                
                <div class="alerta-info">
                    Por favor, verifique cuidadosamente que los datos de su cita sean los correctos antes de confirmar.
                </div>

                <div class="caja-resumen">
                    <dl class="lista-resumen">
                        <div class="item-resumen">
                            <dt>Paciente</dt>
                            <dd id="resumen-paciente"></dd>
                        </div>
                        <div class="item-resumen">
                            <dt>Documento</dt>
                            <dd id="resumen-documento"></dd>
                        </div>
                        <div class="item-resumen">
                            <dt>Especialidad</dt>
                            <dd id="resumen-especialidad"></dd>
                        </div>
                        <div class="item-resumen">
                            <dt>Médico</dt>
                            <dd id="resumen-medico"></dd>
                        </div>
                        <div class="item-resumen">
                            <dt>Fecha</dt>
                            <dd id="resumen-fecha"></dd>
                        </div>
                        <div class="item-resumen">
                            <dt>Hora</dt>
                            <dd id="resumen-hora"></dd>
                        </div>
                    </dl>
                </div>

                <div class="acciones-formulario">
                    <button type="button" class="btn-outline btn-atras-4">Modificar Datos</button>
                    <button type="submit" class="btn-exito">Confirmar Cita Médica</button>
                </div>
            </section>
        </form>
    </main>
@endsection

@section('js')
    <script src="{{asset('js/reservar-cita.js')}}"></script>
@endsection