document.addEventListener('DOMContentLoaded', function() {
    const pasos = [
        document.getElementById('paso-1'),
        document.getElementById('paso-2'),
        document.getElementById('paso-3'),
        document.getElementById('paso-4')
    ];
    const barraPasos = document.querySelectorAll('.barra-pasos li');
    let pasoActual = 0;

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

    const btnContinuar1 = pasos[0].querySelector('.btn-primario');
    const btnContinuar2 = pasos[1].querySelector('.btn-primario');
    const btnContinuar3 = pasos[2].querySelector('.btn-primario');

    const btnAtras2 = pasos[1].querySelector('.btn-outline');
    const btnAtras3 = pasos[2].querySelector('.btn-outline');
    const btnAtras4 = pasos[3].querySelector('.btn-outline');

    const btnValidar = document.querySelector('.btn-validar');
    
    btnValidar.addEventListener('click', function() {
        const documento = document.getElementById('numero_documento').value.trim();
        if (documento.length > 0) {
            document.getElementById('nombres').value = "Juan Carlos";
            document.getElementById('apellidos').value = "Pérez Gonzales";
        } else {
            alert("Por favor, ingrese un número de documento para validar.");
        }
    });

    btnContinuar1.addEventListener('click', function() {
        const tipoDoc = document.getElementById('tipo_documento').value;
        const numDoc = document.getElementById('numero_documento').value.trim();
        const nombres = document.getElementById('nombres').value.trim();

        if (!tipoDoc || !numDoc || !nombres) {
            alert("Complete los campos solicitados antes de continuar.");
            return;
        }
        mostrarPaso(1);
    });

    btnAtras2.addEventListener('click', () => mostrarPaso(0));
    
    btnContinuar2.addEventListener('click', function() {
        const especialidad = document.getElementById('especialidad').value;
        if (!especialidad) {
            alert("Seleccione una especialidad requerida.");
            return;
        }
        mostrarPaso(2);
    });

    btnAtras3.addEventListener('click', () => mostrarPaso(1));

    btnContinuar3.addEventListener('click', function() {
        const fecha = document.getElementById('fecha_cita').value;
        const hora = document.getElementById('hora_cita').value;
        if (!fecha || !hora) {
            alert("Seleccione una fecha y un horario disponible.");
            return;
        }
        mostrarPaso(3);
    });

    btnAtras4.addEventListener('click', () => mostrarPaso(2));

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
});