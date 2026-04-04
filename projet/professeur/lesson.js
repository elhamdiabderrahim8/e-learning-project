const modal = document.getElementById("form-ajout");
    const btn = document.getElementById("ajout");
    const span = document.getElementsByClassName("close-btn")[0];

    // Ouvrir la modale au clic sur le bouton
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // Fermer la modale au clic sur le (X)
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Fermer la modale si on clique n'importe où en dehors de la boîte
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }