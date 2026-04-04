<?php

require_once __DIR__ . '/../includes/bootstrap.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/profil.php');
}

$userId = user_id();
if ($userId === null) {
    redirect('../../index.php');
}

$pdo = db();

try {
    $pdo->prepare('DELETE FROM tasks WHERE user_id = :id')->execute(['id' => $userId]);
    $pdo->prepare('DELETE FROM reclamations WHERE user_id = :id')->execute(['id' => $userId]);
    $pdo->prepare('DELETE FROM enrollments WHERE user_id = :id')->execute(['id' => $userId]);
    $pdo->prepare('DELETE FROM certificates WHERE user_id = :id')->execute(['id' => $userId]);
    $pdo->prepare('DELETE FROM support_messages WHERE student_cin = :id')->execute(['id' => $userId]);
} catch (Throwable $e) {
    // Ignore cleanup errors for optional tables during prototype stage.
}

$delete = $pdo->prepare('DELETE FROM etudiant WHERE CIN = :id');
$delete->execute(['id' => $userId]);

logout_user();
redirect('../../index.php');
