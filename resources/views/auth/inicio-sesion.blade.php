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
            <div class="row align-items-center justify-content-center justify-content-lg-between g-4">
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

                {{-- Panel estático temporal de credenciales ficticias para evaluación académica --}}
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6">
                    <aside class="tarjeta-login-editorial bg-white" aria-label="Credenciales de prueba">
                        <div class="p-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <span class="sobretitulo-mate">Acceso rápido para evaluación</span>
                                <span class="badge-categoria-mate">Datos ficticios</span>
                            </div>

                            <h2 class="h5 fw-bold text-hrc-primario mb-1">Cuentas de prueba por rol</h2>
                            <p class="text-muted texto-xs mb-3">
                                Credenciales precargadas para verificar los paneles y controles de acceso del sistema.
                            </p>

                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0 small">
                                    <thead class="text-muted texto-xs text-uppercase">
                                        <tr>
                                            <th class="ps-0">Rol / Perfil</th>
                                            <th>DNI</th>
                                            <th class="pe-0">Contraseña</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="ps-0">
                                                <strong class="text-hrc-primario d-block">Administrador</strong>
                                                <span class="texto-xs text-muted">KPIs, catálogo, personal y novedades</span>
                                            </td>
                                            <td><span class="doc-codigo-inline fw-semibold">76869228</span></td>
                                            <td class="pe-0"><span class="doc-codigo-inline">admin1</span></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0">
                                                <strong class="text-hrc-primario d-block">Admisión</strong>
                                                <span class="texto-xs text-muted">Agenda por médico y cancelación</span>
                                            </td>
                                            <td><span class="doc-codigo-inline fw-semibold">11223344</span></td>
                                            <td class="pe-0"><span class="doc-codigo-inline">11223344</span></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0">
                                                <strong class="text-hrc-primario d-block">Médico activo</strong>
                                                <span class="texto-xs text-muted">Dr. Sumaq Qori · Medicina General</span>
                                            </td>
                                            <td><span class="doc-codigo-inline fw-semibold">77665544</span></td>
                                            <td class="pe-0"><span class="doc-codigo-inline">77665544</span></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0">
                                                <strong class="text-hrc-primario d-block">Paciente</strong>
                                                <span class="texto-xs text-muted">Luz Marina · 2/3 citas y candado 12h</span>
                                            </td>
                                            <td><span class="doc-codigo-inline fw-semibold">70112233</span></td>
                                            <td class="pe-0"><span class="doc-codigo-inline">123456</span></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 border-bottom-0">
                                                <strong class="text-danger d-block">Médico inactivo</strong>
                                                <span class="texto-xs text-muted">Prueba de bloqueo por baja lógica</span>
                                            </td>
                                            <td class="border-bottom-0"><span class="doc-codigo-inline fw-semibold">45556677</span></td>
                                            <td class="pe-0 border-bottom-0"><span class="doc-codigo-inline">45556677</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="auth-pie-registro px-4 py-2 texto-xs text-muted">
                            <i class="bi bi-info-circle me-1 text-hrc-primario"></i>
                            Panel temporal habilitado únicamente para pruebas y demostración en aula.
                        </div>
                    </aside>
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