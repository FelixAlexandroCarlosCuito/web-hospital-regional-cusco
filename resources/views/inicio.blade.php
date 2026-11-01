@extends('layouts.main')

@section('titulo', 'Inicio')

@section('contenido')
    <!-- Sección Hero: Muestra la propuesta de valor principal con fondo oscuro para alto contraste -->
    <section class="hero-hrc d-flex align-items-center text-white text-center text-md-start">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 py-5">
                    <h1 class="display-4 fw-bold mb-4">Cuidando la salud de toda la región</h1>
                    <p class="lead mb-4 opacity-75">Accede a nuestros servicios médicos y reserva tu cita presencial de manera rápida, segura y sin hacer filas.</p>
                    <a href="{{ route('reservar_cita') }}" class="btn btn-hrc-acento btn-lg px-5 rounded-pill">Reservar Cita Ahora</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Especialidades: Renderizado mediante el sistema Grid de Bootstrap (Tarjetas) -->
    <section class="container py-5 my-4">
        <h2 class="text-center text-hrc-primario fw-bold mb-5">Nuestras Especialidades</h2>
        
        <!-- Grid: 1 columna en móvil, 2 en tablets, 4 en pantallas grandes -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            
            <div class="col">
                <div class="card h-100 border-0 shadow-sm text-center p-4 card-especialidad">
                    <img src="{{ asset('img/medicina-general.png') }}" alt="Medicina General" class="rounded-circle mx-auto mb-3" width="80" height="80" style="object-fit: cover;">
                    <div class="card-body p-0">
                        <h5 class="card-title fw-bold text-hrc-primario">Medicina General</h5>
                        <p class="card-text text-muted small">Atención integral para el diagnóstico temprano y tratamiento de enfermedades comunes.</p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 border-0 shadow-sm text-center p-4 card-especialidad">
                    <img src="{{ asset('img/pediatria.png') }}" alt="Pediatría" class="rounded-circle mx-auto mb-3" width="80" height="80" style="object-fit: cover;">
                    <div class="card-body p-0">
                        <h5 class="card-title fw-bold text-hrc-primario">Pediatría</h5>
                        <p class="card-text text-muted small">Cuidado especializado, control y seguimiento para el desarrollo saludable de los más pequeños.</p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 border-0 shadow-sm text-center p-4 card-especialidad">
                    <img src="{{ asset('img/cardiologia.png') }}" alt="Cardiología" class="rounded-circle mx-auto mb-3" width="80" height="80" style="object-fit: cover;">
                    <div class="card-body p-0">
                        <h5 class="card-title fw-bold text-hrc-primario">Cardiología</h5>
                        <p class="card-text text-muted small">Prevención, diagnóstico y tratamiento de enfermedades del corazón y el sistema circulatorio.</p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 border-0 shadow-sm text-center p-4 card-especialidad">
                    <img src="{{ asset('img/emergencia.png') }}" alt="Emergencias" class="rounded-circle mx-auto mb-3" width="80" height="80" style="object-fit: cover;">
                    <div class="card-body p-0">
                        <h5 class="card-title fw-bold text-hrc-primario">Emergencias</h5>
                        <p class="card-text text-muted small">Atención prioritaria e inmediata de casos críticos con soporte vital avanzado.</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Sección Información: Contenedor con fondo gris para separarlo visualmente del bloque anterior -->
    <section class="bg-light py-5">
        <div class="container py-4">
            <div class="row align-items-center g-5">
                
                <!-- Columna de texto e información de contacto -->
                <div class="col-lg-6">
                    <h2 class="text-hrc-primario fw-bold mb-4">Información al Paciente</h2>
                    <p class="text-muted mb-4">Para garantizar una atención fluida y oportuna, le recordamos presentarse en el área de admisión 15 minutos antes de su cita programada portando su documento de identidad original.</p>
                    
                    <ul class="list-group list-group-flush border-top border-bottom">
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-3">
                            <strong class="text-hrc-primario">Central Telefónica:</strong>
                            <span class="text-muted">(084) 231131</span>
                        </li>
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-3">
                            <strong class="text-hrc-primario">Ubicación:</strong>
                            <span class="text-muted">Av. de La Cultura S/N, Cusco</span>
                        </li>
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-3">
                            <strong class="text-hrc-primario">Horario de Emergencias:</strong>
                            <span class="text-muted">Lunes a Domingo 24hrs</span>
                        </li>
                    </ul>
                </div>

                <!-- Columna de imagen representativa -->
                <div class="col-lg-6">
                    <!-- Se utiliza la etiqueta img en lugar de background CSS para mejor adaptabilidad responsiva -->
                    <img src="{{ asset('img/hospital-regional-foto.png') }}" alt="Fachada Hospital Regional" class="img-fluid rounded-3 shadow">
                </div>
                
            </div>
        </div>
    </section>
@endsection