document.addEventListener('DOMContentLoaded', () => {
    const iframe = document.getElementById('admin-frame');
    const links = document.querySelectorAll('.sidebar nav ul li a');

    if (links.length === 0) return;

    const getSrc = link => {
        if (link.dataset.src) return link.dataset.src;
        if (link.id === 'tab-students') return 'students.html';
        if (link.id === 'tab-professors') return 'professors.html';
        if (link.id === 'tab-payments') return 'payments.html';
        return null;
    };

    links.forEach(link => {
        link.addEventListener('click', event => {
            event.preventDefault();

            links.forEach(l => l.classList.remove('active'));
            link.classList.add('active');

            const src = getSrc(link);
            if (iframe && src) {
                iframe.src = src === 'dashboard' ? 'students.php' : src;
                iframe.style.border = '1px solid #cbd5e0';
                iframe.style.boxShadow = 'none';
            }
        });
    });

    if (iframe) {
        iframe.addEventListener('load', () => {
            iframe.style.background = '#fff';
            iframe.style.border = '1px solid #cbd5e0';
            iframe.style.boxShadow = 'none';
        });
    }
});