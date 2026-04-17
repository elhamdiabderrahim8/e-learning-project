<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'elearning');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // 1. Récupérer le nom du fichier pour le supprimer du dossier
    $res = $conn->query("SELECT nom_fichier FROM lecon WHERE id_lecon = $id");
    if ($row = $res->fetch_assoc()) {
        $nom_fichier = $row['nom_fichier'];
        $chemin = "uploads/" . $nom_fichier;

        // 2. Supprimer le fichier physique s'il existe
        if (file_exists($chemin)) {
            unlink($chemin); // Cette fonction détruit le fichier
        }

        // 3. Supprimer la ligne dans la base de données
        $conn->query("DELETE FROM lecon WHERE id_lecon = $id");
    }
}

// Redirection vers la page des leçons
header("Location: lesson.php");
exit();
?>