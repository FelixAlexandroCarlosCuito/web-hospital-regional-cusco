{{-- Plantilla autónoma sin dependencias de sesión ni base de datos para garantizar renderizado seguro ante fallos críticos --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del Sistema - HRC</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo-hospital.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="header-hrc py-3">
        <div class="container">
            <a class="d-inline-flex align-items-center text-decoration-none" href="/">
                <img src="{{ asset('img/logo-hospital.png') }}" alt="Logo Hospital Regional del Cusco" width="40" height="40" class="me-2">
                <div class="lh-1">
                    <span class="fw-bold fs-5 d-block text-hrc-primario">HRC</span>
                    <span class="text-muted marca-subtitulo">Hospital Regional del Cusco</span>
                </div>
            </a>
        </div>
    </header>

    <main class="flex-grow-1 d-flex align-items-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-9 col-lg-6">
                    <div class="panel-card-elevada p-4 p-md-5 text-center">
                        <div class="kpi-icono-cuadro kpi-icono-terracota mx-auto mb-3 error-icono-grande">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>

                        <span class="sobretitulo-mate d-block mb-1">Incidencia técnica &middot; Error 500</span>

                        <h1 class="h4 fw-bold text-hrc-primario mb-2">
                            Ocurrió un inconveniente al procesar su solicitud
                        </h1>

                        <p class="small text-muted mb-4">
                            El servicio presentó un error inesperado y el incidente fue registrado para su revisión técnica. Intente nuevamente en unos minutos o comuníquese con la central del hospital al (084) 231131.
                        </p>

                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <a href="/" class="btn btn-hrc-acento rounded-pill px-4">
                                Volver al Inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>