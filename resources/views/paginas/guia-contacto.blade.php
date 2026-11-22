@extends('layouts.main')

@section('titulo', 'Guía de Atención y Contacto')
@section('clase_main', 'p-0')

@section('contenido')
    <section class="cabecera-interna-mate seccion-pizarra py-5 position-relative overflow-hidden">
        <div class="container py-2 position-relative">
            <div class="row align-items-center g-4 g-lg-5">
                <div class="col-lg-7">
                    <span class="sobretitulo-mate d-block mb-2 revelar-scroll">Orientación al paciente</span>
                    <h1 class="fw-bold text-hrc-primario mb-3 revelar-scroll">Guía de Atención y Contacto</h1>
                    <p class="text-muted mb-4 subtitulo-acotado revelar-scroll">
                        Revisa los requisitos para pacientes SIS o regulares, consulta las reglas de reserva en línea y ubica nuestros canales oficiales de atención.
                    </p>

                    <div class="d-flex flex-wrap gap-2 revelar-scroll">
                        <a href="#modalidades" class="filtro-pildora-mate">
                            <i class="bi bi-card-checklist me-1"></i> Requisitos y horarios
                        </a>
                        <a href="#preguntas-frecuentes" class="filtro-pildora-mate">
                            <i class="bi bi-question-circle me-1"></i> Preguntas frecuentes
                        </a>
                        <a href="#ubicacion-contacto" class="filtro-pildora-mate">
                            <i class="bi bi-geo-alt me-1"></i> Ubicación y teléfono
                        </a>
                    </div>
                </div>

                <div class="col-lg-5 revelar-scroll" data-delay="100">                    
                    <div class="cabecera-foto-guia shadow-sm">
                        <img src="{{ asset('img/hospital-regional-foto.png') }}"
                             alt="Orientación y admisión de pacientes en el Hospital Regional del Cusco"
                             class="w-100 h-100 img-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="modalidades" class="py-5 bg-white">
        <div class="container py-3">
            <div class="text-center mb-5 revelar-scroll">
                <span class="sobretitulo-mate d-block mb-2">Antes de tu consulta</span>
                <h2 class="fw-bold text-hrc-primario mb-2">Modalidades de Atención y Horarios</h2>
                <p class="text-muted mx-auto subtitulo-acotado mb-0">
                    Verifica la documentación necesaria según tu modalidad de atención para agilizar tu ingreso.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4 revelar-scroll" data-delay="0">
                    <div class="card border-0 h-100 p-4 tarjeta-mate tarjeta-interactiva tarjeta-guia-mate text-start">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="icono-interactivo icono-interactivo-terracota">
                                <i class="bi bi-card-checklist"></i>
                            </div>
                            <span class="badge-categoria-mate">Seguro Integral</span>
                        </div>

                        <h5 class="fw-bold text-hrc-primario mb-2">Pacientes SIS</h5>
                        <p class="small text-muted mb-3">
                            Para atenderse mediante el Seguro Integral de Salud en consulta externa debe presentar:
                        </p>

                        <ul class="list-unstyled lista-requisitos-mate small text-muted mb-0 d-flex flex-column gap-2">
                            <li>
                                <i class="bi bi-check-circle-fill text-hrc-acento"></i>
                                <span>DNI o Carnet de Extranjería original vigente.</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill text-hrc-acento"></i>
                                <span>Hoja de referencia vigente emitida por su establecimiento de salud de origen.</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill text-hrc-acento"></i>
                                <span>Código de referencia registrado al momento de reservar su cita.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-4 revelar-scroll" data-delay="100">
                    <div class="card border-0 h-100 p-4 tarjeta-mate tarjeta-interactiva tarjeta-guia-mate text-start">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="icono-interactivo icono-interactivo-azul">
                                <i class="bi bi-person-vcard-fill"></i>
                            </div>
                            <span class="badge-categoria-mate">Particular</span>
                        </div>

                        <h5 class="fw-bold text-hrc-primario mb-2">Pacientes Regulares</h5>
                        <p class="small text-muted mb-3">
                            Si acude de manera particular sin referencia del SIS, considere lo siguiente:
                        </p>

                        <ul class="list-unstyled lista-requisitos-mate small text-muted mb-0 d-flex flex-column gap-2">
                            <li>
                                <i class="bi bi-check-circle-fill text-hrc-primario"></i>
                                <span>Documento de identidad original (DNI o CE).</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill text-hrc-primario"></i>
                                <span>Ticket de cita impreso o guardado en su teléfono celular.</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill text-hrc-primario"></i>
                                <span>Acercarse a Caja/Admisión 20 minutos antes de su turno para la verificación correspondiente.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-4 revelar-scroll" data-delay="200">
                    <div class="card border-0 h-100 p-4 tarjeta-mate tarjeta-interactiva tarjeta-guia-mate text-start">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="icono-interactivo icono-interactivo-salvia">
                                <i class="bi bi-clock-fill"></i>
                            </div>
                            <span class="etiqueta-medicos-mate">Vigentes</span>
                        </div>

                        <h5 class="fw-bold text-hrc-primario mb-3">Horarios de Atención</h5>

                        <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-3">
                            <li class="item-horario-mate pb-2">
                                <strong class="text-hrc-primario d-block">Consultorios Externos</strong>
                                <span>Lunes a viernes, de 8:00 a. m. a 1:00 p. m.</span>
                            </li>
                            <li class="item-horario-mate pb-2">
                                <strong class="text-hrc-primario d-block">Reserva en línea</strong>
                                <span>Disponible las 24 horas del día.</span>
                            </li>
                            <li>
                                <strong class="text-hrc-primario d-block">Servicio de Emergencias</strong>
                                <span>Atención ininterrumpida los 7 días de la semana (24 horas).</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="preguntas-frecuentes" class="seccion-piedra py-5 position-relative overflow-hidden">
        <div class="container py-4 position-relative">
            <div class="adorno-puntos adorno-esquina-der" aria-hidden="true"></div>
            <div class="adorno-anillo adorno-pasos-izq" aria-hidden="true"></div>

            <div class="text-center mb-5 revelar-scroll">
                <span class="sobretitulo-mate d-block mb-2">Dudas comunes</span>
                <h2 class="fw-bold text-hrc-primario mb-2">Preguntas Frecuentes sobre Citas en Línea</h2>
                <p class="text-muted mx-auto subtitulo-acotado mb-0">
                    Reglas vigentes del sistema diseñadas para garantizar una distribución justa de turnos.
                </p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-9 revelar-scroll">
                    <div class="accordion acordeon-mate d-flex flex-column gap-3" id="acordeonFaq">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                                    ¿Cuántas citas pendientes puedo tener al mismo tiempo?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#acordeonFaq">
                                <div class="accordion-body">
                                    Cada paciente puede mantener hasta <strong>3 citas pendientes activas</strong> de forma simultánea y como máximo <strong>1 cita pendiente por especialidad</strong>. Esta medida evita el acaparamiento de cupos y asegura disponibilidad para pacientes de Cusco y provincias.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                                    ¿Hasta cuándo puedo cancelar una cita reservada?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#acordeonFaq">
                                <div class="accordion-body">
                                    Puede cancelar su cita directamente desde la sección <strong>Mis Citas</strong> con un mínimo de <strong>{{ $horasCancelacion }} horas de anticipación</strong>. Si surge un imprevisto dentro de ese plazo, deberá comunicarse con la central de Admisión para liberar el cupo.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                                    ¿Qué datos necesito para registrarme por primera vez?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#acordeonFaq">
                                <div class="accordion-body">
                                    Para validar su identidad y evitar registros duplicados, tenga a la mano su DNI físico: el formulario solicitará su número de documento, dígito verificador y fecha de emisión.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                                    ¿Debo imprimir obligatoriamente mi ticket de cita?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#acordeonFaq">
                                <div class="accordion-body">
                                    No es obligatorio imprimirlo. Puede mostrar el ticket desde su teléfono celular junto con su DNI físico en el consultorio asignado.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="ubicacion-contacto" class="py-5 bg-white">
        <div class="container py-4">
            <div class="bloque-dividido-mate overflow-hidden revelar-scroll">
                <div class="row g-0 align-items-stretch">
                    <div class="col-lg-5 panel-dividido-azul text-white p-4 p-lg-5 d-flex flex-column">
                        <span class="etiqueta-panel-oscuro mb-2">Canales oficiales</span>
                        <h2 class="h3 fw-bold mb-3">Ubicación y Contacto</h2>
                        <p class="texto-claro-mate small mb-4">
                            Si necesitas orientación adicional sobre referencias o citas presenciales, comunícate con nuestras áreas de atención.
                        </p>

                        <ul class="list-unstyled d-flex flex-column gap-3 small mb-4">
                            <li class="d-flex align-items-start gap-3">
                                <i class="bi bi-geo-alt-fill icono-panel-oscuro mt-1"></i>
                                <div>
                                    <div class="texto-claro-mate">Dirección</div>
                                    <div class="fw-bold">Avenida de La Cultura S/N, Cusco 08000, Perú</div>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3">
                                <i class="bi bi-telephone-fill icono-panel-oscuro mt-1"></i>
                                <div>
                                    <div class="texto-claro-mate">Central Telefónica</div>
                                    <div class="fw-bold">+51 084 231131</div>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3">
                                <i class="bi bi-hospital-fill icono-panel-oscuro mt-1"></i>
                                <div>
                                    <div class="texto-claro-mate">Atención de Emergencias</div>
                                    <div class="fw-bold">Ingreso por puerta de Emergencias &middot; 24 horas</div>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3">
                                <i class="bi bi-envelope-fill icono-panel-oscuro mt-1"></i>
                                <div>
                                    <div class="texto-claro-mate">Mesa de Informes y Admisión</div>
                                    <div class="fw-bold">admision@hospitalregional.gob.pe</div>
                                </div>
                            </li>
                        </ul>

                        <div class="mt-auto pt-3 border-top border-contacto-claro">
                            <a href="{{ route('reservar_cita') }}" class="btn btn-hrc-acento rounded-pill w-100">
                                Ir a Reservar Cita
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-7 bg-white">
                        <div class="mapa-contacto-contenedor h-100">
                            <iframe src="https://maps.google.com/maps?q=Hospital%20Regional%20del%20Cusco%2C%20Cusco&t=&z=16&ie=UTF8&iwloc=&output=embed"
                                    title="Mapa de ubicación del Hospital Regional del Cusco"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    class="w-100 h-100 border-0 d-block"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('js/inicio.js') }}"></script>
@endsection