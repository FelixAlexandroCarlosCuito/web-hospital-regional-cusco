document.addEventListener('DOMContentLoaded', function() {
    const formularioRegistro = document.getElementById('formulario-registro');
    const btnVerificar = document.getElementById('btn-verificar');
    const seccionCompletar = document.getElementById('seccion-completar-registro');

    function mostrarError(idElemento, mensaje) {
        const spanError = document.getElementById(`error-${idElemento}`);
        const input = document.getElementById(idElemento);
        if (spanError) spanError.textContent = mensaje;
        if (input) input.classList.add('input-error', 'is-invalid');
    }

    function limpiarErrores() {
        document.querySelectorAll('.mensaje-error').forEach(msg => msg.textContent = '');
        document.querySelectorAll('.input-error, .is-invalid').forEach(input => {
            input.classList.remove('input-error', 'is-invalid');
        });
        const alertaPrevia = document.getElementById('alerta-cuenta-existente');
        if (alertaPrevia) alertaPrevia.remove();
    }

    function mostrarAvisoCuentaExistente(mensaje) {
        const alerta = document.createElement('div');
        alerta.id = 'alerta-cuenta-existente';
        alerta.className = 'alert alert-warning shadow-sm mb-4';
        alerta.setAttribute('role', 'alert');

        const texto = document.createElement('span');
        texto.textContent = mensaje + ' ';

        const enlace = document.createElement('a');
        enlace.href = formularioRegistro.dataset.loginUrl;
        enlace.className = 'alert-link fw-bold';
        enlace.textContent = 'Ir a Iniciar Sesión →';

        alerta.appendChild(texto);
        alerta.appendChild(enlace);

        formularioRegistro.parentNode.insertBefore(alerta, formularioRegistro);
    }

    // 1. Lógica Asíncrona (Verificar en RENIEC)
    btnVerificar.addEventListener('click', async function() {
        limpiarErrores();

        const numDoc = document.getElementById('numero_documento').value.trim();
        const digito = document.getElementById('digito_verificador').value.trim();
        const fecha = document.getElementById('fecha_emision').value;
        const csrfToken = document.querySelector('input[name="_token"]').value;

        let fase1Valida = true;
        if (!numDoc) { mostrarError('numero_documento', 'Ingrese su número de documento.'); fase1Valida = false; }
        if (!digito) { mostrarError('digito_verificador', 'Ingrese el dígito verificador.'); fase1Valida = false; }
        if (!fecha)  { mostrarError('fecha_emision', 'Seleccione la fecha de emisión.'); fase1Valida = false; }
        if (!fase1Valida) return;

        const textoOriginal = btnVerificar.textContent;
        btnVerificar.textContent = 'Verificando...';
        btnVerificar.disabled = true;

        try {
            const respuesta = await fetch(formularioRegistro.dataset.reniecUrl, {
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
                document.getElementById('numero_documento').readOnly = true;
                document.getElementById('digito_verificador').readOnly = true;
                document.getElementById('fecha_emision').readOnly = true;

                document.getElementById('visual_nombres').value = datos.nombres;
                document.getElementById('visual_apellidos').value = `${datos.apellido_paterno} ${datos.apellido_materno}`;

                btnVerificar.style.display = 'none';
                seccionCompletar.style.display = 'block';
            } else if (respuesta.status === 409 && datos.ya_registrado) {
                mostrarError('numero_documento', 'Este documento ya cuenta con un usuario activo.');
                mostrarAvisoCuentaExistente(datos.message);
            } else {
                mostrarError('numero_documento', datos.message || 'Los datos ingresados son incorrectos.');
            } 
        } catch (error) {
            console.error('Error de red:', error);
            mostrarError('numero_documento', 'Hubo un problema de conexión con el servidor. Intente nuevamente.');
        } finally {
            btnVerificar.textContent = textoOriginal;
            btnVerificar.disabled = false;
        }
    });

    // 2. Envío Final a Laravel
    formularioRegistro.addEventListener('submit', function(e) {
        e.preventDefault();
        limpiarErrores();

        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        let esValido = true;

        if (!password || password.length < 6) {
            mostrarError('password', 'La contraseña debe tener al menos 6 caracteres.');
            esValido = false;
        }

        if (password !== confirmPassword) {
            mostrarError('confirm_password', 'Las contraseñas no coinciden.');
            esValido = false;
        }

        if (esValido) {
            const btnSubmit = formularioRegistro.querySelector('button[type="submit"]');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.textContent = 'Creando cuenta...';
            }
            formularioRegistro.submit();
        }
    });
});