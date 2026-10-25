document.addEventListener('DOMContentLoaded', function() {
    const botonesCancelar = document.querySelectorAll('.btn-cancelar-cita');

    botonesCancelar.forEach(boton => {
        boton.addEventListener('click', function() {
            const idCita = this.getAttribute('data-id');
            const tarjetaCita = document.getElementById(`cita-${idCita}`);
            
            if (tarjetaCita) {
                tarjetaCita.style.opacity = '0';
                
                setTimeout(() => {
                    const contenedor = document.getElementById('contenedor-citas-activas');
                    tarjetaCita.remove();
                    
                    const mensaje = document.createElement('div');
                    mensaje.className = 'mensaje-exito-cancelacion';
                    mensaje.textContent = 'La cita ha sido cancelada exitosamente. El cupo ha sido liberado.';
                    
                    contenedor.appendChild(mensaje);
                    
                    const statCitasActivas = document.querySelector('.stat-numero');
                    if (statCitasActivas) {
                        statCitasActivas.textContent = '0';
                    }
                }, 300);
            }
        });
    });
});