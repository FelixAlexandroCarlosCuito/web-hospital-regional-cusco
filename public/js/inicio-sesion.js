document.addEventListener('DOMContentLoaded', function() {
    const formularioLogin = document.getElementById('formulario-login');

    function mostrarError(idElemento, mensaje) {
        document.getElementById(`error-${idElemento}`).textContent = mensaje;
        document.getElementById(idElemento).classList.add('input-error');
    }

    function limpiarErrores() {
        const mensajes = document.querySelectorAll('.mensaje-error');
        const inputs = document.querySelectorAll('.input-error');
        
        mensajes.forEach(msg => msg.textContent = '');
        inputs.forEach(input => input.classList.remove('input-error'));
    }

    formularioLogin.addEventListener('submit', function(e) {
        e.preventDefault();
        limpiarErrores();

        const documento = document.getElementById('documento').value.trim();
        const password = document.getElementById('password').value.trim();
        let esValido = true;

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

        if (esValido) {
            window.location.href = 'dashboard-paciente.html';
        }
    });
});