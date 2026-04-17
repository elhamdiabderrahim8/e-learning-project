<?php
session_start();
require_once __DIR__ . '/../kmr/student/backend/config/database.php';
require_once __DIR__ . '/../kmr/student/backend/includes/migrate_support_tables.php';

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
    $pdo->beginTransaction();

    $threadStmt = $pdo->prepare('INSERT INTO support_threads (user_id, user_type, user_name, subject) VALUES (:user_id, :user_type, :user_name, :subject)');
    $threadStmt->execute([
        'user_id' => $userId,
        'user_type' => 'professeur',
        'user_name' => $userName,
        'subject' => $subject,
    ]);

    $threadId = (int) $pdo->lastInsertId();

    $msgStmt = $pdo->prepare('INSERT INTO support_messages (thread_id, sender, message, admin_read) VALUES (:thread_id, :sender, :message, :admin_read)');
    $msgStmt->execute([
        'thread_id' => $threadId,
        'sender' => 'professeur',
        'message' => $message,
        'admin_read' => 0,
    ]);

    $pdo->commit();

    $_SESSION['prof_reclamation_flash'] = [
        'type' => 'success',
        'message' => 'Votre reclamation a ete envoyee et transmise a l admin.',
    ];
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['prof_reclamation_flash'] = [
        'type' => 'error',
        'message' => 'Impossible d envoyer la reclamation. Veuillez reessayer.',
    ];
}

header('Location: reclamation.php');
exit();
