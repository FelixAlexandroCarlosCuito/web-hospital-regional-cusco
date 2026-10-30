@extends('layouts.main')

@section('titulo','Inicio de sesión')

@section('css')
    <link rel="stylesheet" href="{{asset('css/inicio-sesion.css')}}">
@endsection

@section('contenido')
    <main class="contenedor-login">
        <div class="login-main">
            <div class="login-header">
                <h1 class="titulo-login">Bienvenido</h1>
                <p class="subtitulo-login">Inicia sesión en el portal del paciente</p>
            </div>

            <div class="tarjeta-login">
                <form id="formulario-login" class="formulario-login" action="{{ route('login') }}" method="POST" novalidate>
                    @csrf
                    
                    <div class="form-group">
                        <label for="documento">Número de Documento (DNI / CE)</label>
                        <input type="text" id="documento" name="documento">
                        <span class="mensaje-error" id="error-documento"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password">
                        <span class="mensaje-error" id="error-password"></span>
                    </div>

                    <button type="submit" class="btn-primario btn-acceso">Acceso</button>
                </form>

                <div class="login-enlaces">
                    <p><a href="#">¿Olvidaste tu contraseña?</a></p>
                    <p>¿No tienes una cuenta? <a href="{{ route('registro') }}">Crea una ahora.</a></p>
                </div>
            </div>

            <p class="login-ayuda">Puede encontrar asistencia en línea en la sección <a href="#">Ayuda de la cuenta.</a></p>
        </div>        
    </main>
@endsection

@section('js')
    <script src="{{asset('js/inicio-sesion.js')}}"></script>
@endsection