<?php
require_once __DIR__ . '/session_prof.php';
require_once __DIR__ . '/../shared/database.php';
require_once __DIR__ . '/../shared/support.php';
require_once __DIR__ . '/../student/backend/includes/migrate_support_tables.php';

if (!isset($_SESSION['CIN'])) {
    header('Location: login.html');
    exit();
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: reclamation.php');
    exit();
}

$subject = trim((string) ($_POST['subject'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if ($subject === '' || $message === '') {
    $_SESSION['prof_reclamation_flash'] = [
        'type' => 'error',
        'message' => 'Sujet et description sont obligatoires.',
    ];
    header('Location: reclamation.php');
    exit();
}

$pdo = db();
$userId = (string) $_SESSION['CIN'];
$userName = trim((string) (($_SESSION['nom'] ?? '') . ' ' . ($_SESSION['prenom'] ?? '')));

if ($userName === '') {
    try {
        $stmt = $pdo->prepare('SELECT nom, prenom FROM professeur WHERE CIN = :cin LIMIT 1');
        $stmt->execute(['cin' => $userId]);
        $prof = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $userName = trim((string) (($prof['nom'] ?? '') . ' ' . ($prof['prenom'] ?? '')));
    } catch (Throwable $e) {
        $userName = '';
    }
}

if ($userName === '') {
    $userName = 'Professeur ' . $userId;
}

try {
    migrate_support_tables();
    create_support_thread($userId, 'professeur', $userName, $subject, $message);

    $_SESSION['prof_reclamation_flash'] = [
        'type' => 'success',
        'message' => 'Votre reclamation a ete envoyee et transmise a l admin.',
    ];
} catch (Throwable $e) {
    $_SESSION['prof_reclamation_flash'] = [
        'type' => 'error',
        'message' => 'Impossible d envoyer la reclamation. Veuillez reessayer.',
    ];
}

header('Location: reclamation.php');
exit();
