<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_auth();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('../../pages/support.php');
}

$message = trim((string) ($_POST['message'] ?? ''));
if ($message === '') {
    set_flash('error', 'Le message est obligatoire.');
    redirect('../../pages/support.php');
}

if (mb_strlen($message) > 4000) {
    set_flash('error', 'Message trop long (max 4000 caracteres).');
    redirect('../../pages/support.php');
}

$pdo = db();

try {
    $stmt = $pdo->prepare('INSERT INTO support_messages (student_cin, sender, message) VALUES (:cin, :sender, :message)');
    $stmt->execute([
        'cin' => (int) user_id(),
        'sender' => 'student',
        'message' => $message,
    ]);

    set_flash('success', 'Message envoye.');
} catch (Throwable $e) {
    error_log('Support message error: ' . $e->getMessage());
    set_flash('error', 'Impossible d\'envoyer le message.');
}
redirect('../../pages/support.php');

