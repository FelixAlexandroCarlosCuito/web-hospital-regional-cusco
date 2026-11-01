@extends('layouts.main')

@section('titulo', 'Inicio de sesión')

@section('contenido')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5">
                
                <!-- Encabezado -->
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold text-hrc-primario">Bienvenido</h1>
                    <p class="text-muted">Inicia sesión en el portal institucional</p>
                </div>

                <!-- Tarjeta del Formulario -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form id="formulario-login" action="{{ route('login') }}" method="POST" novalidate>
                            @csrf
                            
                            <div class="mb-4">
                                <label for="documento" class="form-label fw-bold text-secondary">Número de Documento (DNI / CE)</label>
                                <input type="text" id="documento" name="numero_documento" value="{{ old('numero_documento') }}" class="form-control form-control-lg @error('numero_documento') is-invalid @enderror" placeholder="Ingrese su documento">
                                <span class="mensaje-error d-block text-danger small mt-1" id="error-documento">
                                    @error('numero_documento') {{ $message }} @enderror
                                </span>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold text-secondary">Contraseña</label>
                                <input type="password" id="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Ingrese su contraseña">
                                       
                                <span class="mensaje-error d-block text-danger small mt-1" id="error-password">
                                    @error('password') {{ $message }} @enderror
                                </span>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-hrc-acento btn-lg">Acceder</button>
                            </div>
                        </form>

                        <!-- Enlaces de ayuda -->
                        <div class="text-center mt-4">
                            <p class="mb-2"><a href="#" class="text-decoration-none text-hrc-primario">¿Olvidaste tu contraseña?</a></p>
                            <p class="mb-0 text-muted">¿No tienes una cuenta? <a href="{{ route('registro') }}" class="text-decoration-none fw-bold text-hrc-primario">Crea una ahora.</a></p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted small">Puede encontrar asistencia en línea en la sección <a href="#" class="text-decoration-none text-hrc-primario fw-bold">Ayuda de la cuenta.</a></p>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/inicio-sesion.js') }}"></script>
@endsection