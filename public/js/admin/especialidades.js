/**
 * Controla la carga dinámica del modal de edición y la confirmación de baja lógica
 * en la vista de gestión de especialidades.
 */
document.addEventListener('DOMContentLoaded', function () {
    const formEditar = document.getElementById('formEditarEspecialidad');
    const inputNombre = document.getElementById('editar_nombre');
    const inputDescripcion = document.getElementById('editar_descripcion');

    document.querySelectorAll('.btn-editar-especialidad').forEach(function (boton) {
        boton.addEventListener('click', function () {
            formEditar.action = this.dataset.action;
            inputNombre.value = this.dataset.nombre || '';
            inputDescripcion.value = this.dataset.descripcion || '';
        });
    });

    // Intercepta el envío solo cuando existe un mensaje de confirmación (desactivación)
    document.querySelectorAll('.form-toggle-estado').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const mensaje = this.dataset.mensaje;
            if (mensaje && !confirm(mensaje)) {
                e.preventDefault();
            }
        });
    });
});