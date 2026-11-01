@extends('layouts.main')

@section('titulo', 'Registro de paciente')

@section('contenido')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold text-hrc-primario">Crear Cuenta de Paciente</h1>
                    <p class="text-muted">Regístrese para acceder al sistema de reservas y su historial médico</p>
                </div>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        
                        <!-- Formulario de Registro - Los IDs son vitales para la validación asíncrona de Reniec -->
                        <form id="formulario-registro" action="{{ route('registrar') }}" method="POST" novalidate>
                            @csrf
                            
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="tipo_documento" class="form-label fw-bold text-secondary">Tipo de Documento</label>
                                    <select name="tipo_documento" id="tipo_documento" class="form-select form-select-lg">
                                        <option value="DNI" selected>DNI</option>
                                        <option value="CE" disabled>Carnet de Extranjería</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="numero_documento" class="form-label fw-bold text-secondary">Número de Documento</label>
                                    <input type="text" id="numero_documento" name="numero_documento" class="form-control form-control-lg">
                                    <span class="mensaje-error d-block text-danger small mt-1" id="error-numero_documento"></span>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="digito_verificador" class="form-label fw-bold text-secondary">Dígito Verificador</label>
                                    <input type="text" id="digito_verificador" name="digito_verificador" maxlength="1" placeholder="Ej: 4" class="form-control form-control-lg">
                                    <span class="mensaje-error d-block text-danger small mt-1" id="error-digito_verificador"></span>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="fecha_emision" class="form-label fw-bold text-secondary">Fecha de Emisión</label>
                                    <input type="date" id="fecha_emision" name="fecha_emision" class="form-control form-control-lg">
                                    <span class="mensaje-error d-block text-danger small mt-1" id="error-fecha_emision"></span>
                                </div>
                            </div>

                            <!-- Botón Fase 1: Desencadena la simulación de API externa (Reniec) -->
                            <div class="d-grid mb-4">
                                <button type="button" id="btn-verificar" class="btn btn-outline-hrc-primario btn-lg fw-bold">Verificar Identidad en RENIEC</button>
                            </div>

                            <!-- FASE 2: Interfaz dinámica inyectada vía JS post-validación -->
                            <div id="seccion-completar-registro" style="display: none;">
                                <hr class="my-4">
                                <h5 class="fw-bold text-hrc-primario mb-3">Complete sus datos de acceso</h5>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label fw-bold text-secondary">Nombres</label>
                                        <input type="text" id="visual_nombres" class="form-control bg-light" readonly disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-secondary">Apellidos</label>
                                        <input type="text" id="visual_apellidos" class="form-control bg-light" readonly disabled>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="correo" class="form-label fw-bold text-secondary">Correo Electrónico (Opcional)</label>
                                    <input type="email" id="correo" name="correo" placeholder="ejemplo@correo.com" class="form-control form-control-lg">
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="password" class="form-label fw-bold text-secondary">Crear contraseña</label>
                                        <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres" class="form-control form-control-lg">
                                        <span class="mensaje-error d-block text-danger small mt-1" id="error-password"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label fw-bold text-secondary">Confirmar Contraseña</label>
                                        <input type="password" id="confirm_password" name="password_confirmation" class="form-control form-control-lg">
                                        <span class="mensaje-error d-block text-danger small mt-1" id="error-confirm_password"></span>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-hrc-acento btn-lg">Completar Registro</button>
                                </div>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="mb-0 text-muted">¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="text-decoration-none fw-bold text-hrc-primario">Inicia sesión aquí.</a></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/registro.js') }}"></script>
@endsection