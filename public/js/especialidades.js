/**
 * Filtra en tiempo real la cartera pública de especialidades por texto y disponibilidad de turnos.
 */
document.addEventListener('DOMContentLoaded', function () {
    const inputBusqueda = document.getElementById('buscador-especialidades');
    const btnLimpiarInput = document.getElementById('btn-limpiar-input');
    const btnResetFiltros = document.getElementById('btn-reset-filtros');
    const botonesFiltro = document.querySelectorAll('.btn-filtro-esp');
    const tarjetas = document.querySelectorAll('.item-especialidad');
    const mensajeVacio = document.getElementById('sin-resultados-especialidades');
    const contador = document.getElementById('contador-especialidades');

    if (!inputBusqueda || !tarjetas.length) return;

    let filtroActual = 'todas';

    /**
     * Normaliza texto a minúsculas sin tildes para búsquedas tolerantes.
     */
    function normalizarTexto(texto) {
        return (texto || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    /**
     * Evalúa simultáneamente el término de búsqueda y el filtro de disponibilidad activo.
     */
    function aplicarFiltros() {
        const termino = normalizarTexto(inputBusqueda.value.trim());
        let visibles = 0;

        if (btnLimpiarInput) {
            btnLimpiarInput.classList.toggle('d-none', termino.length === 0);
        }

        tarjetas.forEach(function (tarjeta) {
            const indice = normalizarTexto(tarjeta.dataset.busqueda);
            const disponible = tarjeta.dataset.disponible === '1';

            const coincideTexto = !termino || indice.includes(termino);
            const coincideDisponibilidad = filtroActual === 'todas' || disponible;
            const mostrar = coincideTexto && coincideDisponibilidad;

            tarjeta.classList.toggle('d-none', !mostrar);
            if (mostrar) visibles++;
        });

        if (contador) {
            contador.textContent = visibles;
        }

        if (mensajeVacio) {
            mensajeVacio.classList.toggle('d-none', visibles > 0);
        }
    }

    inputBusqueda.addEventListener('input', aplicarFiltros);

    botonesFiltro.forEach(function (boton) {
        boton.addEventListener('click', function () {
            filtroActual = this.dataset.filtro || 'todas';
            botonesFiltro.forEach((b) => b.classList.toggle('activo', b === this));
            aplicarFiltros();
        });
    });

    if (btnLimpiarInput) {
        btnLimpiarInput.addEventListener('click', function () {
            inputBusqueda.value = '';
            inputBusqueda.focus();
            aplicarFiltros();
        });
    }

    if (btnResetFiltros) {
        btnResetFiltros.addEventListener('click', function () {
            inputBusqueda.value = '';
            filtroActual = 'todas';
            botonesFiltro.forEach((b) => b.classList.toggle('activo', b.dataset.filtro === 'todas'));
            aplicarFiltros();
        });
    }
});