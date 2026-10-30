@extends('layouts.main')

@section('titulo', 'Inicio')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
@endsection

@section('contenido')
    <main class="index-main">
        <section class="hero-index">            
            <div>
                <h1 class="hero-titulo">Cuidando la salud de toda la región</h1>
                <p>Accede a nuestros servicios médicos y reserva tu cita presencial de manera rápida, segura y sin hacer filas.</p>
                <a href="{{route('reservar_cita')}}" class="btn-primario-lg">Reservar Cita Ahora</a>
            </div>
        </section>

        <section class="especialidades-index">
            <h2 class="seccion-titulo">Nuestras Especialidades</h2>
            <div class="grid-especialidades">
                <div class="tarjeta-especialidad">
                    <img class="imagenes-circulares" src="{{asset('img/medicina-general.png')}}">
                    <h3>Medicina General</h3>
                    <p>Atención integral para el diagnóstico temprano y tratamiento de enfermedades comunes.</p>
                </div>
                <div class="tarjeta-especialidad">
                    <img class="imagenes-circulares" src="{{asset('img/pediatria.png')}}">
                    <h3>Pediatría</h3>
                    <p>Cuidado especializado, control y seguimiento para el desarrollo saludable de los más pequeños.</p>
                </div>
                <div class="tarjeta-especialidad">
                    <img class="imagenes-circulares" src="{{asset('img/cardiologia.png')}}">
                    <h3>Cardiología</h3>
                    <p>Prevención, diagnóstico y tratamiento de enfermedades del corazón y el sistema circulatorio.</p>
                </div>
                <div class="tarjeta-especialidad">
                    <img class="imagenes-circulares" src="{{asset('img/emergencia.png')}}">
                    <h3>Emergencias</h3>
                    <p>Atención prioritaria e inmediata de casos críticos con soporte vital avanzado.</p>
                </div>
            </div>
        </section>

        <section class="info-index">
            <div class="info-bloque">
                <h2>Información al Paciente</h2>
                <p>Para garantizar una atención fluida y oportuna, le recordamos presentarse en el área de admisión 15 minutos antes de su cita programada portando su documento de identidad original.</p>
                <div class="info-contacto-rapido">
                    <div class="contacto-item">
                        <strong>Central Telefónica:</strong>
                        <span>(084) 222222</span>
                    </div>
                    <div class="contacto-item">
                        <strong>Ubicación:</strong>
                        <span>Av. de la Cultura, Cusco</span>
                    </div>
                    <div class="contacto-item">
                        <strong>Horario de Emergencias:</strong>
                        <span>Lunes a Domingo 24hrs</span>
                    </div>
                </div>
            </div>
            <div class="info-imagen">
            </div>
        </section>
    </main>
@endsection

@section('js')

@endsection