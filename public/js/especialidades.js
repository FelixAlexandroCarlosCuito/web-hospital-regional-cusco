/**
 * Filtra en tiempo real las tarjetas del catálogo público por especialidad, descripción o médico.
 */
document.addEventListener('DOMContentLoaded', function () {
    const inputBusqueda = document.getElementById('buscador-especialidades');
    const tarjetas = document.querySelectorAll('.item-especialidad');
    const mensajeVacio = document.getElementById('sin-resultados-especialidades');

    if (!inputBusqueda || !tarjetas.length) return;

    /**
     * Normaliza cadenas a minúsculas sin signos diacríticos para comparaciones tolerantes a tildes.
     */
    function normalizarTexto(texto) {
        return (texto || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    inputBusqueda.addEventListener('input', function () {
        const termino = normalizarTexto(this.value.trim());
        let visibles = 0;

        tarjetas.forEach(function (tarjeta) {
            const indice = normalizarTexto(tarjeta.dataset.busqueda);
            const coincide = !termino || indice.includes(termino);
            tarjeta.classList.toggle('d-none', !coincide);
            if (coincide) visibles++;
        });

        if (mensajeVacio) {
            mensajeVacio.classList.toggle('d-none', visibles > 0);
        }
    });
});