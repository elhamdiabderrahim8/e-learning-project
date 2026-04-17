// suppression des cours
let modeSuppressionActif = false;
const btnSupprimerPrincipal = document.getElementById('btn-supprimer-main');
const coursesGrid = document.getElementById('courses-grid');

function activerModeSuppression(event) {
    if (event) {
        event.stopPropagation();
    }

    let cours = document.querySelectorAll('.course-card');
    let boutonsX = document.querySelectorAll('.btn-delete-x');

    if (!modeSuppressionActif) {
        cours.forEach(card => card.classList.add('vibrate'));
        boutonsX.forEach(btn => btn.classList.remove('hidden'));
        modeSuppressionActif = true;
    } else {
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

if (coursesGrid) {
    coursesGrid.onclick = function () {
        if (modeSuppressionActif) {
            desactiverMode();
        }
    };
}

function confirmerSuppression(id, event) {
    if (event) {
        event.stopPropagation();
    }

    if (!confirm('Supprimer ce cours definitivement ?')) {
        return;
    }

    fetch('supprimer_cours.php?id=' + encodeURIComponent(id))
        .then(response => response.json())
        .then(data => {
            if (data && data.success) {
                let card = document.getElementById('cours-' + id);
                if (card) {
                    card.style.transform = 'scale(0)';
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 300);
                }
                return;
            }

            alert((data && data.message) ? data.message : 'La suppression du cours a echoue.');
        })
        .catch(() => {
            alert('Impossible de supprimer le cours pour le moment.');
        });
}

function vers_profile() {
    window.location.href = 'infos.php';
}
