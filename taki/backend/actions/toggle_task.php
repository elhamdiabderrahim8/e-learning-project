<?php

require_once __DIR__ . '/../includes/bootstrap.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/tache_a_fair.php');
}

$taskId = (int) ($_POST['task_id'] ?? 0);

if ($taskId <= 0) {
    set_flash('error', 'Tache invalide.');
    redirect('../../pages/tache_a_fair.php');
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
    redirect('../../pages/tache_a_fair.php');
}

$isCompletedNow = ((int) $task['is_completed']) === 1;
$nextState = $isCompletedNow ? 0 : 1;
$nextStatus = $nextState === 1 ? 'terminee' : 'en_cours';

$update = $pdo->prepare('UPDATE tasks SET is_completed = :is_completed, status = :status WHERE id = :id AND user_id = :user_id');
$update->execute([
    'is_completed' => $nextState,
    'status' => $nextStatus,
    'id' => $taskId,
    'user_id' => user_id(),
]);

set_flash('success', $nextState === 1 ? 'Tache marquee comme terminee.' : 'Tache remise en cours.');
redirect('../../pages/tache_a_fair.php');
