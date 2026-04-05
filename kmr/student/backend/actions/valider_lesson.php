<?php
session_start();
// On remonte de 3 niveaux pour atteindre la configuration de connexion
require_once __DIR__ . '/../../../professeur/config/connexion.php';

// 1. Récupération des données sécurisées depuis l'URL
$id_lecon = isset($_GET['id_lecon']) ? intval($_GET['id_lecon']) : 0;
$id_cours = isset($_GET['id_cours']) ? intval($_GET['id_cours']) : 0;
$id_etudiant = isset($_SESSION['CIN']) ? $_SESSION['CIN'] : '';

if ($id_lecon > 0 && $id_cours > 0 && !empty($id_etudiant)) {
    
    // 2. Marquer la leçon comme terminée 
    // INSERT IGNORE évite de créer une erreur si la ligne existe déjà
    $stmt_save = $conn->prepare("INSERT IGNORE INTO suivi_lecons (id_etudiant, id_lecon, id_cours, date_validation) VALUES (?, ?, ?, NOW())");
    $stmt_save->bind_param("sii", $id_etudiant, $id_lecon, $id_cours);
    $stmt_save->execute();

    // 3. Calculer le nombre TOTAL de leçons existantes pour ce cours
    $res_total = $conn->query("SELECT COUNT(*) as total FROM lecon WHERE id_cours = $id_cours");
    $row_total = $res_total->fetch_assoc();
    $total_lecons = intval($row_total['total']);

    // 4. Calculer le nombre de leçons UNIQUES validées par l'étudiant
    // Le DISTINCT empêche de compter plusieurs fois la même leçon si l'utilisateur reclique
    $res_finies = $conn->query("SELECT COUNT(DISTINCT id_lecon) as finies FROM suivi_lecons WHERE id_etudiant = '$id_etudiant' AND id_cours = $id_cours");
    $row_finies = $res_finies->fetch_assoc();
    $lecons_finies = intval($row_finies['finies']);

    // 5. Calcul du pourcentage (Mathématique exacte)
    if ($total_lecons > 0) {
        $pourcentage = round(($lecons_finies / $total_lecons) * 100);
    } else {
        $pourcentage = 0;
    }

    // Sécurité : on ne dépasse jamais 100%
    if ($pourcentage > 100) $pourcentage = 100;

    // 6. Mise à jour de la table 'inscription' pour le tableau de bord
    $stmt_update = $conn->prepare("UPDATE inscription SET progression = ? WHERE id_etudiant = ? AND id_cours = ?");
    $stmt_update->bind_param("isi", $pourcentage, $id_etudiant, $id_cours);
    $stmt_update->execute();
}

// 7. Redirection automatique vers la liste des leçons
header("Location: ../../pages/lesson(a).php?id=" . $id_cours);
exit;
?>