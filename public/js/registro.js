document.addEventListener('DOMContentLoaded', function() {
    const formularioRegistro = document.getElementById('formulario-registro');

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

    formularioRegistro.addEventListener('submit', function(e) {
        e.preventDefault();
        limpiarErrores();

        const tipoDoc = document.getElementById('tipo_documento').value;
        const numDoc = document.getElementById('numero_documento').value.trim();
        const nombres = document.getElementById('nombres').value.trim();
        const apellidos = document.getElementById('apellidos').value.trim();
        const password = document.getElementById('password').value.trim();
        const confirmPassword = document.getElementById('confirm_password').value.trim();
        
        let esValido = true;

        if (!tipoDoc) {
            mostrarError('tipo_documento', 'Seleccione tipo de documento.');
            esValido = false;
        }

        if (!numDoc) {
            mostrarError('numero_documento', 'Ingrese número de documento.');
            esValido = false;
        } else if (tipoDoc === 'DNI' && !/^\d{8}$/.test(numDoc)) {
            mostrarError('numero_documento', 'El DNI requiere 8 dígitos.');
            esValido = false;
        } else if (tipoDoc === 'CE' && !/^[A-Za-z0-9]{9}$/.test(numDoc)) {
            mostrarError('numero_documento', 'El CE requiere 9 caracteres.');
            esValido = false;
        }

        if (!nombres) {
            mostrarError('nombres', 'Ingrese sus nombres.');
            esValido = false;
        }

        if (!apellidos) {
            mostrarError('apellidos', 'Ingrese sus apellidos.');
            esValido = false;
        }

        if (!password) {
            mostrarError('password', 'Cree una contraseña.');
            esValido = false;
        } else if (password.length < 6) {
            mostrarError('password', 'La contraseña debe tener mínimo 6 caracteres.');
            esValido = false;
        }

        if (password !== confirmPassword) {
            mostrarError('confirm_password', 'Las contraseñas no coinciden.');
            esValido = false;
        }

        if (esValido) {
            window.location.href = "{{route('login')}}";
        }
    });
});