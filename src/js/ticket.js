document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Configuración del Temporizador
    let tiempoRestante = 20 * 60; // 20 minutos en segundos
    const reloj = document.getElementById('reloj');
    const contenedorTemporizador = document.getElementById('contenedor-temporizador');
    const btnSimularPago = document.getElementById('btn-simular-pago');
    const cabecera = document.getElementById('ticket-cabecera');
    const tituloEstado = document.getElementById('titulo-estado');
    const mensajeEstado = document.getElementById('mensaje-estado');
    const iconoEstado = document.getElementById('icono-estado');
    const advertencia = document.getElementById('ticket-advertencia');
    let intervalo;

    // Función para formatear el tiempo (MM:SS)
    function formatearTiempo(segundos) {
        const min = Math.floor(segundos / 60);
        const seg = segundos % 60;
        return `${min.toString().padStart(2, '0')}:${seg.toString().padStart(2, '0')}`;
    }

    // Iniciar el reloj
    function iniciarReloj() {
        intervalo = setInterval(() => {
            tiempoRestante--;
            reloj.textContent = formatearTiempo(tiempoRestante);

            // Efecto de urgencia cuando quedan menos de 2 minutos
            if (tiempoRestante <= 120) {
                reloj.style.color = '#dc2626';
                reloj.style.animation = 'parpadeo 1s infinite';
            }

            // Si llega a 0, se cancela automáticamente
            if (tiempoRestante <= 0) {
                clearInterval(intervalo);
                cancelarCitaAutomaticamente();
            }
        }, 1000);
    }

    // Estado: Cancelado por falta de pago
    function cancelarCitaAutomaticamente() {
        cabecera.style.backgroundColor = '#dc2626'; // Rojo
        iconoEstado.textContent = '✕';
        iconoEstado.style.color = '#dc2626';
        tituloEstado.textContent = 'Cita Cancelada';
        mensajeEstado.textContent = 'El tiempo de pago ha expirado.';
        contenedorTemporizador.style.display = 'none';
        advertencia.style.display = 'none';
        btnSimularPago.style.display = 'none';
        
        // Mostrar aviso de cancelación
        const aviso = document.createElement('div');
        aviso.className = 'mensaje-error';
        aviso.style.textAlign = 'center';
        aviso.style.marginTop = '20px';
        aviso.innerHTML = '<strong>Su cita ha sido cancelada automáticamente.</strong><br>El cupo ha sido liberado. Por favor, genere una nueva reserva.';
        document.querySelector('.ticket-cuerpo').appendChild(aviso);
    }

    // Estado: Pagado y Confirmado
    btnSimularPago.addEventListener('click', function() {
        clearInterval(intervalo); // Detener el reloj
        
        cabecera.style.backgroundColor = '#059669'; // Verde
        iconoEstado.textContent = '✓';
        iconoEstado.style.color = '#059669';
        tituloEstado.textContent = '¡Cita Confirmada!';
        mensajeEstado.textContent = 'Pago verificado en Caja. Cita en vigor.';
        
        contenedorTemporizador.innerHTML = '<h3 style="color: #059669; font-size: 20px; margin: 0;">✅ PAGO CONFIRMADO</h3>';
        advertencia.textContent = 'Presente este ticket impreso en Admisión 15 minutos antes de su cita.';
        advertencia.style.backgroundColor = '#ecfdf5';
        advertencia.style.color = '#047857';
        advertencia.style.borderColor = '#a7f3d0';
        
        btnSimularPago.style.display = 'none';
    });

    // Inicializar
    iniciarReloj();
});