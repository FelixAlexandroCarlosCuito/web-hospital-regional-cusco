/**
 * Controla el asistente de reserva en 3 pasos con descripción dinámica de especialidad
 * y consulta asíncrona de horarios disponibles sin selección manual de médico.
 */
document.addEventListener('DOMContentLoaded', function () {
    const contenedorDatos = document.getElementById('datos-reserva-cita');
    if (!contenedorDatos) return;

    const datos = JSON.parse(contenedorDatos.textContent);
    const catalogoEspecialidades = datos.especialidades || [];
    const paciente = datos.paciente || {};
    const urlHorarios = datos.urlHorarios;

    const pasos = [
        document.getElementById('paso-1'),
        document.getElementById('paso-2'),
        document.getElementById('paso-3'),
    ];
    const itemsBarra = document.querySelectorAll('.barra-pasos li');

    const selectEspecialidad = document.getElementById('especialidad');
    const fichaInicial = document.getElementById('ficha-estado-inicial');
    const fichaDetalle = document.getElementById('ficha-estado-detalle');
    const fichaNombre = document.getElementById('ficha-esp-nombre');
    const fichaDescripcion = document.getElementById('ficha-esp-descripcion');

    const inputFecha = document.getElementById('fecha_cita');
    const inputHora = document.getElementById('hora_cita');
    const contenedorHorarios = document.getElementById('contenedor-horarios');
    const inputTelefono = document.getElementById('telefono');
    const inputSis = document.getElementById('referencia_sis');

    /**
     * Cambia la vista activa del stepper y sincroniza el indicador visual superior.
     */
    function irAPaso(numeroPaso) {
        pasos.forEach(function (bloque, indice) {
            if (bloque) {
                bloque.style.display = (indice + 1 === numeroPaso) ? 'block' : 'none';
            }
        });

        itemsBarra.forEach(function (item, indice) {
            item.classList.toggle('activo', indice + 1 === numeroPaso);
            item.classList.toggle('completado', indice + 1 < numeroPaso);
        });
    }

    function mostrarError(idElemento, mensaje) {
        const el = document.getElementById(idElemento);
        if (el) el.textContent = mensaje || '';
    }

    /**
     * Formatea una hora en formato 24h (HH:MM) a lectura de 12h para el paciente.
     */
    function formatearHora12(hora24) {
        const partes = hora24.split(':');
        let h = parseInt(partes[0], 10);
        const m = partes[1];
        const periodo = h >= 12 ? 'p. m.' : 'a. m.';
        if (h === 0) h = 12;
        else if (h > 12) h -= 12;
        return `${String(h).padStart(2, '0')}:${m} ${periodo}`;
    }

    /**
     * Formatea YYYY-MM-DD a DD/MM/YYYY sin desfases de zona horaria.
     */
    function formatearFechaLocal(fechaIso) {
        const partes = (fechaIso || '').split('-');
        if (partes.length !== 3) return fechaIso;
        return `${partes[2]}/${partes[1]}/${partes[0]}`;
    }

    /**
     * Determina si una cadena YYYY-MM-DD cae en sábado o domingo usando componentes locales.
     */
    function esFinDeSemana(fechaIso) {
        const partes = (fechaIso || '').split('-').map(Number);
        if (partes.length !== 3) return false;
        const fecha = new Date(partes[0], partes[1] - 1, partes[2]);
        const dia = fecha.getDay();
        return dia === 0 || dia === 6;
    }

    /**
     * Muestra en el panel lateral la descripción de la especialidad seleccionada.
     */
    function actualizarFichaEspecialidad() {
        const idSeleccionado = parseInt(selectEspecialidad.value, 10);
        const esp = catalogoEspecialidades.find((item) => item.id === idSeleccionado);

        if (!esp) {
            fichaInicial.classList.remove('d-none');
            fichaDetalle.classList.add('d-none');
            return;
        }

        fichaNombre.textContent = esp.nombre;
        fichaDescripcion.textContent = esp.descripcion;
        fichaInicial.classList.add('d-none');
        fichaDetalle.classList.remove('d-none');
    }

    /**
     * Renderiza un mensaje informativo dentro de la caja de horarios y limpia la selección previa.
     */
    function mostrarMensajeHorarios(texto, esAlerta) {
        inputHora.value = '';
        contenedorHorarios.innerHTML = '';

        const aviso = document.createElement('div');
        aviso.className = esAlerta ? 'small text-danger fw-semibold' : 'small text-muted';
        aviso.textContent = texto;
        contenedorHorarios.appendChild(aviso);
    }

    /**
     * Consulta al backend los bloques con al menos un especialista libre para la especialidad y fecha.
     */
    async function cargarHorariosDisponibles() {
        const especialidadId = selectEspecialidad.value;
        const fecha = inputFecha.value;

        mostrarError('error-fecha_cita', '');
        mostrarError('error-hora_cita', '');

        if (!especialidadId || !fecha) {
            mostrarMensajeHorarios('Selecciona primero una fecha hábil para ver los cupos disponibles.', false);
            return;
        }

        if (fecha < datos.hoy) {
            mostrarError('error-fecha_cita', 'No puede seleccionar una fecha pasada.');
            mostrarMensajeHorarios('Seleccione una fecha igual o posterior a hoy.', true);
            return;
        }

        if (esFinDeSemana(fecha)) {
            mostrarError('error-fecha_cita', 'Los consultorios externos atienden de lunes a viernes.');
            mostrarMensajeHorarios('Seleccione un día hábil (lunes a viernes) para ver horarios.', true);
            return;
        }

        const horaPrevia = inputHora.value;
        mostrarMensajeHorarios('Consultando disponibilidad de turnos...', false);

        try {
            const params = new URLSearchParams({ especialidad_id: especialidadId, fecha: fecha });
            const respuesta = await fetch(`${urlHorarios}?${params.toString()}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!respuesta.ok) {
                throw new Error('No se pudo consultar la disponibilidad.');
            }

            const payload = await respuesta.json();
            const horarios = payload.horarios || [];

            if (!horarios.length) {
                mostrarMensajeHorarios(payload.mensaje || 'No hay horarios disponibles para esta fecha.', true);
                return;
            }

            contenedorHorarios.innerHTML = '';
            const grilla = document.createElement('div');
            grilla.className = 'grilla-horarios-mate';

            horarios.forEach(function (hora24) {
                const boton = document.createElement('button');
                boton.type = 'button';
                boton.className = 'btn-horario-mate';
                boton.dataset.hora = hora24;
                boton.textContent = formatearHora12(hora24);

                if (horaPrevia === hora24) {
                    boton.classList.add('seleccionado');
                    inputHora.value = hora24;
                }

                boton.addEventListener('click', function () {
                    grilla.querySelectorAll('.btn-horario-mate').forEach((b) => b.classList.remove('seleccionado'));
                    this.classList.add('seleccionado');
                    inputHora.value = hora24;
                    mostrarError('error-hora_cita', '');
                });

                grilla.appendChild(boton);
            });

            contenedorHorarios.appendChild(grilla);
        } catch (err) {
            mostrarMensajeHorarios('Ocurrió un error al consultar los horarios. Intente nuevamente.', true);
        }
    }

    selectEspecialidad.addEventListener('change', function () {
        mostrarError('error-especialidad', '');
        actualizarFichaEspecialidad();

        // Al cambiar de especialidad se invalida el horario elegido previamente
        inputHora.value = '';
        if (inputFecha.value) {
            cargarHorariosDisponibles();
        }
    });

    inputFecha.addEventListener('change', cargarHorariosDisponibles);

    document.querySelector('.btn-siguiente-1')?.addEventListener('click', function () {
        if (!selectEspecialidad.value) {
            mostrarError('error-especialidad', 'Debe seleccionar una especialidad para continuar.');
            selectEspecialidad.focus();
            return;
        }

        mostrarError('error-especialidad', '');
        irAPaso(2);

        if (inputFecha.value && !inputHora.value) {
            cargarHorariosDisponibles();
        }
    });

    document.querySelector('.btn-atras-2')?.addEventListener('click', function () {
        irAPaso(1);
    });

    document.querySelector('.btn-siguiente-2')?.addEventListener('click', function () {
        let valido = true;

        if (!inputFecha.value) {
            mostrarError('error-fecha_cita', 'Seleccione una fecha para su cita.');
            valido = false;
        } else if (inputFecha.value < datos.hoy || esFinDeSemana(inputFecha.value)) {
            mostrarError('error-fecha_cita', 'Seleccione una fecha hábil (lunes a viernes) desde hoy.');
            valido = false;
        } else {
            mostrarError('error-fecha_cita', '');
        }

        if (!inputHora.value) {
            mostrarError('error-hora_cita', 'Seleccione uno de los horarios disponibles.');
            valido = false;
        } else {
            mostrarError('error-hora_cita', '');
        }

        const tel = inputTelefono.value.trim();
        if (!/^9\d{8}$/.test(tel)) {
            mostrarError('error-telefono', 'Ingrese un celular válido de 9 dígitos que empiece con 9.');
            valido = false;
        } else {
            mostrarError('error-telefono', '');
        }

        if (!valido) return;

        const opcionEsp = selectEspecialidad.options[selectEspecialidad.selectedIndex];
        const espObj = catalogoEspecialidades.find((e) => e.id === parseInt(selectEspecialidad.value, 10));

        document.getElementById('resumen-paciente').textContent = `${paciente.nombres} ${paciente.apellidos}`;
        document.getElementById('resumen-documento').textContent = paciente.documento;
        document.getElementById('resumen-especialidad').textContent = espObj ? espObj.nombre : opcionEsp.textContent.trim();
        document.getElementById('resumen-fecha').textContent = formatearFechaLocal(inputFecha.value);
        document.getElementById('resumen-hora').textContent = formatearHora12(inputHora.value);
        document.getElementById('resumen-telefono').textContent = tel;
        document.getElementById('resumen-sis').textContent = inputSis.value.trim() || 'No aplica (Paciente regular)';

        irAPaso(3);
    });

    document.querySelector('.btn-atras-3')?.addEventListener('click', function () {
        irAPaso(2);
    });

    // Sincroniza estado inicial si la página viene preseleccionada o con old() tras un error de validación
    function actualizarFichaEspecialidad() {
        const idSeleccionado = parseInt(selectEspecialidad.value, 10);
        const esp = catalogoEspecialidades.find((item) => item.id === idSeleccionado);
        const etiquetaPaso2 = document.getElementById('paso2-especialidad-nombre');

        if (!esp) {
            fichaInicial.classList.remove('d-none');
            fichaDetalle.classList.add('d-none');
            if (etiquetaPaso2) etiquetaPaso2.textContent = '—';
            return;
        }

        fichaNombre.textContent = esp.nombre;
        fichaDescripcion.textContent = esp.descripcion;
        if (etiquetaPaso2) etiquetaPaso2.textContent = esp.nombre;
        fichaInicial.classList.add('d-none');
        fichaDetalle.classList.remove('d-none');
    }
});