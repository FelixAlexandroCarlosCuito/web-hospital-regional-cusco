/**
 * Valida el formato del documento de identidad y la presencia de contraseña
 * antes de enviar la solicitud de autenticación al servidor.
 */
document.addEventListener('DOMContentLoaded', function () {
    const formularioLogin = document.getElementById('formulario-login');
    const inputDocumento = document.getElementById('documento');
    const inputPassword = document.getElementById('password');
    const btnSubmit = document.getElementById('btn-submit-login');

    if (!formularioLogin || !inputDocumento || !inputPassword) return;

    function mostrarError(idElemento, mensaje) {
        const spanError = document.getElementById(`error-${idElemento}`);
        const campo = document.getElementById(idElemento);
        if (spanError) spanError.textContent = mensaje;
        if (campo) campo.classList.add('is-invalid');
    }

    function limpiarErrorCampo(idElemento) {
        const spanError = document.getElementById(`error-${idElemento}`);
        const campo = document.getElementById(idElemento);
        if (spanError) spanError.textContent = '';
        if (campo) campo.classList.remove('is-invalid');
    }

    inputDocumento.addEventListener('input', function () {
        limpiarErrorCampo('documento');
    });

    inputPassword.addEventListener('input', function () {
        limpiarErrorCampo('password');
    });

    formularioLogin.addEventListener('submit', function (e) {
        e.preventDefault();
        limpiarErrorCampo('documento');
        limpiarErrorCampo('password');

        const documento = inputDocumento.value.trim();
        const password = inputPassword.value.trim();
        let esValido = true;

        // Acepta DNI peruano (8 dígitos) o Carnet de Extranjería (9 caracteres alfanuméricos)
        if (!documento) {
            mostrarError('documento', 'Ingrese su número de documento.');
            esValido = false;
        } else if (!/^\d{8}$/.test(documento) && !/^[A-Za-z0-9]{9}$/.test(documento)) {
            mostrarError('documento', 'Ingrese un DNI (8 dígitos) o CE (9 caracteres) válido.');
            esValido = false;
        }

        if (!password) {
            mostrarError('password', 'Ingrese su contraseña.');
            esValido = false;
        }

        if (!esValido) return;

        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.textContent = 'Verificando...';
        }

        formularioLogin.submit();
    });
});