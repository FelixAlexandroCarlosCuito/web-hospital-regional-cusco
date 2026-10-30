document.addEventListener('DOMContentLoaded', function() {
    const pasos = [
        document.getElementById('paso-1'),
        document.getElementById('paso-2'),
        document.getElementById('paso-3'),
        document.getElementById('paso-4')
    ];
    const barraPasos = document.querySelectorAll('.barra-pasos li');
    let pasoActual = 0;

    // 1. Cargar las especialidades desde Laravel al iniciar la página
    async function cargarEspecialidades() {
        try {
            const respuesta = await fetch('http://127.0.0.1:8000/api/especialidades');
            const especialidades = await respuesta.json();
            const selectEspecialidad = document.getElementById('especialidad');
            
            selectEspecialidad.innerHTML = '<option value="" disabled selected>Seleccione una especialidad</option>';
            
            especialidades.forEach(esp => {
                const option = document.createElement('option');
                option.value = esp.id;
                option.textContent = esp.nombre;
                selectEspecialidad.appendChild(option);
            });
        } catch (error) {
            console.error("Error cargando especialidades:", error);
        }
    }
    cargarEspecialidades(); // Ejecutar al cargar la página

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

    // --- VALIDAR PACIENTE (Paso 1) ---
    document.querySelector('.btn-validar').addEventListener('click', async function() {
        limpiarErrores('paso-1');
        const tipoDoc = document.getElementById('tipo_documento').value;
        const numDoc = document.getElementById('numero_documento').value.trim();
        let esValido = true;

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
            try {
                const respuesta = await fetch(`http://127.0.0.1:8000/api/paciente/${numDoc}`);
                const data = await respuesta.json();

                const inputNombres = document.getElementById('nombres');
                const inputApellidos = document.getElementById('apellidos');

                if (data.encontrado) {
                    const partesNombre = data.nombre_completo.split(' ');
                    inputNombres.value = partesNombre[0] || data.nombre_completo;
                    inputApellidos.value = partesNombre.slice(1).join(' ') || '';
                    
                    inputNombres.readOnly = true;
                    inputApellidos.readOnly = true;
                    
                    alert("Paciente encontrado. Datos autocompletados.");
                } else {
                    inputNombres.value = "";
                    inputApellidos.value = "";
                    
                    inputNombres.readOnly = false;
                    inputApellidos.readOnly = false;
                    
                    inputNombres.focus();
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

    // --- CARGAR MÉDICOS SEGÚN ESPECIALIDAD (Paso 2) ---
    document.getElementById('especialidad').addEventListener('change', async function() {
        const especialidadId = this.value;
        const selectMedico = document.getElementById('medico');
        
        selectMedico.innerHTML = '<option value="">Cargando médicos...</option>';
        selectMedico.disabled = true;

        try {
            const respuesta = await fetch(`http://127.0.0.1:8000/api/especialidades/${especialidadId}/medicos`);
            const medicos = await respuesta.json();

            selectMedico.innerHTML = '<option value="" selected>Cualquier médico disponible</option>';
            selectMedico.disabled = false;

            if (medicos.length === 0) {
                selectMedico.innerHTML = '<option value="">No hay médicos disponibles</option>';
            } else {
                medicos.forEach(medico => {
                    const option = document.createElement('option');
                    option.value = medico.id;
                    option.textContent = medico.nombre;
                    selectMedico.appendChild(option);
                });
            }
        } catch (error) {
            console.error("Error cargando médicos:", error);
            selectMedico.innerHTML = '<option value="">Error al cargar</option>';
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

    // --- FECHA Y HORA (Paso 3) ---
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

    // --- RESUMEN (Paso 4) ---
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

    // --- GUARDAR EN LARAVEL Y REDIRIGIR AL TICKET ---
    document.getElementById('formulario-reserva').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Recolectar datos reales
        const numDoc = document.getElementById('numero_documento').value;
        const nombres = document.getElementById('nombres').value;
        const apellidos = document.getElementById('apellidos').value;
        const especialidadTexto = document.getElementById('especialidad').options[document.getElementById('especialidad').selectedIndex].text;
        const medicoId = document.getElementById('medico').value; 
        const fecha = document.getElementById('fecha_cita').value;
        const hora = document.getElementById('hora_cita').value;
        const monto = 6.00; 

        const datosCita = {
            paciente_nombre: nombres + ' ' + apellidos,
            paciente_documento: numDoc,
            especialidad: especialidadTexto,
            medico_id: medicoId || null, 
            fecha: fecha,
            hora: hora,
            monto_pagar: monto
        };

        try {
            // Enviar a Laravel
            const respuesta = await fetch('http://127.0.0.1:8000/api/citas', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json' 
                },
                body: JSON.stringify(datosCita)
            });

            const resultado = await respuesta.json();

            if (respuesta.ok) {
                // Guardamos el ID de la cita para que el ticket.html sepa cuál mostrar
                localStorage.setItem('cita_id', resultado.cita.id);
                window.location.href = 'ticket.html';
            } else {
                alert('Error al guardar la cita: ' + JSON.stringify(resultado.errors));
            }
        } catch (error) {
            console.error(error);
            alert('Error de conexión con el servidor Laravel. Asegúrate de que php artisan serve esté encendido.');
        }
    });
});