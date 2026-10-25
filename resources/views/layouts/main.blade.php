 <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo') - HRC</title>
    <link rel="stylesheet" href="{{ asset('css/general.css') }}">
    @yield('css')
</head>
<body>
    <header class="encabezado-principal oculta-impresion">
        <div class="header-contenido">
            <img class="logo-container" src="{{ asset('assets/logo-hospital.png') }}" alt="Logo">
            <h1 class="titulo-sitio">Hospital Regional del Cusco</h1>
            <nav class="navegacion-superior">
                <a href="{{ url('/inicio') }}">Inicio</a>
                <a href="{{ url('/reservar-cita') }}">Reservar cita</a>
                <a href="{{ url('/inicio-sesion') }}" class="btn-login">Iniciar Sesión</a>
            </nav>
        </div>
    </header>

    @yield('contenido')

    <footer class="pie-pagina oculta-impresion">
        <div class="footer-contenido">
            <div class="footer-info">
                <h3>Hospital Regional del Cusco</h3>
                <p>Avenida de La Cultura S/N, Cusco 08000</p>
                <p>Central Telefónica: +51 84 231131</p>
                <p>Emergencias: 24 horas, Lunes a Domingo</p>
            </div>
            <div class="footer-enlaces">
                <a href="#">Términos y Condiciones</a>
                <a href="#">Políticas de Privacidad</a>
                <a href="#">Libro de Reclamaciones</a>
            </div>
            <div class="redes-sociales">
                <a href="#">Facebook</a>
                <a href="#">Instagram</a>
                <a href="#">WhatsApp</a>
            </div>
        </div>
        <div class="footer-copyright">
            <p>&copy; Proyecto académico - Uso simulado con fines educativos. No es una página oficial</p>
        </div>
    </footer>

    @yield('js')
</body>
</html>