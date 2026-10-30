document.addEventListener('DOMContentLoaded', function() {
    const formularioRegistro = document.getElementById('formulario-registro');
    const btnVerificar = document.getElementById('btn-verificar');
    const seccionCompletar = document.getElementById('seccion-completar-registro');

    function mostrarError(idElemento, mensaje) {
        document.getElementById(`error-${idElemento}`).textContent = mensaje;
        document.getElementById(idElemento).classList.add('input-error');
    }

    function limpiarErrores() {
        document.querySelectorAll('.mensaje-error').forEach(msg => msg.textContent = '');
        document.querySelectorAll('.input-error').forEach(input => input.classList.remove('input-error'));
    }

    // 1. Lógica Asíncrona (Verificar en RENIEC)
    btnVerificar.addEventListener('click', async function() {
        limpiarErrores();
        const numDoc = document.getElementById('numero_documento').value.trim();
        const digito = document.getElementById('digito_verificador').value.trim();
        const fecha = document.getElementById('fecha_emision').value;
        const csrfToken = document.querySelector('input[name="_token"]').value;

        if (!numDoc || !digito || !fecha) {
            alert('Por favor, complete DNI, dígito y fecha de emisión para verificar.');
            return;
        }

        // Cambiamos el texto del botón para UX
        const textoOriginal = btnVerificar.textContent;
        btnVerificar.textContent = 'Verificando...';
        btnVerificar.disabled = true;

        try {
            const respuesta = await fetch('/verificar-reniec', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    numero_documento: numDoc,
                    digito_verificador: digito,
                    fecha_emision: fecha
                })
            });

            const datos = await respuesta.json();

            if (respuesta.ok && datos.success) {
                // Bloqueamos los inputs originales para que no los cambie después de verificar
                document.getElementById('numero_documento').readOnly = true;
                document.getElementById('digito_verificador').readOnly = true;
                document.getElementById('fecha_emision').readOnly = true;

                // Llenamos los visuales con los datos del servidor
                document.getElementById('visual_nombres').value = datos.nombres;
                document.getElementById('visual_apellidos').value = datos.apellido_paterno + ' ' + datos.apellido_materno;

                // Ocultamos el botón de verificar y mostramos la fase 2
                btnVerificar.style.display = 'none';
                seccionCompletar.style.display = 'block';
            } else {
                mostrarError('numero_documento', datos.message || 'Datos incorrectos.');
            }
        } catch (error) {
            console.error('Error de red:', error);
            alert('Hubo un problema de conexión. Intente nuevamente.');
        } finally {
            btnVerificar.textContent = textoOriginal;
            btnVerificar.disabled = false;
        }
    });

    // 2. Envío Final a Laravel
    formularioRegistro.addEventListener('submit', function(e) {
        e.preventDefault();
        limpiarErrores();

        const password = document.getElementById('password').value.trim();
        const confirmPassword = document.getElementById('confirm_password').value.trim();
        let esValido = true;

        if (!password || password.length < 6) {
            mostrarError('password', 'Mínimo 6 caracteres.');
            esValido = false;
        }

        if (password !== confirmPassword) {
            mostrarError('confirm_password', 'Las contraseñas no coinciden.');
            esValido = false;
        }

        if (esValido) {
            formularioRegistro.submit(); 
        }
    });
});