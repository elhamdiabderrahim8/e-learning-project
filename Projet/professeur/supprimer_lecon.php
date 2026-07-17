<?php
require_once __DIR__ . '/session_prof.php';
require_once __DIR__ . '/../student/database/database.php';
$pdo = db();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $stmt = $pdo->prepare("SELECT nom_fichier FROM lecon WHERE id_lecon = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $nom_fichier = $row['nom_fichier'];
            $chemin = "uploads/" . $nom_fichier;

            if (file_exists($chemin)) {
                if (!unlink($chemin)) {
                    error_log('Failed to delete lesson file: ' . $chemin);
                }
            }

            $deleteStmt = $pdo->prepare("DELETE FROM lecon WHERE id_lecon = :id");
            $deleteStmt->execute(['id' => $id]);
        }
    } catch (Throwable $e) {
        error_log('Delete lesson error: ' . $e->getMessage());
    }
}

// Redirection vers la page des leçons
header("Location: lesson.php");
exit();
?>