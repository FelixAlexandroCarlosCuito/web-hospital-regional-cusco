@extends('layouts.main')

@section('titulo', 'Registro de paciente')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/registro.css') }}">
@endsection

@section('contenido')
    <div class="registro-main">
        <main class="contenedor-registro">
            <div class="registro-header">
                <h1 class="titulo-registro">Crear Cuenta de Paciente</h1>
                <p class="subtitulo-registro">Regístrese para acceder al sistema de reservas y su historial médico</p>
            </div>

            <div class="tarjeta-registro">
                <form id="formulario-registro" class="formulario-registro" action="{{ route('registrar') }}" method="POST" novalidate>
                    @csrf
                    
                    <div class="fila-formulario">
                        <div class="form-group">
                            <label for="tipo_documento">Tipo de Documento</label>
                            <select name="tipo_documento" id="tipo_documento">
                                <option value="DNI" selected>DNI</option>
                                <option value="CE" disabled>Carnet de Extranjería</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="numero_documento">Número de Documento</label>
                            <input type="text" id="numero_documento" name="numero_documento">
                            <span class="mensaje-error" id="error-numero_documento"></span>
                        </div>
                    </div>

                    <div class="fila-formulario">
                        <div class="form-group">
                            <label for="digito_verificador">Dígito Verificador</label>
                            <input type="text" id="digito_verificador" name="digito_verificador" maxlength="1" placeholder="Ej: 4">
                            <span class="mensaje-error" id="error-digito_verificador"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha_emision">Fecha de Emisión</label>
                            <input type="date" id="fecha_emision" name="fecha_emision">
                            <span class="mensaje-error" id="error-fecha_emision"></span>
                        </div>
                    </div>

                    <!-- Botón asíncrono para verificar -->
                    <button type="button" id="btn-verificar" class="btn-secundario btn-registro" style="margin-bottom: 20px;">Verificar Identidad en RENIEC</button>

                    <!-- FASE 2: Oculta por defecto. Se revelará con JS -->
                    <div id="seccion-completar-registro" style="display: none;">
                        
                        <!-- Campos visuales de confirmación (Bloqueados) -->
                        <div class="fila-formulario">
                            <div class="form-group">
                                <label>Nombres</label>
                                <input type="text" id="visual_nombres" class="input-bloqueado" readonly disabled>
                            </div>
                            <div class="form-group">
                                <label>Apellidos</label>
                                <input type="text" id="visual_apellidos" class="input-bloqueado" readonly disabled>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="correo">Correo Electrónico (Opcional)</label>
                            <input type="email" id="correo" name="correo" placeholder="ejemplo@correo.com">
                        </div>

                        <div class="fila-formulario">
                            <div class="form-group">
                                <label for="password">Crear contraseña</label>
                                <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres">
                                <span class="mensaje-error" id="error-password"></span>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirmar Contraseña</label>
                                <input type="password" id="confirm_password" name="password_confirmation">
                                <span class="mensaje-error" id="error-confirm_password"></span>
                            </div>
                        </div>

                        <button type="submit" class="btn-primario btn-registro">Completar Registro</button>
                    </div>
                </form>

                <div class="registro-enlaces">
                    <!-- Uso correcto del helper route() para evitar URLs estáticas propensas a romperse -->
                    <p>¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia sesión aquí.</a></p>
                </div>
            </div>
        </main>
    </div>
@endsection

@section('js')
    <script src="{{asset('js/registro.js')}}"></script>
@endsection