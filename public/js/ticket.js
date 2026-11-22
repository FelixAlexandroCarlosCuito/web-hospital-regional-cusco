/**
 * Controla la impresión de la constancia y renderiza el código QR vectorial
 * apuntando al enlace firmado de verificación en tiempo real.
 */
document.addEventListener('DOMContentLoaded', function () {
    const botonImprimir = document.getElementById('btn-imprimir-ticket');
    if (botonImprimir) {
        botonImprimir.addEventListener('click', function () {
            window.print();
        });
    }

    const contenedorQr = document.getElementById('qr-constancia');
    if (!contenedorQr) return;

    const urlVerificacion = contenedorQr.dataset.url || '';
    if (!urlVerificacion) return;

    if (typeof window.qrcode !== 'function') {
        contenedorQr.innerHTML = '<span class="texto-xs text-muted">QR no disponible sin conexión</span>';
        return;
    }

    // Corrección de error M asegura lectura estable en papel común y pantallas con brillo bajo
    const qr = window.qrcode(0, 'M');
    qr.addData(urlVerificacion);
    qr.make();

    contenedorQr.innerHTML = qr.createSvgTag(4, 2);
});