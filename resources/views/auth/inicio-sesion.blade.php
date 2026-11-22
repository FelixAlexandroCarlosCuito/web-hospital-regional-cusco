@extends('layouts.main')

@section('titulo', 'Iniciar Sesión')
@section('clase_main', 'p-0 d-flex flex-column')

@section('contenido')
    <section class="auth-portada-completa flex-grow-1 d-flex align-items-center position-relative overflow-hidden py-4">
        <img src="{{ asset('img/hospital-regional-foto.png') }}"
             alt="Hospital Regional del Cusco"
             class="auth-fondo-foto">
        <div class="auth-fondo-velo" aria-hidden="true"></div>

        <div class="container position-relative">
            <div class="row justify-content-center justify-content-lg-start">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                    <div class="tarjeta-login-editorial bg-white">

                        <div class="p-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <img src="{{ asset('img/logo-hospital.png') }}" alt="Logo HRC" width="28" height="28">
                                <span class="fw-bold text-hrc-primario small">Hospital Regional del Cusco</span>
                            </div>

                            <h1 class="h4 fw-bold text-hrc-primario mb-1">Iniciar sesión</h1>
                            <p class="text-muted small mb-3">
                                Accede con tu documento de identidad y contraseña.
                            </p>

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show border-0 small py-2 mb-3" role="alert">
                                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show border-0 small py-2 mb-3" role="alert">
                                    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                                </div>
                            @endif

                            <form id="formulario-login" action="{{ route('login') }}" method="POST" novalidate>
                                @csrf

                                <div class="mb-3">
                                    <label for="documento" class="form-label small fw-bold text-hrc-primario mb-1">
                                        Número de Documento (DNI / CE)
                                    </label>
                                    <input type="text"
                                           id="documento"
                                           name="numero_documento"
                                           value="{{ old('numero_documento') }}"
                                           maxlength="9"
                                           autocomplete="username"
                                           class="form-control input-mate py-2 @error('numero_documento') is-invalid @enderror"
                                           placeholder="Ej: 71234567"
                                           required>
                                    <span class="mensaje-error d-block text-danger texto-xs mt-1" id="error-documento">
                                        @error('numero_documento') {{ $message }} @enderror
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label small fw-bold text-hrc-primario mb-1">
                                        Contraseña
                                    </label>
                                    <div class="campo-password-mate position-relative">
                                        <input type="password"
                                               id="password"
                                               name="password"
                                               autocomplete="current-password"
                                               class="form-control input-mate input-password-mate py-2 @error('password') is-invalid @enderror"
                                               placeholder="Ingresa tu contraseña"
                                               required>
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

                                <div class="d-grid mt-3 mb-3">
                                    <button type="submit" id="btn-submit-login" class="btn btn-hrc-acento rounded-pill py-2">
                                        Acceder
                                    </button>
                                </div>
                            </form>

                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <button type="button"
                                        class="btn btn-link p-0 text-decoration-underline text-hrc-primario texto-xs fw-semibold"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalRecuperarAcceso">
                                    ¿Necesitas ayuda para ingresar?
                                </button>
                                <a href="{{ route('guia.contacto') }}" class="texto-xs text-muted text-decoration-none enlace-ayuda-auth">
                                    Guía de atención
                                </a>
                            </div>
                        </div>

                        <div class="auth-pie-registro px-4 py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <span class="fw-semibold text-hrc-primario small">
                                ¿Aún no tienes una cuenta?
                            </span>
                            <a href="{{ route('registro') }}" class="enlace-mate small fw-bold text-decoration-none">
                                Regístrate ahora <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal informativo para recuperación presencial mientras se habilita el restablecimiento en línea --}}
    <div class="modal fade" id="modalRecuperarAcceso" tabindex="-1" aria-labelledby="modalRecuperarTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow modal-mate">
                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold text-hrc-primario" id="modalRecuperarTitulo">
                        Asistencia de acceso
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body px-4 py-3 small text-muted">
                    <p class="mb-3">
                        Si olvidaste tu contraseña o tienes inconvenientes con tu documento registrado, solicita el restablecimiento verificando tu identidad.
                    </p>
                    <div class="resumen-reserva-mate p-3">
                        <div class="fw-bold text-hrc-primario mb-1">Canales de soporte</div>
                        <div class="mb-1">
                            <i class="bi bi-telephone me-1 text-hrc-acento"></i>Central Telefónica: <strong>(084) 231131</strong>
                        </div>
                        <div>
                            <i class="bi bi-geo-alt me-1 text-hrc-acento"></i>Ventanilla de Admisión (presentar DNI físico vigente)
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-hrc-acento btn-sm rounded-pill px-4" data-bs-dismiss="modal">
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/inicio-sesion.js') }}"></script>
@endsection