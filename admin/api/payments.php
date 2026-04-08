<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../professeur/config/connexion.php';
// Optionnel : assure la colonne existe
$conn->query("ALTER TABLE inscription ADD COLUMN IF NOT EXISTS paiement_valide TINYINT(1) NOT NULL DEFAULT 0");
$conn->query("ALTER TABLE etudiant ADD COLUMN IF NOT EXISTS premium TINYINT(1) NOT NULL DEFAULT 0");
$sql = "SELECT i.id_inscription, i.id_etudiant, i.id_cours, i.date_achat, i.methode_paiement, i.paiement_valide,
               e.nom AS nom_etudiant, e.prenom AS prenom_etudiant,
               c.nom_cours, p.nom AS nom_prof, p.prenom AS prenom_prof
        FROM inscription i
        LEFT JOIN etudiant e ON e.CIN=i.id_etudiant
        LEFT JOIN cours c ON c.id=i.id_cours
        LEFT JOIN professeur p ON p.CIN=c.id_professeur
        ORDER BY i.date_achat DESC";
$result = $conn->query($sql);
$rows = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}
echo json_encode($rows);
