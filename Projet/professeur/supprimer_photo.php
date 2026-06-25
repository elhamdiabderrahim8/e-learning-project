<?php
require_once __DIR__ . '/session_prof.php';

// 1. Connexion à la base de données avec PDO
require_once __DIR__ . '/../student/database/database.php';
$pdo = db();

// 2. Vérification de la session (CIN d'après ta table)
if (!isset($_SESSION['CIN'])) {
    header('Location: login.php');
    exit();
}

$cin = $_SESSION['CIN'];

// 3. Préparation de la requête pour vider les colonnes data, name et type
// On utilise une requête préparée pour la sécurité (contre les injections SQL)
try {
    $sql = "UPDATE professeur SET data = NULL, name = NULL, type = NULL WHERE CIN = :cin";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['cin' => $cin]);
    header('Location: infos.php?status=success');
} catch (Throwable $e) {
    error_log('Delete professor photo error: ' . $e->getMessage());
    header('Location: infos.php?status=error');
}
exit();