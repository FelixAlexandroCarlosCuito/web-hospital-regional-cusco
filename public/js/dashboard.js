/**
 * Configura el modal de confirmación de cancelación con los datos de la cita seleccionada
 * y previene envíos duplicados al confirmar la baja lógica.
 */
document.addEventListener('DOMContentLoaded', function () {
    const formCancelar = document.getElementById('formConfirmarCancelacion');
    const txtEspecialidad = document.getElementById('modal-cancelar-especialidad');
    const txtFecha = document.getElementById('modal-cancelar-fecha');
    const txtHora = document.getElementById('modal-cancelar-hora');
    const btnConfirmar = document.getElementById('btn-confirmar-cancelacion');

    if (!formCancelar) return;

    document.querySelectorAll('.btn-abrir-cancelar').forEach(function (boton) {
        boton.addEventListener('click', function () {
            formCancelar.action = this.dataset.action || '';
            if (txtEspecialidad) txtEspecialidad.textContent = this.dataset.especialidad || 'Cita médica';
            if (txtFecha) txtFecha.textContent = this.dataset.fecha || '';
            if (txtHora) txtHora.textContent = this.dataset.hora || '';

            if (btnConfirmar) {
                btnConfirmar.disabled = false;
                btnConfirmar.textContent = 'Sí, cancelar cita';
            }
        });
    });

    formCancelar.addEventListener('submit', function () {
        if (btnConfirmar) {
            btnConfirmar.disabled = true;
            btnConfirmar.textContent = 'Cancelando...';
        }
    });
});