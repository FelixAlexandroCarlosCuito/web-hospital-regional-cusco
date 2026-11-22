@extends('layouts.main')

@section('titulo', 'Registro de paciente')
@section('clase_main', 'p-0 d-flex flex-column')

@section('contenido')
    @php
        $tieneErrorFase2 = $errors->hasAny(['correo', 'password']);
    @endphp

    <section class="auth-portada-completa flex-grow-1 d-flex align-items-center position-relative overflow-hidden py-4">        
        <img src="{{ asset('img/hospital-regional-foto.png') }}"
             alt="Hospital Regional del Cusco"
             class="auth-fondo-foto">
        <div class="auth-fondo-velo" aria-hidden="true"></div>

        <div class="container position-relative">
            <div class="row justify-content-center justify-content-lg-start">
                <div class="col-12 col-lg-8 col-xl-7">
                    <div class="tarjeta-registro-editorial bg-white">

                        <div class="p-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset('img/logo-hospital.png') }}" alt="Logo HRC" width="28" height="28">
                                    <span class="fw-bold text-hrc-primario small">Hospital Regional del Cusco</span>
                                </div>

                                {{-- Indicador ligero de avance entre validación RENIEC y credenciales --}}
                                <div class="d-flex align-items-center gap-2 texto-xs" aria-label="Progreso del registro">
                                    <span id="indicador-paso-1" class="paso-registro-chip {{ $tieneErrorFase2 ? 'completado' : 'activo' }}">
                                        1. Identidad
                                    </span>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                    <span id="indicador-paso-2" class="paso-registro-chip {{ $tieneErrorFase2 ? 'activo' : '' }}">
                                        2. Acceso
                                    </span>
                                </div>
                            </div>

                            <h1 class="h4 fw-bold text-hrc-primario mb-1">Crear cuenta de paciente</h1>
                            <p class="text-muted small mb-3">
                                Valida los datos de tu DNI físico para habilitar tu acceso a reservas en línea.
                            </p>

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show border-0 small py-2 mb-3" role="alert">
                                    <strong class="d-block mb-1">No se pudo completar el registro:</strong>
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>

                                    @if ($errors->has('numero_documento'))
                                        <div class="mt-2 pt-2 border-top border-danger">
                                            <a href="{{ route('login') }}" class="alert-link fw-bold">
                                                Ir a Iniciar Sesión <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    @endif

                                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                                </div>
                            @endif

                            <form id="formulario-registro"
                                  action="{{ route('registrar') }}"
                                  data-login-url="{{ route('login') }}"
                                  data-reniec-url="{{ route('api.reniec') }}"
                                  data-recuperar-fase2="{{ $tieneErrorFase2 ? '1' : '0' }}"
                                  method="POST"
                                  novalidate>
                                @csrf

                                {{-- Fase 1: Datos del documento físico para consulta RENIEC --}}
                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <label for="tipo_documento" class="form-label small fw-bold text-hrc-primario mb-1">
                                            Tipo de Documento
                                        </label>
                                        <select name="tipo_documento"
                                                id="tipo_documento"
                                                class="form-select input-mate py-2 @error('tipo_documento') is-invalid @enderror">
                                            <option value="DNI" {{ old('tipo_documento', 'DNI') === 'DNI' ? 'selected' : '' }}>DNI</option>
                                            <option value="CE" disabled>Carnet de Extranjería (próximamente)</option>
                                        </select>
                                        <span class="mensaje-error d-block text-danger texto-xs mt-1" id="error-tipo_documento">
                                            @error('tipo_documento') {{ $message }} @enderror
                                        </span>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="numero_documento" class="form-label small fw-bold text-hrc-primario mb-1">
                                            Número de DNI
                                        </label>
                                        <input type="text"
                                               id="numero_documento"
                                               name="numero_documento"
                                               value="{{ old('numero_documento') }}"
                                               maxlength="8"
                                               inputmode="numeric"
                                               placeholder="8 dígitos"
                                               class="form-control input-mate py-2 @error('numero_documento') is-invalid @enderror">
                                        <span class="mensaje-error d-block text-danger texto-xs mt-1" id="error-numero_documento">
                                            @error('numero_documento') {{ $message }} @enderror
                                        </span>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="digito_verificador" class="form-label small fw-bold text-hrc-primario mb-1">
                                            Dígito Verificador
                                        </label>
                                        <input type="text"
                                               id="digito_verificador"
                                               name="digito_verificador"
                                               value="{{ old('digito_verificador') }}"
                                               maxlength="1"
                                               placeholder="Ej: 4 (después del guion)"
                                               class="form-control input-mate py-2 @error('digito_verificador') is-invalid @enderror">
                                        <span class="mensaje-error d-block text-danger texto-xs mt-1" id="error-digito_verificador">
                                            @error('digito_verificador') {{ $message }} @enderror
                                        </span>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="fecha_emision" class="form-label small fw-bold text-hrc-primario mb-1">
                                            Fecha de Emisión
                                        </label>
                                        <input type="date"
                                               id="fecha_emision"
                                               name="fecha_emision"
                                               value="{{ old('fecha_emision') }}"
                                               max="{{ now()->toDateString() }}"
                                               class="form-control input-mate py-2 @error('fecha_emision') is-invalid @enderror">
                                        <span class="mensaje-error d-block text-danger texto-xs mt-1" id="error-fecha_emision">
                                            @error('fecha_emision') {{ $message }} @enderror
                                        </span>
                                    </div>
                                </div>

                                <div id="bloque-accion-verificar" class="d-grid mb-2" style="{{ $tieneErrorFase2 ? 'display: none;' : '' }}">
                                    <button type="button" id="btn-verificar" class="btn btn-hrc-acento rounded-pill py-2">
                                        Verificar identidad en RENIEC
                                    </button>
                                </div>

                                {{-- Fase 2: Datos confirmados por RENIEC y creación de contraseña --}}
                                <div id="seccion-completar-registro" style="{{ $tieneErrorFase2 ? 'display: block;' : 'display: none;' }}">
                                    <div class="bloque-identidad-verificada p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                            <span class="texto-xs fw-bold text-hrc-salvia">
                                                <i class="bi bi-patch-check-fill me-1"></i>Identidad validada
                                            </span>
                                            <button type="button"
                                                    id="btn-cambiar-documento"
                                                    class="btn btn-link p-0 texto-xs fw-semibold text-hrc-primario text-decoration-underline">
                                                Cambiar documento
                                            </button>
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-sm-6">
                                                <label for="visual_nombres" class="form-label texto-xs text-muted mb-1">Nombres</label>
                                                <input type="text" id="visual_nombres" class="form-control form-control-sm input-verificado-mate" readonly disabled>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="visual_apellidos" class="form-label texto-xs text-muted mb-1">Apellidos</label>
                                                <input type="text" id="visual_apellidos" class="form-control form-control-sm input-verificado-mate" readonly disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="correo" class="form-label small fw-bold text-hrc-primario mb-1">
                                            Correo Electrónico <span class="fw-normal text-muted">(Opcional)</span>
                                        </label>
                                        <input type="email"
                                               id="correo"
                                               name="correo"
                                               value="{{ old('correo') }}"
                                               autocomplete="email"
                                               placeholder="ejemplo@correo.com"
                                               class="form-control input-mate py-2 @error('correo') is-invalid @enderror">
                                        <span class="mensaje-error d-block text-danger texto-xs mt-1" id="error-correo">
                                            @error('correo') {{ $message }} @enderror
                                        </span>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-sm-6">
                                            <label for="password" class="form-label small fw-bold text-hrc-primario mb-1">
                                                Crear contraseña
                                            </label>
                                            <div class="campo-password-mate position-relative">
                                                <input type="password"
                                                       id="password"
                                                       name="password"
                                                       autocomplete="new-password"
                                                       placeholder="Mínimo 6 caracteres"
                                                       class="form-control input-mate input-password-mate py-2 @error('password') is-invalid @enderror">
                                                <button type="button"
                                                        class="btn-ojo-interno btn-toggle-password"
                                                        data-target="password"
                                                        aria-label="Mostrar contraseña"
                                                        aria-pressed="false">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                            <span class="mensaje-error d-block text-danger texto-xs mt-1" id="error-password">
                                                @error('password') {{ $message }} @enderror
                                            </span>
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="confirm_password" class="form-label small fw-bold text-hrc-primario mb-1">
                                                Confirmar contraseña
                                            </label>
                                            <div class="campo-password-mate position-relative">
                                                <input type="password"
                                                       id="confirm_password"
                                                       name="password_confirmation"
                                                       autocomplete="new-password"
                                                       placeholder="Repite tu contraseña"
                                                       class="form-control input-mate input-password-mate py-2">
                                                <button type="button"
                                                        class="btn-ojo-interno btn-toggle-password"
                                                        data-target="confirm_password"
                                                        aria-label="Mostrar contraseña"
                                                        aria-pressed="false">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                            <span class="mensaje-error d-block text-danger texto-xs mt-1" id="error-confirm_password"></span>
                                        </div>
                                    </div>

                                    <div class="d-grid mt-3">
                                        <button type="submit" id="btn-submit-registro" class="btn btn-hrc-acento rounded-pill py-2">
                                            Completar registro
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="auth-pie-registro px-4 py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <span class="fw-semibold text-hrc-primario small">
                                ¿Ya tienes una cuenta registrada?
                            </span>
                            <a href="{{ route('login') }}" class="enlace-mate small fw-bold text-decoration-none">
                                Inicia sesión aquí <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('js/registro.js') }}"></script>
@endsection