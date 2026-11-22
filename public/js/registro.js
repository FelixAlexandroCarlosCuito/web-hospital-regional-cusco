/**
 * Controla el registro en dos fases: validación asíncrona de identidad en RENIEC
 * y validación local de credenciales antes del envío definitivo a Laravel.
 */
document.addEventListener('DOMContentLoaded', function () {
    const formularioRegistro = document.getElementById('formulario-registro');
    const btnVerificar = document.getElementById('btn-verificar');
    const bloqueVerificar = document.getElementById('bloque-accion-verificar');
    const btnCambiarDocumento = document.getElementById('btn-cambiar-documento');
    const seccionCompletar = document.getElementById('seccion-completar-registro');

    const inputNumDoc = document.getElementById('numero_documento');
    const inputDigito = document.getElementById('digito_verificador');
    const inputFecha = document.getElementById('fecha_emision');
    const inputNombres = document.getElementById('visual_nombres');
    const inputApellidos = document.getElementById('visual_apellidos');
    const inputCorreo = document.getElementById('correo');
    const inputPassword = document.getElementById('password');
    const inputConfirm = document.getElementById('confirm_password');
    const chipPaso1 = document.getElementById('indicador-paso-1');
    const chipPaso2 = document.getElementById('indicador-paso-2');

    if (!formularioRegistro || !btnVerificar || !seccionCompletar) return;

    let identidadVerificada = formularioRegistro.dataset.recuperarFase2 === '1';

    function mostrarError(idElemento, mensaje) {
        const spanError = document.getElementById(`error-${idElemento}`);
        const input = document.getElementById(idElemento);
        if (spanError) spanError.textContent = mensaje;
        if (input) input.classList.add('is-invalid');
    }

    function limpiarErrorCampo(idElemento) {
        const spanError = document.getElementById(`error-${idElemento}`);
        const input = document.getElementById(idElemento);
        if (spanError) spanError.textContent = '';
        if (input) input.classList.remove('is-invalid');
    }

    function limpiarErrores() {
        document.querySelectorAll('.mensaje-error').forEach((msg) => {
            msg.textContent = '';
        });
        document.querySelectorAll('.is-invalid').forEach((input) => {
            input.classList.remove('is-invalid');
        });
        const alertaPrevia = document.getElementById('alerta-cuenta-existente');
        if (alertaPrevia) alertaPrevia.remove();
    }

    function actualizarEstadoFases(verificado) {
        identidadVerificada = verificado;

        inputNumDoc.readOnly = verificado;
        inputDigito.readOnly = verificado;
        inputFecha.readOnly = verificado;

        if (bloqueVerificar) bloqueVerificar.style.display = verificado ? 'none' : 'grid';
        seccionCompletar.style.display = verificado ? 'block' : 'none';

        if (chipPaso1 && chipPaso2) {
            chipPaso1.classList.toggle('activo', !verificado);
            chipPaso1.classList.toggle('completado', verificado);
            chipPaso2.classList.toggle('activo', verificado);
        }
    }

    function mostrarAvisoCuentaExistente(mensaje) {
        const alerta = document.createElement('div');
        alerta.id = 'alerta-cuenta-existente';
        alerta.className = 'alerta-mate-aviso small p-3 mb-3';
        alerta.setAttribute('role', 'alert');

        const texto = document.createElement('span');
        texto.textContent = mensaje + ' ';

        const enlace = document.createElement('a');
        enlace.href = formularioRegistro.dataset.loginUrl;
        enlace.className = 'fw-bold text-hrc-primario text-decoration-underline';
        enlace.textContent = 'Ir a Iniciar Sesión';

        alerta.appendChild(texto);
        alerta.appendChild(enlace);

        formularioRegistro.parentNode.insertBefore(alerta, formularioRegistro);
    }

    async function consultarReniec(silencioso) {
        const numDoc = inputNumDoc.value.trim();
        const digito = inputDigito.value.trim();
        const fecha = inputFecha.value;
        const csrfToken = document.querySelector('input[name="_token"]').value;

        if (!silencioso) {
            limpiarErrores();

            let fase1Valida = true;
            if (!numDoc) {
                mostrarError('numero_documento', 'Ingrese su número de DNI.');
                fase1Valida = false;
            } else if (!/^\d{8}$/.test(numDoc)) {
                mostrarError('numero_documento', 'El DNI debe contener 8 dígitos numéricos.');
                fase1Valida = false;
            }

            if (!digito) {
                mostrarError('digito_verificador', 'Ingrese el dígito verificador.');
                fase1Valida = false;
            }

            if (!fecha) {
                mostrarError('fecha_emision', 'Seleccione la fecha de emisión.');
                fase1Valida = false;
            }

            if (!fase1Valida) return;
        }

        const textoOriginal = btnVerificar.textContent;
        btnVerificar.textContent = 'Verificando identidad...';
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
                inputNombres.value = datos.nombres;
                inputApellidos.value = `${datos.apellido_paterno} ${datos.apellido_materno}`;
                actualizarEstadoFases(true);

                if (!silencioso && inputPassword) {
                    inputCorreo ? inputCorreo.focus() : inputPassword.focus();
                }
            } else if (!silencioso && respuesta.status === 409 && datos.ya_registrado) {
                mostrarError('numero_documento', 'Este documento ya cuenta con un usuario activo.');
                mostrarAvisoCuentaExistente(datos.message);
            } else if (!silencioso) {
                mostrarError('numero_documento', datos.message || 'Los datos ingresados no coinciden con RENIEC.');
            }
        } catch (error) {
            if (!silencioso) {
                mostrarError('numero_documento', 'No se pudo conectar con el servicio de verificación. Intente nuevamente.');
            }
        } finally {
            btnVerificar.textContent = textoOriginal;
            btnVerificar.disabled = false;
        }
    }

    btnVerificar.addEventListener('click', function () {
        consultarReniec(false);
    });

    if (btnCambiarDocumento) {
        btnCambiarDocumento.addEventListener('click', function () {
            limpiarErrores();
            inputNombres.value = '';
            inputApellidos.value = '';
            actualizarEstadoFases(false);
            inputNumDoc.focus();
        });
    }

    [inputNumDoc, inputDigito, inputFecha, inputCorreo, inputPassword, inputConfirm].forEach(function (campo) {
        if (!campo) return;
        campo.addEventListener('input', function () {
            limpiarErrorCampo(this.id);
        });
    });

    formularioRegistro.addEventListener('submit', function (e) {
        e.preventDefault();

        // Si el usuario presiona Enter antes de verificar, dispara la consulta RENIEC en lugar de validar contraseñas ocultas
        if (!identidadVerificada) {
            consultarReniec(false);
            return;
        }

        limpiarErrores();

        const password = inputPassword.value;
        const confirmPassword = inputConfirm.value;
        const correo = inputCorreo ? inputCorreo.value.trim() : '';
        let esValido = true;

        if (correo && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
            mostrarError('correo', 'Ingrese un correo electrónico válido.');
            esValido = false;
        }

        if (!password || password.length < 6) {
            mostrarError('password', 'La contraseña debe tener al menos 6 caracteres.');
            esValido = false;
        }

        if (password !== confirmPassword) {
            mostrarError('confirm_password', 'Las contraseñas no coinciden.');
            esValido = false;
        }

        if (!esValido) return;

        const btnSubmit = document.getElementById('btn-submit-registro');
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.textContent = 'Creando cuenta...';
        }

        formularioRegistro.submit();
    });

    // Al volver con errores de servidor en Fase 2, hidrata nombres sin borrar los mensajes devueltos por Laravel
    if (identidadVerificada && inputNumDoc.value && inputDigito.value && inputFecha.value) {
        actualizarEstadoFases(true);
        consultarReniec(true);
    }
});