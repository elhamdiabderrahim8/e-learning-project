<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_auth();

$pdo = db();
$cin = $_SESSION['CIN'] ?? $_SESSION['user_id'];

try {
    // On met à NULL les colonnes data et type pour supprimer l'image
    $stmt = $pdo->prepare("UPDATE etudiant SET data = NULL, type = NULL WHERE CIN = :cin");
    $stmt->execute(['cin' => $cin]);

    set_flash('success', 'Votre photo de profil a été supprimée.');
} catch (PDOException $e) {
    error_log('delete_photo error: ' . $e->getMessage());
    set_flash('error', 'Erreur lors de la suppression de la photo.');
}

redirect('../../pages/profil.php');