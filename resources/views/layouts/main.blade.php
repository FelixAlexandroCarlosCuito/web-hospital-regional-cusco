<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo') - HRC</title>
    <link rel="icon" type="image" href="{{ asset('img/logo-hospital.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    @yield('css')
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    <!-- Encabezado y Navegación Principal -->
    <header class="oculta-impresion shadow-sm bg-white">
        <nav class="navbar navbar-expand-lg navbar-light py-3">
            <div class="container">
                
                <!-- Identidad Institucional -->
                <a class="navbar-brand d-flex align-items-center" href="{{ route('inicio') }}">
                    <img src="{{ asset('img/logo-hospital.png') }}" alt="Logo HRC" width="50" class="me-3">
                    <span class="fw-bold h4 mb-0 text-hrc-primario">Hospital Regional del Cusco</span>
                </a>

                <!-- Botón de menú colapsable para dispositivos móviles -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPrincipal" aria-controls="navbarPrincipal" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Menú de Enlaces -->
                <div class="collapse navbar-collapse" id="navbarPrincipal">
                    <ul class="navbar-nav ms-auto align-items-center gap-3">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('inicio') }}">Inicio</a>
                        </li>
                        
                        <!-- Control de Acceso Basado en Roles (RBAC) para el enrutamiento visual -->
                        @auth
                            @if(Auth::user()->rol === 'admision')
                                <li class="nav-item"><a href="{{ route('admision.panel') }}" class="nav-link fw-bold text-hrc-acento">Centro de Comando</a></li>
                            @elseif(Auth::user()->rol === 'medico')
                                <li class="nav-item"><span class="navbar-text fw-bold me-2">Dr. {{ Auth::user()->apellido_paterno }}</span></li>
                                <li class="nav-item"><a href="{{ route('medico.panel') }}" class="nav-link fw-bold text-hrc-acento">Mi Agenda</a></li>
                            @else
                                <li class="nav-item"><a href="{{ route('reservar_cita') }}" class="btn btn-hrc-acento">Reservar cita</a></li>
                                <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link">Mis Citas</a></li>
                                <li class="nav-item"><span class="navbar-text">Hola, {{ Auth::user()->nombres }}</span></li>
                            @endif
                            
                            <!-- Formulario de cierre de sesión centralizado -->
                            <li class="nav-item">
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Cerrar Sesión</button>
                                </form>
                            </li>
                        @endauth
                        
                        <!-- Interfaz para usuarios no autenticados (Público general) -->
                        @guest
                            <li class="nav-item">
                                <a href="{{ route('reservar_cita') }}" class="btn btn-hrc-acento px-4">Reservar cita</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="nav-link">Iniciar Sesión</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('registro') }}" class="btn btn-outline-hrc-primario px-4">Registrarse</a>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-grow-1 py-5">
        @yield('contenido')
    </main>

    <!-- Pie de Página Institucional -->
    <footer class="footer-hrc mt-auto py-5 oculta-impresion text-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-5">
                    <h5 class="fw-bold mb-3">Hospital Regional del Cusco</h5>
                    <p class="mb-1">Avenida de La Cultura S/N, Cusco 08000</p>
                    <p class="mb-1">Central Telefónica: +51 084 231131</p>
                    <p class="mb-0">Atención de Emergencias: 24 horas</p>
                </div>
                
                <div class="col-md-4">
                    <h5 class="fw-bold mb-3">Enlaces de Interés</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="#" class="text-white-50 text-decoration-none">Términos y Condiciones</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Políticas de Privacidad</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Libro de Reclamaciones</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3">
                    <h5 class="fw-bold mb-3">Canales Oficiales</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="#" class="text-white-50 text-decoration-none">Facebook</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Mesa de Partes Virtual</a></li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 border-light">
            
            <div class="text-center text-white-50 small">
                <p class="mb-0">&copy; {{ date('Y') }} Proyecto académico - Sistema de Gestión Hospitalaria. Uso simulado con fines educativos.</p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    @yield('js')
</body>
</html>