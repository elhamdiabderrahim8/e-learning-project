<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_auth();

$pdo = db();

// 1. Récupération des données sécurisées depuis l'URL
$id_lecon = isset($_GET['id_lecon']) ? (int)$_GET['id_lecon'] : 0;
$id_cours = isset($_GET['id_cours']) ? (int)$_GET['id_cours'] : 0;
$id_etudiant = isset($_SESSION['CIN']) ? $_SESSION['CIN'] : '';

if ($id_lecon > 0 && $id_cours > 0 && !empty($id_etudiant)) {
    
    // 2. Marquer la leçon comme terminée 
    // INSERT IGNORE évite de créer une erreur si la ligne existe déjà
    $stmt_save = $pdo->prepare("INSERT IGNORE INTO suivi_lecons (id_etudiant, id_lecon, id_cours, date_validation) VALUES (?, ?, ?, NOW())");
    $stmt_save->execute([$id_etudiant, $id_lecon, $id_cours]);

    // 3. Calculer le nombre TOTAL de leçons existantes pour ce cours
    $stmt_total = $pdo->prepare("SELECT COUNT(*) as total FROM lecon WHERE id_cours = ?");
    $stmt_total->execute([$id_cours]);
    $row_total = $stmt_total->fetch();
    $total_lecons = (int)$row_total['total'];

    // 4. Calculer le nombre de leçons UNIQUES validées par l'étudiant
    // Le DISTINCT empêche de compter plusieurs fois la même leçon si l'utilisateur reclique
    $stmt_finies = $pdo->prepare("SELECT COUNT(DISTINCT id_lecon) as finies FROM suivi_lecons WHERE id_etudiant = ? AND id_cours = ?");
    $stmt_finies->execute([$id_etudiant, $id_cours]);
    $row_finies = $stmt_finies->fetch();
    $lecons_finies = (int)$row_finies['finies'];

    // 5. Calcul du pourcentage (Mathématique exacte)
    if ($total_lecons > 0) {
        $pourcentage = (int)round(($lecons_finies / $total_lecons) * 100);
    } else {
        $pourcentage = 0;
    }

    // Sécurité : on ne dépasse jamais 100%
    if ($pourcentage > 100) $pourcentage = 100;

    // 6. Mise à jour de la table 'inscription' pour le tableau de bord
    $stmt_update = $pdo->prepare("UPDATE inscription SET progression = ? WHERE id_etudiant = ? AND id_cours = ?");
    $stmt_update->execute([$pourcentage, $id_etudiant, $id_cours]);
}

// 7. Redirection automatique vers la liste des leçons
header("Location: ../../pages/lesson.php?id=" . $id_cours);
exit;
?>