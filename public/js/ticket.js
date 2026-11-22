/**
 * Gestiona la impresión de la constancia de cita desde el botón de acción del ticket.
 */
document.addEventListener('DOMContentLoaded', function () {
    const btnImprimir = document.getElementById('btn-imprimir-ticket');
    if (!btnImprimir) return;

    btnImprimir.addEventListener('click', function () {
        window.print();
    });
});