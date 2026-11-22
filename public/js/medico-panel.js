/**
 * Controla el cambio automático de fecha, el filtrado local por estado y
 * la prevención de envíos duplicados al marcar pacientes como atendidos.
 */
document.addEventListener('DOMContentLoaded', function () {
    const inputFecha = document.getElementById('fecha');
    const formFecha = document.getElementById('form-filtro-fecha-medico');

    if (inputFecha && formFecha) {
        inputFecha.addEventListener('change', function () {
            formFecha.submit();
        });
    }

    const tabsEstado = document.querySelectorAll('.tab-estado-panel');
    const filas = document.querySelectorAll('.fila-agenda-medico');
    const mensajeVacio = document.getElementById('sin-resultados-agenda');

    tabsEstado.forEach(function (tab) {
        tab.addEventListener('click', function () {
            const estadoObjetivo = this.dataset.estado || 'todos';

            tabsEstado.forEach((btn) => btn.classList.toggle('activo', btn === this));

            let visibles = 0;
            filas.forEach(function (fila) {
                const coincide = estadoObjetivo === 'todos' || fila.dataset.estado === estadoObjetivo;
                fila.classList.toggle('d-none', !coincide);
                if (coincide) visibles++;
            });

            if (mensajeVacio) {
                mensajeVacio.classList.toggle('d-none', visibles > 0);
            }
        });
    });

    // Evita doble envío si el médico pulsa varias veces el botón de atención
    document.querySelectorAll('.form-atender-cita').forEach(function (form) {
        form.addEventListener('submit', function () {
            const boton = this.querySelector('button[type="submit"]');
            if (boton) {
                boton.disabled = true;
                boton.textContent = 'Guardando...';
            }
        });
    });
});