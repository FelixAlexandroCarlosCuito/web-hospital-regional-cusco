document.addEventListener('DOMContentLoaded', function() {
    const pasos = [
        document.getElementById('paso-1'),
        document.getElementById('paso-2'),
        document.getElementById('paso-3')
    ];
    const barraPasos = document.querySelectorAll('.barra-pasos li');
    let pasoActual = 0;

    // Inicialización
    pasos.forEach((paso, index) => { if (index !== 0) paso.style.display = 'none'; });
    configurarFechaMinima();

    function mostrarPaso(nuevoPaso) {
        pasos[pasoActual].style.display = 'none';
        if(barraPasos[pasoActual]) barraPasos[pasoActual].classList.remove('activo');
        
        pasoActual = nuevoPaso;
        
        pasos[pasoActual].style.display = 'block';
        if(barraPasos[pasoActual]) barraPasos[pasoActual].classList.add('activo');

        if (pasoActual === 2) generarResumen();
    }

    function mostrarError(idElemento, mensaje) {
        document.getElementById(`error-${idElemento}`).textContent = mensaje;
        const input = document.getElementById(idElemento);
        if(input) input.classList.add('input-error');
    }

    function limpiarErrores(idPaso) {
        const paso = document.getElementById(idPaso);
        paso.querySelectorAll('.mensaje-error').forEach(msg => msg.textContent = '');
        paso.querySelectorAll('.input-error').forEach(input => input.classList.remove('input-error'));
    }

    function configurarFechaMinima() {
        const inputFecha = document.getElementById('fecha_cita');
        const hoy = new Date();
        const anio = hoy.getFullYear();
        const mes = String(hoy.getMonth() + 1).padStart(2, '0');
        const dia = String(hoy.getDate()).padStart(2, '0');
        inputFecha.min = `${anio}-${mes}-${dia}`;
    }

    // --- LÓGICA DEL PASO 1 (Especialidad y Médico) ---
    document.getElementById('especialidad').addEventListener('change', function() {
        const especialidadId = this.value;
        const selectMedico = document.getElementById('medico');
        
        selectMedico.innerHTML = '<option value="" disabled selected>Seleccione un médico</option>';
        selectMedico.disabled = false;

        const especialidad = window.CatalogoHospital.find(esp => esp.id == especialidadId);
        
        if (especialidad && especialidad.medicos.length > 0) {
            especialidad.medicos.forEach(medico => {
                const option = document.createElement('option');
                option.value = medico.id;
                option.textContent = `Dr. ${medico.nombres} ${medico.apellidos} (${medico.consultorio})`;
                selectMedico.appendChild(option);
            });
        } else {
            selectMedico.innerHTML = '<option value="" disabled selected>No hay médicos disponibles</option>';
        }
    });

    document.querySelector('.btn-siguiente-1').addEventListener('click', function() {
        limpiarErrores('paso-1');
        const especialidad = document.getElementById('especialidad').value;
        const medico = document.getElementById('medico').value;
        let esValido = true;

        if (!especialidad) { mostrarError('especialidad', 'Seleccione una especialidad.'); esValido = false; }
        if (!medico) { mostrarError('medico', 'Seleccione un médico tratante.'); esValido = false; }
        
        if (esValido) mostrarPaso(1);
    });

    // --- LÓGICA DEL PASO 2 (Fecha, Hora y Datos) ---
    document.getElementById('fecha_cita').addEventListener('change', async function() {
        limpiarErrores('paso-2');
        const fechaSeleccionada = this.value;
        const selectHora = document.getElementById('hora_cita');
        const medicoId = document.getElementById('medico').value;
        
        // 1. Validar fin de semana
        const dateObj = new Date(fechaSeleccionada + 'T00:00:00');
        if (dateObj.getDay() === 0 || dateObj.getDay() === 6) {
            mostrarError('fecha_cita', 'Atención solo de Lunes a Viernes.');
            this.value = '';
            selectHora.innerHTML = '<option value="" disabled selected>Seleccione una fecha válida</option>';
            selectHora.disabled = true;
            return;
        }

        if (!medicoId) {
            alert('Error crítico: No se ha seleccionado un médico.');
            return;
        }

        // 2. Estado de carga visual
        selectHora.innerHTML = '<option value="" disabled selected>Buscando cupos...</option>';
        selectHora.disabled = true;

        // 3. Consulta en vivo al backend para evitar colisiones
        try {
            const respuesta = await fetch(`/citas/horarios?medico_id=${medicoId}&fecha=${fechaSeleccionada}`);
            const horariosDisponibles = await respuesta.json();

            selectHora.innerHTML = '<option value="" disabled selected>Seleccione su turno</option>';
            
            if (horariosDisponibles.length > 0) {
                horariosDisponibles.forEach(hora => {
                    // Formateamos para que el paciente vea '08:00 AM' en lugar de '08:00:00'
                    const [h, m] = hora.split(':');
                    const formato12h = new Date(2000, 0, 1, h, m).toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'});
                    
                    const option = document.createElement('option');
                    option.value = hora; // El value sigue siendo H:i:s para la base de datos
                    option.textContent = formato12h;
                    selectHora.appendChild(option);
                });
                selectHora.disabled = false;
            } else {
                selectHora.innerHTML = '<option value="" disabled selected>Sin cupos para este día</option>';
            }
        } catch (error) {
            console.error("Error al cargar horarios:", error);
            selectHora.innerHTML = '<option value="" disabled selected>Error de conexión</option>';
        }
    });

    document.querySelector('.btn-siguiente-2').addEventListener('click', function() {
        limpiarErrores('paso-2');
        const fecha = document.getElementById('fecha_cita').value;
        const hora = document.getElementById('hora_cita').value;
        const telefono = document.getElementById('telefono').value.trim();
        let esValido = true;

        if (!fecha) { mostrarError('fecha_cita', 'Seleccione una fecha.'); esValido = false; }
        if (!hora) { mostrarError('hora_cita', 'Seleccione un horario.'); esValido = false; }
        if (!telefono) { mostrarError('telefono', 'El teléfono es obligatorio.'); esValido = false; }
        
        if (esValido) mostrarPaso(2);
    });

    // --- NAVEGACIÓN HACIA ATRÁS ---
    document.querySelector('.btn-atras-2').addEventListener('click', () => mostrarPaso(0));
    document.querySelector('.btn-atras-3').addEventListener('click', () => mostrarPaso(1));

    // --- LÓGICA DEL PASO 3 (Resumen) ---
    function generarResumen() {
        const selEsp = document.getElementById('especialidad');
        const selMed = document.getElementById('medico');
        
        const fechaRaw = document.getElementById('fecha_cita').value.split('-');
        const fechaFormat = `${fechaRaw[2]}/${fechaRaw[1]}/${fechaRaw[0]}`;
        
        const telefono = document.getElementById('telefono').value;
        const sis = document.getElementById('referencia_sis').value.trim();

        document.getElementById('resumen-paciente').textContent = window.PacienteActual.nombres + ' ' + window.PacienteActual.apellidos;
        document.getElementById('resumen-documento').textContent = window.PacienteActual.documento;
        document.getElementById('resumen-especialidad').textContent = selEsp.options[selEsp.selectedIndex].text;
        document.getElementById('resumen-medico').textContent = selMed.options[selMed.selectedIndex].text;
        document.getElementById('resumen-fecha').textContent = fechaFormat;
        document.getElementById('resumen-hora').textContent = document.getElementById('hora_cita').value;
        document.getElementById('resumen-telefono').textContent = telefono;
        document.getElementById('resumen-sis').textContent = sis ? sis : 'Ninguna (Paciente Regular)';
    }

    // --- ENVÍO FINAL A LARAVEL ---
    document.getElementById('formulario-reserva').addEventListener('submit', function(e) {
        const btnSubmit = this.querySelector('button[type="submit"]');
        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Procesando Reserva...';
        // El formulario viaja por POST a CitaController@store
    });
});