// 1. On récupère les éléments
let selectElement = document.getElementsByTagName("select")[0];
let prixInput = document.getElementById("prix");

// 2. On crée une fonction qui fait le travail
function verifierPrix() {
    if (selectElement.value === 'Free') {
        prixInput.style.display="none";     
        prixInput.required= false;
    } else {
          prixInput.style.display="block";
          prixInput.required=true;
    }
}
selectElement.addEventListener("change", verifierPrix);
verifierPrix();