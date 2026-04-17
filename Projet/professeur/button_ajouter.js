// suppression des cours
let modeSuppressionActif = false;
const btnSupprimerPrincipal = document.getElementById('btn-supprimer-main'); // Ton bouton "Supprimer" du menu
const coursesGrid = document.getElementById("courses-grid");

function activerModeSuppression(event) {
    if(event) event.stopPropagation(); // Empêche la grille de détecter le clic immédiatement
    
    let cours = document.querySelectorAll('.course-card');
    let boutonsX = document.querySelectorAll('.btn-delete-x'); // Les petites croix sur chaque cours

    if (!modeSuppressionActif) {
        // ACTIVER LE MODE
        cours.forEach(card => card.classList.add('vibrate'));
        boutonsX.forEach(btn => btn.classList.remove('hidden'));
        modeSuppressionActif = true;
    } else {
        // DÉSACTIVER LE MODE (si on clique à nouveau sur le bouton)
        desactiverMode();
    }
}

function desactiverMode() {
    let cours = document.querySelectorAll('.course-card');
    let boutonsX = document.querySelectorAll('.btn-delete-x');
    
    cours.forEach(card => card.classList.remove('vibrate'));
    boutonsX.forEach(btn => btn.classList.add('hidden'));
    modeSuppressionActif = false;
}

// Clic sur la grille pour stopper l'animation
coursesGrid.onclick = function() {
    if (modeSuppressionActif) {
        desactiverMode();
    }
};

function confirmerSuppression(id, event) {
    if(event) event.stopPropagation(); // Évite de désactiver le mode en cliquant sur la croix
    
    if (confirm("Supprimer ce cours définitivement ?")) {
        fetch('supprimer_cours.php?id=' + id)
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "success") {
                    let card = document.getElementById('cours-' + id);
                    card.style.transform = "scale(0)";
                    card.style.opacity = "0";
                    setTimeout(() => card.remove(), 300);
                }
            });
    }
}
function vers_profile(){
    window.location.href="infos.php";
}