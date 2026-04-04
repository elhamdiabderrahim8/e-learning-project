document.querySelectorAll('.btn-pay-trigger').forEach(btn => {
    btn.addEventListener('click', function() {
        const courseId = this.getAttribute('data-id');
        // On met l'ID dans le champ caché du modal
        document.getElementById('selected-course-id').value = courseId;
        // On affiche manuellement le modal (ou via l'URL)
        window.location.hash = 'step-payment';
    });
});