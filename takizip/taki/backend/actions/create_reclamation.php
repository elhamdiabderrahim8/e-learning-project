<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../reclamation.php');
}

$subject = trim((string) ($_POST['subject'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if ($subject === '' || $message === '') {
    set_flash('error', 'Sujet et description sont obligatoires.');
    redirect('../../reclamation.php');
}

$attachmentPath = null;

if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['attachment']['error'] !== UPLOAD_ERR_OK) {
        set_flash('error', 'Echec du telechargement de la piece jointe.');
        redirect('../../reclamation.php');
    }

    $maxSize = 5 * 1024 * 1024;
    if ((int) $_FILES['attachment']['size'] > $maxSize) {
        set_flash('error', 'La piece jointe ne doit pas depasser 5 Mo.');
        redirect('../../reclamation.php');
    }

    $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', (string) $_FILES['attachment']['name']);
    $targetName = time() . '_' . $safeName;
    $targetDir = __DIR__ . '/../uploads/reclamations/';
    $targetFile = $targetDir . $targetName;

    if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
        set_flash('error', 'Impossible de sauvegarder la piece jointe.');
        redirect('../../reclamation.php');
    }

    $attachmentPath = 'backend/uploads/reclamations/' . $targetName;
}

$pdo = db();
$stmt = $pdo->prepare('INSERT INTO reclamations (user_id, subject, message, attachment_path) VALUES (:user_id, :subject, :message, :attachment_path)');
$stmt->execute([
    'user_id' => user_id(),
    'subject' => $subject,
    'message' => $message,
    'attachment_path' => $attachmentPath,
]);

set_flash('success', 'Votre reclamation a ete envoyee.');
redirect('../../reclamation.php');
