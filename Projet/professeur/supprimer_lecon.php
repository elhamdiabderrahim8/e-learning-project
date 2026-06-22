<?php
require_once __DIR__ . '/session_prof.php';
require_once __DIR__ . '/../student/database/database.php';
$pdo = db();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // 1. Récupérer le nom du fichier pour le supprimer du dossier
    $stmt = $pdo->prepare("SELECT nom_fichier FROM lecon WHERE id_lecon = :id");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $nom_fichier = $row['nom_fichier'];
        $chemin = "uploads/" . $nom_fichier;

        // 2. Supprimer le fichier physique s'il existe
        if (file_exists($chemin)) {
            unlink($chemin); // Cette fonction détruit le fichier
        }

        // 3. Supprimer la ligne dans la base de données
        $deleteStmt = $pdo->prepare("DELETE FROM lecon WHERE id_lecon = :id");
        $deleteStmt->execute(['id' => $id]);
    }
}

// Redirection vers la page des leçons
header("Location: lesson.php");
exit();
?>