document.addEventListener('DOMContentLoaded', function() {
    const pasos = [
        document.getElementById('paso-1'),
        document.getElementById('paso-2'),
        document.getElementById('paso-3'),
        document.getElementById('paso-4')
    ];
    const barraPasos = document.querySelectorAll('.barra-pasos li');
    let pasoActual = 0;

    const bdMedicos = {
        "1": [{id: "101", nombre: "Dr. Carlos Mendoza"}, {id: "102", nombre: "Dra. Ana Silva"}],
        "2": [{id: "201", nombre: "Dr. Luis Paredes"}],
        "3": [{id: "301", nombre: "Dra. Carmen Rosa"}],
        "4": [{id: "401", nombre: "Dr. Jorge Torres"}, {id: "402", nombre: "Dr. Marcos Ruiz"}],
        "5": [{id: "501", nombre: "Dra. María Elena"}]
    };

    configurarFechaMinima();

    pasos.forEach((paso, index) => {
        if (index !== 0) paso.style.display = 'none';
    });

    function mostrarPaso(nuevoPaso) {
        pasos[pasoActual].style.display = 'none';
        barraPasos[pasoActual].classList.remove('activo');
        
        pasoActual = nuevoPaso;
        
        pasos[pasoActual].style.display = 'block';
        barraPasos[pasoActual].classList.add('activo');

        if (pasoActual === 3) {
            generarResumen();
        }
    }

    function mostrarError(idElemento, mensaje) {
        document.getElementById(`error-${idElemento}`).textContent = mensaje;
        if(document.getElementById(idElemento)) {
            document.getElementById(idElemento).classList.add('input-error');
        }
    }

    function limpiarErrores(idPaso) {
        const paso = document.getElementById(idPaso);
        const mensajes = paso.querySelectorAll('.mensaje-error');
        const inputs = paso.querySelectorAll('.input-error');
        
        mensajes.forEach(msg => msg.textContent = '');
        inputs.forEach(input => input.classList.remove('input-error'));
    }

    function configurarFechaMinima() {
        const inputFecha = document.getElementById('fecha_cita');
        const hoy = new Date();
        const anio = hoy.getFullYear();
        const mes = String(hoy.getMonth() + 1).padStart(2, '0');
        const dia = String(hoy.getDate()).padStart(2, '0');
        inputFecha.min = `${anio}-${mes}-${dia}`;
    }

       document.querySelector('.btn-validar').addEventListener('click', async function() {
        limpiarErrores('paso-1');
        const tipoDoc = document.getElementById('tipo_documento').value;
        const numDoc = document.getElementById('numero_documento').value.trim();
        let esValido = true;

        // Validaciones básicas
        if (!tipoDoc) {
            mostrarError('tipo_documento', 'Seleccione un tipo de documento.');
            esValido = false;
        }

        if (!numDoc) {
            mostrarError('numero_documento', 'Ingrese el número de documento.');
            esValido = false;
        } else if (tipoDoc === 'DNI' && !/^\d{8}$/.test(numDoc)) {
            mostrarError('numero_documento', 'El DNI debe tener exactamente 8 dígitos.');
            esValido = false;
        } else if (tipoDoc === 'CE' && !/^[A-Za-z0-9]{9}$/.test(numDoc)) {
            mostrarError('numero_documento', 'El CE debe tener exactamente 9 caracteres.');
            esValido = false;
        }

        if (esValido) {
            // Consultamos a Laravel
            try {
                const respuesta = await fetch(`http://127.0.0.1:8000/api/paciente/${numDoc}`);
                const data = await respuesta.json();

                const inputNombres = document.getElementById('nombres');
                const inputApellidos = document.getElementById('apellidos');

                if (data.encontrado) {
                    // SI EXISTE: Autocompletar y bloquear
                    // (Como guardamos el nombre completo, lo separamos o lo ponemos en el primer campo)
                    const partesNombre = data.nombre_completo.split(' ');
                    inputNombres.value = partesNombre[0] || data.nombre_completo;
                    inputApellidos.value = partesNombre.slice(1).join(' ') || '';
                    
                    inputNombres.readOnly = true;
                    inputApellidos.readOnly = true;
                    
                    alert("Paciente encontrado. Datos autocompletados.");
                } else {
                    // NO EXISTE: Limpiar y permitir escribir
                    inputNombres.value = "";
                    inputApellidos.value = "";
                    
                    inputNombres.readOnly = false;
                    inputApellidos.readOnly = false;
                    
                    inputNombres.focus(); // Ponemos el cursor en Nombres
                    alert("Paciente nuevo. Por favor, ingrese sus nombres y apellidos.");
                }
            } catch (error) {
                console.error("Error al conectar con Laravel:", error);
                mostrarError('validacion-general', 'Error de conexión. Asegúrese de que el servidor esté encendido.');
            }
        }
    });
    document.querySelector('.btn-siguiente-1').addEventListener('click', function() {
        limpiarErrores('paso-1');
        const nombres = document.getElementById('nombres').value.trim();

        if (!nombres) {
            mostrarError('validacion-general', 'Debe validar un documento correctamente para continuar.');
            return;
        }
        mostrarPaso(1);
    });

    document.getElementById('especialidad').addEventListener('change', function() {
        const especialidadId = this.value;
        const selectMedico = document.getElementById('medico');
        
        selectMedico.innerHTML = '<option value="" selected>Cualquier médico disponible</option>';
        
        if (especialidadId && bdMedicos[especialidadId]) {
            selectMedico.disabled = false;
            bdMedicos[especialidadId].forEach(medico => {
                const option = document.createElement('option');
                option.value = medico.id;
                option.textContent = medico.nombre;
                selectMedico.appendChild(option);
            });
        } else {
            selectMedico.disabled = true;
        }
    });

    document.querySelector('.btn-siguiente-2').addEventListener('click', function() {
        limpiarErrores('paso-2');
        const especialidad = document.getElementById('especialidad').value;
        
        if (!especialidad) {
            mostrarError('especialidad', 'Debe seleccionar una especialidad.');
            return;
        }
        mostrarPaso(2);
    });

    document.getElementById('fecha_cita').addEventListener('change', function() {
        limpiarErrores('paso-3');
        const fechaIngresada = this.value;
        if (!fechaIngresada) return;

        const dateObj = new Date(fechaIngresada + 'T00:00:00');
        const diaSemana = dateObj.getDay();

        if (diaSemana === 0 || diaSemana === 6) {
            mostrarError('fecha_cita', 'No hay atención regular los fines de semana. Seleccione de Lunes a Viernes.');
            this.value = '';
        }
    });

    document.querySelector('.btn-siguiente-3').addEventListener('click', function() {
        limpiarErrores('paso-3');
        const fecha = document.getElementById('fecha_cita').value;
        const hora = document.getElementById('hora_cita').value;
        let esValido = true;

        if (!fecha) {
            mostrarError('fecha_cita', 'Seleccione una fecha válida.');
            esValido = false;
        }
        if (!hora) {
            mostrarError('hora_cita', 'Seleccione un horario disponible.');
            esValido = false;
        }

        if (esValido) mostrarPaso(3);
    });

    document.querySelector('.btn-atras-2').addEventListener('click', () => mostrarPaso(0));
    document.querySelector('.btn-atras-3').addEventListener('click', () => mostrarPaso(1));
    document.querySelector('.btn-atras-4').addEventListener('click', () => mostrarPaso(2));

    function generarResumen() {
        const selEspecialidad = document.getElementById('especialidad');
        const txtEspecialidad = selEspecialidad.options[selEspecialidad.selectedIndex].text;
        
        const selMedico = document.getElementById('medico');
        const txtMedico = selMedico.options[selMedico.selectedIndex].text;

        const selHora = document.getElementById('hora_cita');
        const txtHora = selHora.options[selHora.selectedIndex].text;

        const tipoDoc = document.getElementById('tipo_documento').value;
        const numDoc = document.getElementById('numero_documento').value;
        const nombres = document.getElementById('nombres').value;
        const apellidos = document.getElementById('apellidos').value;
        
        const fechaRaw = document.getElementById('fecha_cita').value;
        const partesFecha = fechaRaw.split('-');
        const fechaFormateada = partesFecha.length === 3 ? `${partesFecha[2]}/${partesFecha[1]}/${partesFecha[0]}` : fechaRaw;

        document.getElementById('resumen-paciente').textContent = `${nombres} ${apellidos}`;
        document.getElementById('resumen-documento').textContent = `${tipoDoc} - ${numDoc}`;
        document.getElementById('resumen-especialidad').textContent = txtEspecialidad;
        document.getElementById('resumen-medico').textContent = txtMedico;
        document.getElementById('resumen-fecha').textContent = fechaFormateada;
        document.getElementById('resumen-hora').textContent = txtHora;
    }

    document.getElementById('formulario-reserva').addEventListener('submit', function(e) {
        e.preventDefault();
        window.location.href = 'ticket.html';
    });
});