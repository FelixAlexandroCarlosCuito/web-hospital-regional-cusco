/**
 * Administra el filtrado local de novedades, la precarga del modal de edición
 * y la confirmación mediante modal para el borrado definitivo de publicaciones.
 */
document.addEventListener('DOMContentLoaded', function () {
    const formEditar = document.getElementById('formEditarNovedad');
    const bloqueImagenActual = document.getElementById('bloque-imagen-actual');
    const previewImagen = document.getElementById('preview-imagen-actual');
    const checkEliminarImagen = document.getElementById('editar_eliminar_imagen');
    const inputNuevaImagen = document.getElementById('editar_imagen');

    document.querySelectorAll('.btn-editar-novedad').forEach(function (boton) {
        boton.addEventListener('click', function () {
            if (!formEditar) return;
            const datos = this.dataset;

            formEditar.action = datos.action || '';
            document.getElementById('editar_titulo').value = datos.titulo || '';
            document.getElementById('editar_categoria').value = datos.categoria || 'Comunicado';
            document.getElementById('editar_resumen').value = datos.resumen || '';
            document.getElementById('editar_contenido').value = datos.contenido || '';
            document.getElementById('editar_publicado').checked = datos.publicado === '1';

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

    // Si se selecciona una nueva imagen de reemplazo, evita enviar también la bandera de eliminación
    if (inputNuevaImagen && checkEliminarImagen) {
        inputNuevaImagen.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                checkEliminarImagen.checked = false;
            }
        });
    }

    // Modal de confirmación para eliminación definitiva
    const formEliminar = document.getElementById('formEliminarNovedad');
    const txtTituloEliminar = document.getElementById('modal-eliminar-novedad-titulo');
    const btnConfirmarEliminar = document.getElementById('btn-confirmar-eliminar-novedad');

    if (formEliminar) {
        document.querySelectorAll('.btn-abrir-eliminar-novedad').forEach(function (boton) {
            boton.addEventListener('click', function () {
                formEliminar.action = this.dataset.action || '';
                if (txtTituloEliminar) {
                    txtTituloEliminar.textContent = this.dataset.titulo || 'Publicación sin título';
                }
                if (btnConfirmarEliminar) {
                    btnConfirmarEliminar.disabled = false;
                    btnConfirmarEliminar.textContent = 'Sí, eliminar';
                }
            });
        });

        formEliminar.addEventListener('submit', function () {
            if (btnConfirmarEliminar) {
                btnConfirmarEliminar.disabled = true;
                btnConfirmarEliminar.textContent = 'Eliminando...';
            }
        });
    }

    ['formCrearNovedad', 'formEditarNovedad'].forEach(function (idForm) {
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

    // Filtrado local combinado por texto y estado de publicación
    const inputBusqueda = document.getElementById('buscador-admin-novedades');
    const tabsEstado = document.querySelectorAll('.tab-estado-panel');
    const filas = document.querySelectorAll('.fila-admin-novedad');
    const mensajeVacio = document.getElementById('sin-resultados-novedades-admin');

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