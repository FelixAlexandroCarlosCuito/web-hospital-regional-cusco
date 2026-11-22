/**
 * Gestiona el comportamiento dinámico de los modales de registro, edición y
 * cambio de estado del personal médico y de admisión.
 */
document.addEventListener('DOMContentLoaded', function () {
    const selectRolCrear = document.getElementById('crear_rol');
    const bloqueMedicoCrear = document.getElementById('campos-medico-crear');
    const inputEspCrear = document.getElementById('crear_especialidad_id');
    const inputConsCrear = document.getElementById('crear_consultorio');

    /**
     * Sincroniza la visibilidad y obligatoriedad de los campos clínicos según el rol seleccionado.
     */
    function actualizarCamposMedicoCrear() {
        const esMedico = selectRolCrear.value === 'medico';
        bloqueMedicoCrear.style.display = esMedico ? 'block' : 'none';
        inputEspCrear.required = esMedico;
        inputConsCrear.required = esMedico;
    }

    selectRolCrear.addEventListener('change', actualizarCamposMedicoCrear);
    actualizarCamposMedicoCrear();

    const formEditar = document.getElementById('formEditarPersonal');
    const badgeRolEditar = document.getElementById('editar_badge_rol');
    const bloqueMedicoEditar = document.getElementById('campos-medico-editar');
    const selectEspEditar = document.getElementById('editar_especialidad_id');
    const inputConsEditar = document.getElementById('editar_consultorio');

    document.querySelectorAll('.btn-editar-personal').forEach(function (boton) {
        boton.addEventListener('click', function () {
            const datos = this.dataset;
            const esMedico = datos.rol === 'medico';

            formEditar.action = datos.action;
            badgeRolEditar.textContent = esMedico ? 'Médico' : 'Admisión';

            document.getElementById('editar_tipo_documento').value = datos.tipoDocumento || 'DNI';
            document.getElementById('editar_numero_documento').value = datos.numeroDocumento || '';
            document.getElementById('editar_nombres').value = datos.nombres || '';
            document.getElementById('editar_apellido_paterno').value = datos.apellidoPaterno || '';
            document.getElementById('editar_apellido_materno').value = datos.apellidoMaterno || '';
            document.getElementById('editar_fecha_nacimiento').value = datos.fechaNacimiento || '';
            document.getElementById('editar_telefono').value = datos.telefono || '';
            document.getElementById('editar_correo').value = datos.correo || '';
            document.getElementById('editar_direccion').value = datos.direccion || '';
            document.getElementById('editar_password').value = '';

            bloqueMedicoEditar.style.display = esMedico ? 'block' : 'none';
            selectEspEditar.required = esMedico;
            inputConsEditar.required = esMedico;

            if (esMedico) {
                const especialidadActual = datos.especialidadId || '';

                // Permite conservar una especialidad inactiva solo si ya pertenece al médico editado
                Array.from(selectEspEditar.options).forEach(function (opcion) {
                    if (!opcion.value) return;
                    const estaActiva = opcion.dataset.activa === '1';
                    opcion.disabled = !estaActiva && opcion.value !== especialidadActual;
                });

                selectEspEditar.value = especialidadActual;
                inputConsEditar.value = datos.consultorio || '';
            } else {
                selectEspEditar.value = '';
                inputConsEditar.value = '';
            }
        });
    });

    document.querySelectorAll('.form-toggle-estado').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const mensaje = this.dataset.mensaje;
            if (mensaje && !confirm(mensaje)) {
                e.preventDefault();
            }
        });
    });
});