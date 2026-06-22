// On récupère l'élément vidéo
const maVideo = document.querySelector('video');

if (maVideo) {
    // Quand la vidéo se termine...
    maVideo.onended = function() {
        console.log("Vidéo terminée, enregistrement automatique...");
        validerAutomatiquement();
    };
}

function validerAutomatiquement() {
    const idLecon = <?php echo $id_lecon; ?>; // ID récupéré par PHP
    const idCours = <?php echo $id_cours; ?>;

    fetch('valider_lecon.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_lecon=${idLecon}&id_cours=${idCours}`
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // On peut afficher un petit message discret
            console.log("Progression sauvegardée !");
        }
    });
}