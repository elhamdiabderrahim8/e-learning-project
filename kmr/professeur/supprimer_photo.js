function confirmerSuppression() {
    // Alerte native Enjah
    if (confirm("Êtes-vous sûr de vouloir supprimer votre photo de profil ?")) {
        // Envoi vers le PHP pour la suppression
        window.location.href = 'supprimer_photo.php';
    }
}