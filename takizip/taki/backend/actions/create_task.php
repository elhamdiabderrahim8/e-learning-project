<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/tache_a_fair.php');
}

$title = trim((string) ($_POST['title'] ?? ''));

if ($title === '') {
    set_flash('error', 'Le titre de la tache est obligatoire.');
    redirect('../../pages/tache_a_fair.php');
}

$pdo = db();
$stmt = $pdo->prepare('INSERT INTO tasks (user_id, title) VALUES (:user_id, :title)');
$stmt->execute([
    'user_id' => user_id(),
    'title' => $title,
]);

set_flash('success', 'Nouvelle tache ajoutee.');
redirect('../../pages/tache_a_fair.php');
