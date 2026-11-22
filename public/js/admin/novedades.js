/**
 * Administra la precarga del modal de edición de novedades y la confirmación de borrado.
 */
document.addEventListener('DOMContentLoaded', function () {
    const formEditar = document.getElementById('formEditarNovedad');
    const bloqueImagenActual = document.getElementById('bloque-imagen-actual');
    const previewImagen = document.getElementById('preview-imagen-actual');
    const checkEliminarImagen = document.getElementById('editar_eliminar_imagen');
    const inputNuevaImagen = document.getElementById('editar_imagen');

    document.querySelectorAll('.btn-editar-novedad').forEach(function (boton) {
        boton.addEventListener('click', function () {
            const datos = this.dataset;

            formEditar.action = datos.action;
            document.getElementById('editar_titulo').value = datos.titulo || '';
            document.getElementById('editar_categoria').value = datos.categoria || 'Comunicado';
            document.getElementById('editar_resumen').value = datos.resumen || '';
            document.getElementById('editar_contenido').value = datos.contenido || '';
            document.getElementById('editar_publicado').checked = datos.publicado === '1';

            inputNuevaImagen.value = '';
            checkEliminarImagen.checked = false;

            if (datos.imagenUrl) {
                previewImagen.src = datos.imagenUrl;
                bloqueImagenActual.style.display = 'block';
            } else {
                previewImagen.src = '';
                bloqueImagenActual.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('.form-eliminar-novedad').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const titulo = this.dataset.titulo || 'esta novedad';
            if (!confirm(`¿Eliminar definitivamente "${titulo}"? Esta acción no se puede deshacer.`)) {
                e.preventDefault();
            }
        });
    });
});