/**
 * Gestiona los modales de alta, edición y baja lógica del personal médico y de admisión,
 * además del filtrado local independiente en cada tabla del módulo.
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
        if (!selectRolCrear || !bloqueMedicoCrear) return;
        const esMedico = selectRolCrear.value === 'medico';
        bloqueMedicoCrear.style.display = esMedico ? 'block' : 'none';
        if (inputEspCrear) inputEspCrear.required = esMedico;
        if (inputConsCrear) inputConsCrear.required = esMedico;
    }

    if (selectRolCrear) {
        selectRolCrear.addEventListener('change', actualizarCamposMedicoCrear);
        actualizarCamposMedicoCrear();
    }

    const formEditar = document.getElementById('formEditarPersonal');
    const badgeRolEditar = document.getElementById('editar_badge_rol');
    const bloqueMedicoEditar = document.getElementById('campos-medico-editar');
    const selectEspEditar = document.getElementById('editar_especialidad_id');
    const inputConsEditar = document.getElementById('editar_consultorio');

    document.querySelectorAll('.btn-editar-personal').forEach(function (boton) {
        boton.addEventListener('click', function () {
            if (!formEditar) return;
            const datos = this.dataset;
            const esMedico = datos.rol === 'medico';

            formEditar.action = datos.action || '';
            if (badgeRolEditar) badgeRolEditar.textContent = esMedico ? 'Médico' : 'Admisión';

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

            if (bloqueMedicoEditar) bloqueMedicoEditar.style.display = esMedico ? 'block' : 'none';
            if (selectEspEditar) selectEspEditar.required = esMedico;
            if (inputConsEditar) inputConsEditar.required = esMedico;

            if (esMedico && selectEspEditar && inputConsEditar) {
                const especialidadActual = datos.especialidadId || '';

                // Permite conservar una especialidad inactiva solo si ya pertenece al médico editado
                Array.from(selectEspEditar.options).forEach(function (opcion) {
                    if (!opcion.value) return;
                    const estaActiva = opcion.dataset.activa === '1';
                    opcion.disabled = !estaActiva && opcion.value !== especialidadActual;
                });

                selectEspEditar.value = especialidadActual;
                inputConsEditar.value = datos.consultorio || '';
            } else if (selectEspEditar && inputConsEditar) {
                selectEspEditar.value = '';
                inputConsEditar.value = '';
            }
        });
    });

    // Modal de confirmación para baja lógica con advertencia de citas pendientes en médicos
    const formDesactivar = document.getElementById('formDesactivarPersonal');
    const txtTextoDesactivar = document.getElementById('modal-desactivar-personal-texto');
    const avisoPendientes = document.getElementById('aviso-pendientes-personal');
    const txtPendientes = document.getElementById('modal-desactivar-personal-pendientes');
    const btnConfirmarDesactivar = document.getElementById('btn-confirmar-desactivar-personal');

    if (formDesactivar) {
        document.querySelectorAll('.btn-abrir-desactivar-personal').forEach(function (boton) {
            boton.addEventListener('click', function () {
                const nombre = this.dataset.nombre || 'este usuario';
                const esMedico = this.dataset.rol === 'medico';
                const pendientes = parseInt(this.dataset.pendientes || '0', 10);

                formDesactivar.action = this.dataset.action || '';

                if (txtTextoDesactivar) {
                    txtTextoDesactivar.textContent = '';
                    const textoInicio = document.createTextNode(
                        esMedico
                            ? 'Al desactivar a '
                            : 'Al desactivar la cuenta de '
                    );
                    const nodoNombre = document.createElement('strong');
                    nodoNombre.className = 'text-hrc-primario';
                    nodoNombre.textContent = nombre;
                    const textoFin = document.createTextNode(
                        esMedico
                            ? ', dejará de recibir nuevas asignaciones automáticas y se bloqueará su acceso al sistema.'
                            : ', se bloqueará su acceso al módulo de ventanilla y admisión.'
                    );
                    txtTextoDesactivar.append(textoInicio, nodoNombre, textoFin);
                }

                if (avisoPendientes && txtPendientes) {
                    txtPendientes.textContent = pendientes;
                    avisoPendientes.classList.toggle('d-none', !esMedico || pendientes <= 0);
                }

                if (btnConfirmarDesactivar) {
                    btnConfirmarDesactivar.disabled = false;
                    btnConfirmarDesactivar.textContent = 'Sí, desactivar';
                }
            });
        });

        formDesactivar.addEventListener('submit', function () {
            if (btnConfirmarDesactivar) {
                btnConfirmarDesactivar.disabled = true;
                btnConfirmarDesactivar.textContent = 'Desactivando...';
            }
        });
    }

    // Evita envíos duplicados al registrar o actualizar cuentas de personal
    ['formCrearPersonal', 'formEditarPersonal'].forEach(function (idForm) {
        const form = document.getElementById(idForm);
        if (!form) return;
        form.addEventListener('submit', function () {
            const boton = this.querySelector('button[type="submit"]');
            if (boton) {
                boton.disabled = true;
                boton.textContent = 'Guardando...';
            }
        });
    });

    /**
     * Normaliza cadenas a minúsculas sin diacríticos para búsquedas tolerantes.
     */
    function normalizar(texto) {
        return (texto || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    /**
     * Inicializa el filtrado combinado por texto y estado sobre una tabla específica del módulo.
     */
    function inicializarFiltroTabla(config) {
        const input = document.getElementById(config.inputId);
        const tabs = document.querySelectorAll(config.tabsSelector);
        const filas = document.querySelectorAll(config.filasSelector);
        const vacio = document.getElementById(config.vacioId);

        if (!filas.length) return;

        let estadoActivo = 'todos';

        function aplicar() {
            const termino = normalizar(input ? input.value.trim() : '');
            let visibles = 0;

            filas.forEach(function (fila) {
                const coincideEstado = estadoActivo === 'todos' || fila.dataset.estado === estadoActivo;
                const coincideTexto = !termino || normalizar(fila.dataset.busqueda).includes(termino);
                const mostrar = coincideEstado && coincideTexto;

                fila.classList.toggle('d-none', !mostrar);
                if (mostrar) visibles++;
            });

            if (vacio) {
                vacio.classList.toggle('d-none', visibles > 0);
            }
        }

        if (input) input.addEventListener('input', aplicar);

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                estadoActivo = this.dataset.estado || 'todos';
                tabs.forEach((btn) => btn.classList.toggle('activo', btn === this));
                aplicar();
            });
        });
    }

    inicializarFiltroTabla({
        inputId: 'buscador-admin-medicos',
        tabsSelector: '.tab-filtro-medicos',
        filasSelector: '.fila-admin-medico',
        vacioId: 'sin-resultados-medicos-admin',
    });

    inicializarFiltroTabla({
        inputId: 'buscador-admin-admision',
        tabsSelector: '.tab-filtro-admision',
        filasSelector: '.fila-admin-admision',
        vacioId: 'sin-resultados-admision-admin',
    });
});