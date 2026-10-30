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
                                <option value="" disabled selected>Seleccione</option>
                                <option value="DNI">DNI</option>
                                <option value="CE">Carnet de Extranjería</option>
                            </select>
                            <span class="mensaje-error" id="error-tipo_documento"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="numero_documento">Número de Documento</label>
                            <!-- El atributo name="..." es vital; es la llave con la que el Controlador leerá este dato -->
                            <input type="text" id="numero_documento" name="numero_documento">
                            <span class="mensaje-error" id="error-numero_documento"></span>
                        </div>
                    </div>

                    <div class="fila-formulario">
                        <div class="form-group">
                            <label for="nombres">Nombres</label>
                            <input type="text" id="nombres" name="nombres">
                            <span class="mensaje-error" id="error-nombres"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="apellidos">Apellidos</label>
                            <input type="text" id="apellidos" name="apellidos">
                            <span class="mensaje-error" id="error-apellidos"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="correo">Correo Electrónico (Opcional)</label>
                        <input type="email" id="correo" name="correo" placeholder="ejemplo@correo.com">
                        <span class="mensaje-error" id="error-correo"></span>
                    </div>

                    <div class="fila-formulario">
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres">
                            <span class="mensaje-error" id="error-password"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirmar Contraseña</label>
                            <!-- 
                               Nota: Laravel puede validar la confirmación automáticamente en el backend 
                               si este input se llama exactamente password_confirmation. 
                               Cambiamos el name de 'confirm_password' a 'password_confirmation' por estándar del framework. 
                            -->
                            <input type="password" id="confirm_password" name="password_confirmation">
                            <span class="mensaje-error" id="error-confirm_password"></span>
                        </div>
                    </div>

                    <button type="submit" class="btn-primario btn-registro">Registrarse</button>
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