/**
 * Controla la aparición progresiva de bloques al desplazarse y la sombra del encabezado fijo.
 */
document.addEventListener('DOMContentLoaded', function () {
    const elementosRevelables = document.querySelectorAll('.revelar-scroll');
    const prefiereMovimientoReducido = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (prefiereMovimientoReducido || !('IntersectionObserver' in window)) {
        elementosRevelables.forEach(function (el) {
            el.classList.add('visible');
        });
    } else {
        const observador = new IntersectionObserver(function (entradas, obs) {
            entradas.forEach(function (entrada) {
                if (!entrada.isIntersecting) return;

                const retraso = parseInt(entrada.target.dataset.delay || '0', 10);
                if (retraso > 0) {
                    entrada.target.style.transitionDelay = `${retraso}ms`;
                }

                entrada.target.classList.add('visible');
                obs.unobserve(entrada.target);
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -30px 0px'
        });

        elementosRevelables.forEach(function (el) {
            observador.observe(el);
        });
    }

    const header = document.querySelector('.header-hrc');
    if (header) {
        const actualizarSombraHeader = function () {
            header.classList.toggle('con-sombra', window.scrollY > 12);
        };

        window.addEventListener('scroll', actualizarSombraHeader, { passive: true });
        actualizarSombraHeader();
    }
});