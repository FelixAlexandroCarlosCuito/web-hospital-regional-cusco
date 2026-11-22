/**
 * Inicializa los gráficos analíticos del panel de administración mediante Chart.js.
 * Consume las métricas serializadas por el servidor en un bloque JSON no ejecutable.
 */
document.addEventListener('DOMContentLoaded', function () {
    const contenedorDatos = document.getElementById('datos-metricas-admin');
    if (!contenedorDatos) return;

    const metricas = JSON.parse(contenedorDatos.textContent);

    new Chart(document.getElementById('graficoEspecialidades'), {
        type: 'bar',
        data: {
            labels: metricas.especialidadesLabels,
            datasets: [{
                label: 'Total de citas',
                data: metricas.especialidadesData,
                backgroundColor: '#1e3a5f',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    new Chart(document.getElementById('graficoTendencia'), {
        type: 'line',
        data: {
            labels: metricas.tendenciaLabels,
            datasets: [{
                label: 'Citas programadas',
                data: metricas.tendenciaValores,
                borderColor: '#6f42c1',
                backgroundColor: 'rgba(111, 66, 193, 0.12)',
                fill: true,
                tension: 0.3,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    new Chart(document.getElementById('graficoEstados'), {
        type: 'doughnut',
        data: {
            labels: ['Pendiente', 'Atendido', 'Cancelado'],
            datasets: [{
                data: metricas.estadosData,
                backgroundColor: ['#ffc107', '#198754', '#dc3545'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    new Chart(document.getElementById('graficoSis'), {
        type: 'doughnut',
        data: {
            labels: ['Con Referencia SIS', 'Paciente Regular'],
            datasets: [{
                data: metricas.sisData,
                backgroundColor: ['#0d6efd', '#6c757d'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});