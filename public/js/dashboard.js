document.addEventListener('DOMContentLoaded', function() {
    const formulariosCancelar = document.querySelectorAll('.formulario-cancelar');

    formulariosCancelar.forEach(formulario => {
        formulario.addEventListener('submit', function(e) {
            // Detenemos el envío inmediato
            e.preventDefault();
            
            // Pedimos confirmación al paciente
            const confirmacion = confirm('¿Está seguro de que desea cancelar esta cita? Esta acción liberará el cupo y no se puede deshacer.');
            
            // Si el paciente acepta, liberamos el formulario para que Laravel haga la eliminación real en MySQL
            if (confirmacion) {
                this.submit();
            }
        });
    });
});