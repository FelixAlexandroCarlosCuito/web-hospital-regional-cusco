<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo') - HRC</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo-hospital.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    @yield('css')
</head>
<body class="d-flex flex-column min-vh-100">

    <header class="oculta-impresion header-hrc sticky-top">
        <nav class="navbar navbar-expand-lg navbar-light py-2 py-lg-3">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center me-lg-4" href="{{ route('inicio') }}">
                    <img src="{{ asset('img/logo-hospital.png') }}" alt="Logo Hospital Regional del Cusco" width="42" height="42" class="me-2">
                    <div class="lh-1">
                        <span class="fw-bold fs-5 d-block text-hrc-primario">HRC</span>
                        <span class="d-none d-xl-block text-muted marca-subtitulo">Hospital Regional del Cusco</span>
                    </div>
                </a>

                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPrincipal" aria-controls="navbarPrincipal" aria-expanded="false" aria-label="Abrir menú de navegación">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarPrincipal">
                    {{-- Navegación pública centrada en pantallas de escritorio --}}
                    <ul class="navbar-nav mx-lg-auto my-3 my-lg-0 gap-1 gap-lg-2">
                        <li class="nav-item">
                            <a class="nav-link nav-link-hrc {{ request()->routeIs('inicio') ? 'active' : '' }}" href="{{ route('inicio') }}">
                                Inicio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-hrc {{ request()->routeIs('especialidades') ? 'active' : '' }}" href="{{ route('especialidades') }}">
                                Especialidades
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-hrc {{ request()->routeIs('novedades.*') ? 'active' : '' }}" href="{{ route('novedades.index') }}">
                                Novedades
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-hrc {{ request()->routeIs('guia.contacto') ? 'active' : '' }}" href="{{ route('guia.contacto') }}">
                                Guía y Contacto
                            </a>
                        </li>
                    </ul>

                    {{-- Acciones contextuales y control de sesión por rol --}}
                    <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2 pb-2 pb-lg-0">
                        @auth
                            @php
                                $usuario = Auth::user();
                                $etiquetaRol = match ($usuario->rol) {
                                    'admin' => 'Administrador',
                                    'admision' => 'Admisión',
                                    'medico' => 'Médico',
                                    default => 'Paciente',
                                };
                                $nombreCorto = $usuario->rol === 'medico'
                                    ? 'Dr. ' . $usuario->apellido_paterno
                                    : explode(' ', trim($usuario->nombres))[0];
                            @endphp

                            @if($usuario->rol === 'admin')
                                <a href="{{ route('admin.panel') }}" class="btn btn-outline-hrc-primario btn-sm rounded-pill px-3 text-nowrap">
                                    Panel Admin
                                </a>
                            @elseif($usuario->rol === 'admision')
                                <a href="{{ route('admision.panel') }}" class="btn btn-outline-hrc-primario btn-sm rounded-pill px-3 text-nowrap">
                                    Panel Admisión
                                </a>
                            @elseif($usuario->rol === 'medico')
                                <a href="{{ route('medico.panel') }}" class="btn btn-outline-hrc-primario btn-sm rounded-pill px-3 text-nowrap">
                                    Mi Agenda
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="nav-link nav-link-hrc text-nowrap {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    Mis Citas
                                </a>
                                <a href="{{ route('reservar_cita') }}" class="btn btn-hrc-acento btn-sm rounded-pill px-3 text-nowrap">
                                    Reservar cita
                                </a>
                            @endif

                            <div class="dropdown">
                                <button class="btn btn-usuario-hrc btn-sm rounded-pill dropdown-toggle d-flex align-items-center justify-content-center gap-2 w-100"
                                        type="button"
                                        id="menuUsuario"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                    <i class="bi bi-person-circle"></i>
                                    <span>{{ $nombreCorto }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2 menu-usuario-dropdown" aria-labelledby="menuUsuario">
                                    <li class="px-3 py-2 border-bottom">
                                        <div class="fw-bold text-hrc-primario small">{{ $usuario->nombres }} {{ $usuario->apellido_paterno }}</div>
                                        <div class="text-muted texto-xs">{{ $etiquetaRol }} &middot; {{ $usuario->numero_documento }}</div>
                                    </li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger small py-2 d-flex align-items-center gap-2">
                                                <i class="bi bi-box-arrow-right"></i>
                                                <span>Cerrar Sesión</span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endauth

                        @guest
                            <a href="{{ route('login') }}" class="nav-link nav-link-hrc text-nowrap text-center">
                                Iniciar Sesión
                            </a>
                            <a href="{{ route('registro') }}" class="btn btn-outline-hrc-primario btn-sm rounded-pill px-3 text-nowrap">
                                Registrarse
                            </a>
                            <a href="{{ route('reservar_cita') }}" class="btn btn-hrc-acento btn-sm rounded-pill px-3 text-nowrap">
                                Reservar cita
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-grow-1 @yield('clase_main', 'py-5')">
        @yield('contenido')
    </main>

    <footer class="footer-hrc mt-auto py-5 oculta-impresion text-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/logo-hospital.png') }}" alt="Logo HRC" width="38" height="38" class="me-2">
                        <h5 class="fw-bold mb-0">Hospital Regional del Cusco</h5>
                    </div>
                    <p class="small texto-claro-mate mb-3">
                        Plataforma digital orientada a reducir tiempos de espera y facilitar el acceso a consulta externa para pacientes de Cusco y provincias.
                    </p>
                    <ul class="list-unstyled small texto-claro-mate mb-0 d-flex flex-column gap-1">
                        <li><i class="bi bi-geo-alt me-2"></i>Avenida de La Cultura S/N, Cusco 08000</li>
                        <li><i class="bi bi-telephone me-2"></i>Central Telefónica: +51 084 231131</li>
                        <li><i class="bi bi-hospital me-2"></i>Atención de Emergencias: 24 horas</li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3">
                    <h6 class="fw-bold mb-3">Navegación</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        <li><a href="{{ route('inicio') }}" class="enlace-footer">Inicio</a></li>
                        <li><a href="{{ route('especialidades') }}" class="enlace-footer">Cartera de Especialidades</a></li>
                        <li><a href="{{ route('novedades.index') }}" class="enlace-footer">Novedades y Campañas</a></li>
                        <li><a href="{{ route('guia.contacto') }}" class="enlace-footer">Guía de Atención y Contacto</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-4">
                    <h6 class="fw-bold mb-3">Atención al Paciente</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-3">
                        <li><a href="{{ route('reservar_cita') }}" class="enlace-footer">Reservar Cita en Línea</a></li>
                        @guest
                            <li><a href="{{ route('login') }}" class="enlace-footer">Iniciar Sesión</a></li>
                            <li><a href="{{ route('registro') }}" class="enlace-footer">Registro de Pacientes</a></li>
                        @else
                            @if(Auth::user()->rol === 'paciente')
                                <li><a href="{{ route('dashboard') }}" class="enlace-footer">Consultar Mis Citas</a></li>
                            @endif
                        @endguest
                    </ul>
                    <div class="small texto-claro-mate">
                        <strong class="text-white d-block">Consultorios Externos</strong>
                        Lunes a viernes &middot; 8:00 a. m. a 1:00 p. m.
                    </div>
                </div>
            </div>

            <hr class="my-4 footer-divisor">

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 texto-claro-mate texto-xs">
                <p class="mb-0">&copy; {{ date('Y') }} Hospital Regional del Cusco &middot; Sistema de Gestión de Citas.</p>
                <p class="mb-0">Proyecto académico con fines educativos.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="{{ asset('js/password.js') }}"></script>
    @yield('js')
</body>
</html>