@extends('layouts.main')

@section('titulo', 'Guía de Atención y Contacto')

@section('contenido')
<div class="container">

    <div class="text-center mb-5">
        <h1 class="fw-bold text-hrc-primario mb-2">Guía de Atención y Contacto</h1>
        <p class="text-muted mb-0">
            Requisitos, horarios de consultorios externos, preguntas frecuentes y canales de atención
        </p>
    </div>

    {{-- Modalidades de atención y horarios operativos --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="fw-bold text-hrc-primario mb-2">
                        <i class="bi bi-card-checklist me-2"></i>Pacientes SIS
                    </div>
                    <p class="small text-muted mb-2">
                        Para atenderse mediante el Seguro Integral de Salud en consulta externa debe presentar:
                    </p>
                    <ul class="small text-muted mb-0 ps-3">
                        <li class="mb-1">DNI o Carnet de Extranjería original vigente.</li>
                        <li class="mb-1">Hoja de referencia vigente emitida por su establecimiento de salud de origen.</li>
                        <li>Código de referencia registrado al momento de reservar su cita.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="fw-bold text-hrc-primario mb-2">
                        <i class="bi bi-person-vcard me-2"></i>Pacientes Regulares
                    </div>
                    <p class="small text-muted mb-2">
                        Si acude de manera particular sin referencia del SIS, considere lo siguiente:
                    </p>
                    <ul class="small text-muted mb-0 ps-3">
                        <li class="mb-1">Documento de identidad original (DNI o CE).</li>
                        <li class="mb-1">Ticket de cita impreso o guardado en su celular.</li>
                        <li>Acercarse a Caja/Admisión 20 minutos antes de su turno para la verificación correspondiente.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="fw-bold text-hrc-primario mb-2">
                        <i class="bi bi-clock me-2"></i>Horarios de Atención
                    </div>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2">
                            <strong class="text-dark d-block">Consultorios Externos:</strong>
                            Lunes a viernes, de 8:00 a. m. a 1:00 p. m.
                        </li>
                        <li class="mb-2">
                            <strong class="text-dark d-block">Reserva en línea:</strong>
                            Disponible las 24 horas del día.
                        </li>
                        <li>
                            <strong class="text-dark d-block">Servicio de Emergencias:</strong>
                            Atención ininterrumpida los 7 días de la semana (24 horas).
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Preguntas frecuentes alineadas a las reglas del sistema --}}
    <div class="mb-5">
        <h3 class="h4 fw-bold text-hrc-primario mb-3">Preguntas Frecuentes sobre Citas en Línea</h3>

        <div class="accordion shadow-sm" id="acordeonFaq">
            <div class="accordion-item border-0 border-bottom">
                <h2 class="accordion-header" id="faqHeading1">
                    <button class="accordion-button fw-bold text-hrc-primario" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                        ¿Cuántas citas pendientes puedo tener al mismo tiempo?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#acordeonFaq">
                    <div class="accordion-body small text-muted">
                        Cada paciente puede mantener hasta <strong>3 citas pendientes activas</strong> de forma simultánea y como máximo <strong>1 cita pendiente por especialidad</strong>. Esta medida evita el acaparamiento de cupos y asegura disponibilidad para pacientes de Cusco y provincias.
                    </div>
                </div>
            </div>

            <div class="accordion-item border-0 border-bottom">
                <h2 class="accordion-header" id="faqHeading2">
                    <button class="accordion-button collapsed fw-bold text-hrc-primario" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                        ¿Hasta cuándo puedo cancelar una cita reservada?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#acordeonFaq">
                    <div class="accordion-body small text-muted">
                        Puede cancelar su cita directamente desde la sección <strong>Mis Citas</strong> con un mínimo de <strong>{{ $horasCancelacion }} horas de anticipación</strong>. Si surge un imprevisto dentro de ese plazo, deberá comunicarse con la central de Admisión para liberar el cupo.
                    </div>
                </div>
            </div>

            <div class="accordion-item border-0 border-bottom">
                <h2 class="accordion-header" id="faqHeading3">
                    <button class="accordion-button collapsed fw-bold text-hrc-primario" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                        ¿Qué datos necesito para registrarme por primera vez?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#acordeonFaq">
                    <div class="accordion-body small text-muted">
                        Para validar su identidad y evitar registros duplicados, tenga a la mano su DNI físico: el formulario solicitará su número de documento, dígito verificador y fecha de emisión.
                    </div>
                </div>
            </div>

            <div class="accordion-item border-0">
                <h2 class="accordion-header" id="faqHeading4">
                    <button class="accordion-button collapsed fw-bold text-hrc-primario" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                        ¿Debo imprimir obligatoriamente mi ticket de cita?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#acordeonFaq">
                    <div class="accordion-body small text-muted">
                        No es obligatorio imprimirlo. Puede mostrar el ticket desde su teléfono celular junto con su DNI físico en el consultorio asignado.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Información de contacto y mapa institucional --}}
    <div class="row g-4 align-items-stretch">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <h4 class="h5 fw-bold text-hrc-primario mb-3">Ubicación y Canales de Contacto</h4>

                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class="bi bi-geo-alt-fill text-hrc-primario fs-5"></i>
                        <div class="small">
                            <div class="fw-bold text-dark">Dirección</div>
                            <div class="text-muted">Avenida de La Cultura S/N, Cusco 08000, Perú</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class="bi bi-telephone-fill text-hrc-primario fs-5"></i>
                        <div class="small">
                            <div class="fw-bold text-dark">Central Telefónica</div>
                            <div class="text-muted">+51 084 231131</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class="bi bi-hospital-fill text-hrc-primario fs-5"></i>
                        <div class="small">
                            <div class="fw-bold text-dark">Atención de Emergencias</div>
                            <div class="text-muted">Ingreso por puerta de Emergencias — 24 horas</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <i class="bi bi-envelope-fill text-hrc-primario fs-5"></i>
                        <div class="small">
                            <div class="fw-bold text-dark">Mesa de Informes y Admisión</div>
                            <div class="text-muted">admision@hospitalregional.gob.pe</div>
                        </div>
                    </div>

                    <div class="mt-auto pt-3 border-top">
                        <a href="{{ route('reservar_cita') }}" class="btn btn-hrc-acento w-100">
                            Ir a Reservar Cita
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="ratio ratio-16x9 h-100" style="min-height: 320px;">
                    <iframe src="https://maps.google.com/maps?q=Hospital%20Regional%20del%20Cusco%2C%20Cusco&t=&z=16&ie=UTF8&iwloc=&output=embed"
                            title="Mapa de ubicación del Hospital Regional del Cusco"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            style="border: 0;"></iframe>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection