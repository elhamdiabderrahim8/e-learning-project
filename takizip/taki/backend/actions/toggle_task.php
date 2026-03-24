<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../tache_a_fair.php');
}

$taskId = (int) ($_POST['task_id'] ?? 0);

if ($taskId <= 0) {
    set_flash('error', 'Tache invalide.');
    redirect('../../tache_a_fair.php');
}

$pdo = db();

$check = $pdo->prepare('SELECT id, is_completed FROM tasks WHERE id = :id AND user_id = :user_id LIMIT 1');
$check->execute([
    'id' => $taskId,
    'user_id' => user_id(),
]);
$task = $check->fetch();

if (!$task) {
    set_flash('error', 'Tache introuvable.');
    redirect('../../tache_a_fair.php');
}

$nextState = ((int) $task['is_completed']) === 1 ? 0 : 1;
$update = $pdo->prepare('UPDATE tasks SET is_completed = :is_completed WHERE id = :id');
$update->execute([
    'is_completed' => $nextState,
    'id' => $taskId,
]);

set_flash('success', $nextState === 1 ? 'Tache marquee comme terminee.' : 'Tache remise en cours.');
redirect('../../tache_a_fair.php');
