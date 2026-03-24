document.addEventListener('DOMContentLoaded', () => {
    const blocks = document.querySelectorAll('.reveal');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.16 });

    blocks.forEach((block) => observer.observe(block));

    const counters = document.querySelectorAll('[data-count]');
    counters.forEach((counter) => {
        const max = Number(counter.getAttribute('data-count'));
        if (!Number.isFinite(max) || max <= 0) {
            return;
        }

        let value = 0;
        const steps = 32;
        const increment = Math.ceil(max / steps);

        const timer = window.setInterval(() => {
            value += increment;
            if (value >= max) {
                value = max;
                window.clearInterval(timer);
            }

            if (max >= 1000) {
                counter.textContent = value.toLocaleString('fr-FR') + '+';
            } else if (max === 95) {
                counter.textContent = value + '%';
            } else {
                counter.textContent = value + '+';
            }
        }, 26);
    });
});
