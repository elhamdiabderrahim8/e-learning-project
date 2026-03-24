<?php

declare(strict_types=1);

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
$delete = $pdo->prepare('DELETE FROM users WHERE id = :id');
$delete->execute(['id' => $userId]);

logout_user();
redirect('../../index.php');
