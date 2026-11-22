/**
 * Controla el filtrado local del catálogo, la hidratación del modal de edición
 * con vista previa de imagen y la confirmación de baja lógica en especialidades.
 */
document.addEventListener('DOMContentLoaded', function () {
    const formEditar = document.getElementById('formEditarEspecialidad');
    const inputNombre = document.getElementById('editar_nombre');
    const inputDescripcion = document.getElementById('editar_descripcion');
    const bloqueImagenActual = document.getElementById('bloque-imagen-esp-actual');
    const previewImagen = document.getElementById('preview-imagen-esp-actual');
    const checkEliminarImagen = document.getElementById('editar_eliminar_imagen_esp');
    const inputNuevaImagen = document.getElementById('editar_imagen');

    document.querySelectorAll('.btn-editar-especialidad').forEach(function (boton) {
        boton.addEventListener('click', function () {
            if (!formEditar) return;
            const datos = this.dataset;

            formEditar.action = datos.action || '';
            if (inputNombre) inputNombre.value = datos.nombre || '';
            if (inputDescripcion) inputDescripcion.value = datos.descripcion || '';
            if (inputNuevaImagen) inputNuevaImagen.value = '';
            if (checkEliminarImagen) checkEliminarImagen.checked = false;

            if (datos.imagenUrl && previewImagen && bloqueImagenActual) {
                previewImagen.src = datos.imagenUrl;
                bloqueImagenActual.style.display = 'block';
            } else if (previewImagen && bloqueImagenActual) {
                previewImagen.src = '';
                bloqueImagenActual.style.display = 'none';
            }
        });
    });

    // Evita enviar la bandera de eliminación cuando el usuario elige una imagen de reemplazo
    if (inputNuevaImagen && checkEliminarImagen) {
        inputNuevaImagen.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                checkEliminarImagen.checked = false;
            }
        });
    }

    // Hidrata el modal de confirmación para baja lógica y advierte si existen citas pendientes
    const formDesactivar = document.getElementById('formDesactivarEspecialidad');
    const txtNombreDesactivar = document.getElementById('modal-desactivar-esp-nombre');
    const avisoPendientes = document.getElementById('aviso-pendientes-esp');
    const txtPendientes = document.getElementById('modal-desactivar-esp-pendientes');
    const btnConfirmarDesactivar = document.getElementById('btn-confirmar-desactivar-esp');

    if (formDesactivar) {
        document.querySelectorAll('.btn-abrir-desactivar-esp').forEach(function (boton) {
            boton.addEventListener('click', function () {
                formDesactivar.action = this.dataset.action || '';
                if (txtNombreDesactivar) txtNombreDesactivar.textContent = this.dataset.nombre || '—';

                const pendientes = parseInt(this.dataset.pendientes || '0', 10);
                if (avisoPendientes && txtPendientes) {
                    txtPendientes.textContent = pendientes;
                    avisoPendientes.classList.toggle('d-none', pendientes <= 0);
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

    // Filtrado local combinado por texto y estado activo/inactivo
    const inputBusqueda = document.getElementById('buscador-admin-especialidades');
    const tabsEstado = document.querySelectorAll('.tab-estado-panel');
    const filas = document.querySelectorAll('.fila-admin-especialidad');
    const mensajeVacio = document.getElementById('sin-resultados-especialidades-admin');

    let estadoActivo = 'todas';

    function normalizar(texto) {
        return (texto || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    function aplicarFiltros() {
        const termino = normalizar(inputBusqueda ? inputBusqueda.value.trim() : '');
        let visibles = 0;

        filas.forEach(function (fila) {
            const coincideEstado = estadoActivo === 'todas' || fila.dataset.estado === estadoActivo;
            const coincideTexto = !termino || normalizar(fila.dataset.busqueda).includes(termino);
            const mostrar = coincideEstado && coincideTexto;

            fila.classList.toggle('d-none', !mostrar);
            if (mostrar) visibles++;
        });

        if (mensajeVacio) {
            mensajeVacio.classList.toggle('d-none', visibles > 0);
        }
    }

    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', aplicarFiltros);
    }

    tabsEstado.forEach(function (tab) {
        tab.addEventListener('click', function () {
            estadoActivo = this.dataset.estado || 'todas';
            tabsEstado.forEach((btn) => btn.classList.toggle('activo', btn === this));
            aplicarFiltros();
        });
    });
});