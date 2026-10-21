document.addEventListener('DOMContentLoaded', function() {
    const btnImprimir = document.getElementById('btn-imprimir');
    
    if (btnImprimir) {
        btnImprimir.addEventListener('click', function() {
            window.print();
        });
    }
});