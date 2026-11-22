/**
 * Alterna la visibilidad de campos de contraseña vinculados mediante el atributo data-target.
 */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-toggle-password').forEach(function (boton) {
        boton.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            if (!input) return;

            const mostrar = input.type === 'password';
            input.type = mostrar ? 'text' : 'password';

            const icono = this.querySelector('i');
            if (icono) {
                icono.classList.toggle('bi-eye', !mostrar);
                icono.classList.toggle('bi-eye-slash', mostrar);
            }

            this.setAttribute('aria-pressed', mostrar ? 'true' : 'false');
            this.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    });
});