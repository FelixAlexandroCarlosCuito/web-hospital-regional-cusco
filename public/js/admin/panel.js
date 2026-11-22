/**
 * Inicializa los gráficos analíticos del panel de administración mediante Chart.js,
 * consumiendo las métricas serializadas en el bloque JSON embebido por el servidor.
 */
document.addEventListener('DOMContentLoaded', function () {
    const contenedorDatos = document.getElementById('datos-metricas-admin');
    if (!contenedorDatos || typeof Chart === 'undefined') return;

    const metricas = JSON.parse(contenedorDatos.textContent);

    // Paleta institucional mate compartida por los cuatro gráficos
    const colores = {
        azul: '#21325b',
        azulHover: '#182647',
        terracota: '#b5524c',
        salvia: '#3b7a62',
        ocre: '#c48b2c',
        piedraMedia: '#94a3b8',
        grilla: 'rgba(33, 50, 91, 0.08)',
        texto: '#5a667a',
    };

    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.color = colores.texto;

    const opcionesEjesComunes = {
        x: {
            grid: { display: false },
            border: { display: false },
        },
        y: {
            beginAtZero: true,
            border: { display: false },
            grid: { color: colores.grilla },
            ticks: { precision: 0 },
        },
    };

    const canvasEsp = document.getElementById('graficoEspecialidades');
    if (canvasEsp) {
        new Chart(canvasEsp, {
            type: 'bar',
            data: {
                labels: metricas.especialidadesLabels,
                datasets: [{
                    label: 'Total de citas',
                    data: metricas.especialidadesData,
                    backgroundColor: colores.azul,
                    hoverBackgroundColor: colores.terracota,
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 42,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: opcionesEjesComunes,
            },
        });
    }

    const canvasTendencia = document.getElementById('graficoTendencia');
    if (canvasTendencia) {
        const ctx = canvasTendencia.getContext('2d');
        const degradadoArea = ctx.createLinearGradient(0, 0, 0, 260);
        degradadoArea.addColorStop(0, 'rgba(181, 82, 76, 0.24)');
        degradadoArea.addColorStop(1, 'rgba(181, 82, 76, 0.0)');

        new Chart(canvasTendencia, {
            type: 'line',
            data: {
                labels: metricas.tendenciaLabels,
                datasets: [{
                    label: 'Citas programadas',
                    data: metricas.tendenciaValores,
                    borderColor: colores.terracota,
                    backgroundColor: degradadoArea,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: colores.terracota,
                    pointBorderWidth: 2.5,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: opcionesEjesComunes,
            },
        });
    }

    const canvasEstados = document.getElementById('graficoEstados');
    if (canvasEstados) {
        new Chart(canvasEstados, {
            type: 'doughnut',
            data: {
                labels: ['Pendiente', 'Atendido', 'Cancelado'],
                datasets: [{
                    data: metricas.estadosData,
                    backgroundColor: [colores.ocre, colores.salvia, colores.terracota],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '66%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 16 },
                    },
                },
            },
        });
    }

    const canvasSis = document.getElementById('graficoSis');
    if (canvasSis) {
        new Chart(canvasSis, {
            type: 'doughnut',
            data: {
                labels: ['Con Referencia SIS', 'Paciente Regular'],
                datasets: [{
                    data: metricas.sisData,
                    backgroundColor: [colores.azul, colores.piedraMedia],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '66%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 16 },
                    },
                },
            },
        });
    }
});