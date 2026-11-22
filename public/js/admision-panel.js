/**
 * Gestiona el filtrado local por estado y paciente/DNI en la agenda de Admisión,
 * además del modal de confirmación para cancelaciones operativas.
 */
document.addEventListener('DOMContentLoaded', function () {
    const inputBusqueda = document.getElementById('buscador-paciente-admision');
    const tabsEstado = document.querySelectorAll('.tab-estado-panel');
    const filas = document.querySelectorAll('.fila-agenda-admision');
    const mensajeVacio = document.getElementById('sin-resultados-admision');

    let estadoActivo = 'todos';

    /**
     * Normaliza texto a minúsculas sin tildes para búsquedas rápidas en ventanilla.
     */
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
            const coincideEstado = estadoActivo === 'todos' || fila.dataset.estado === estadoActivo;
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
            estadoActivo = this.dataset.estado || 'todos';
            tabsEstado.forEach((btn) => btn.classList.toggle('activo', btn === this));
            aplicarFiltros();
        });
    });

    // Hidratación del modal de cancelación operativa y bloqueo de doble envío
    const formCancelar = document.getElementById('formCancelarAdmision');
    const txtPaciente = document.getElementById('admision-modal-paciente');
    const txtDocumento = document.getElementById('admision-modal-documento');
    const txtFecha = document.getElementById('admision-modal-fecha');
    const txtHora = document.getElementById('admision-modal-hora');
    const btnConfirmar = document.getElementById('btn-confirmar-cancelar-admision');

    if (formCancelar) {
        document.querySelectorAll('.btn-abrir-cancelar-admision').forEach(function (boton) {
            boton.addEventListener('click', function () {
                formCancelar.action = this.dataset.action || '';
                if (txtPaciente) txtPaciente.textContent = this.dataset.paciente || '—';
                if (txtDocumento) txtDocumento.textContent = this.dataset.documento || '—';
                if (txtFecha) txtFecha.textContent = this.dataset.fecha || '—';
                if (txtHora) txtHora.textContent = this.dataset.hora || '—';

                if (btnConfirmar) {
                    btnConfirmar.disabled = false;
                    btnConfirmar.textContent = 'Anular y liberar cupo';
                }
            });
        });

        formCancelar.addEventListener('submit', function () {
            if (btnConfirmar) {
                btnConfirmar.disabled = true;
                btnConfirmar.textContent = 'Anulando...';
            }
        });
    }
});