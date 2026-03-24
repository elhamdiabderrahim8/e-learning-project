<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/reclamation.php');
}

$subject = trim((string) ($_POST['subject'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if ($subject === '' || $message === '') {
    set_flash('error', 'Sujet et description sont obligatoires.');
    redirect('../../pages/reclamation.php');
}

$uploadedPaths = [];
$maxSize = 5 * 1024 * 1024;

if (isset($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
    $targetDir = __DIR__ . '/../uploads/reclamations/';
    if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
        set_flash('error', 'Impossible de preparer le dossier de pieces jointes.');
        redirect('../../pages/reclamation.php');
    }

    $fileCount = count($_FILES['attachments']['name']);
    for ($i = 0; $i < $fileCount; $i++) {
        $errorCode = (int) ($_FILES['attachments']['error'][$i] ?? UPLOAD_ERR_NO_FILE);
        if ($errorCode === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if ($errorCode !== UPLOAD_ERR_OK) {
            set_flash('error', 'Echec du telechargement de la piece jointe.');
            redirect('../../pages/reclamation.php');
        }

        $fileSize = (int) ($_FILES['attachments']['size'][$i] ?? 0);
        if ($fileSize > $maxSize) {
            set_flash('error', 'Chaque piece jointe ne doit pas depasser 5 Mo.');
            redirect('../../pages/reclamation.php');
        }

        $originalName = (string) ($_FILES['attachments']['name'][$i] ?? 'fichier');
        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $targetName = time() . '_' . $i . '_' . $safeName;
        $targetFile = $targetDir . $targetName;

        $tmpName = (string) ($_FILES['attachments']['tmp_name'][$i] ?? '');
        if ($tmpName === '' || !move_uploaded_file($tmpName, $targetFile)) {
            set_flash('error', 'Impossible de sauvegarder une piece jointe.');
            redirect('../../pages/reclamation.php');
        }

        $uploadedPaths[] = 'backend/uploads/reclamations/' . $targetName;
    }
}

$attachmentPath = $uploadedPaths[0] ?? null;
$attachmentPaths = $uploadedPaths ? json_encode($uploadedPaths, JSON_UNESCAPED_SLASHES) : null;

$pdo = db();

$stmt = $pdo->prepare('INSERT INTO reclamations (user_id, subject, message, attachment_path, attachment_paths) VALUES (:user_id, :subject, :message, :attachment_path, :attachment_paths)');
$stmt->execute([
    'user_id' => user_id(),
    'subject' => $subject,
    'message' => $message,
    'attachment_path' => $attachmentPath,
    'attachment_paths' => $attachmentPaths,
]);

set_flash('success', 'Votre reclamation a ete envoyee.');
redirect('../../pages/reclamation.php');
