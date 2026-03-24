<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/tache_a_fair.php');
}

$taskId = (int) ($_POST['task_id'] ?? 0);
$deleteCompleted = ((string) ($_POST['delete_completed'] ?? '0')) === '1';

if ($taskId <= 0) {
    set_flash('error', 'Tache invalide.');
    redirect('../../pages/tache_a_fair.php');
}

$allowedStatuses = ['a_faire', 'en_cours', 'terminee'];

$pdo = db();

$check = $pdo->prepare('SELECT id, status, is_completed FROM tasks WHERE id = :id AND user_id = :user_id LIMIT 1');
$check->execute([
    'id' => $taskId,
    'user_id' => user_id(),
]);

$task = $check->fetch();
if (!$task) {
    set_flash('error', 'Tache introuvable.');
    redirect('../../pages/tache_a_fair.php');
}

$currentStatus = (string) ($task['status'] ?? '');
if (!in_array($currentStatus, $allowedStatuses, true)) {
    $currentStatus = ((int) ($task['is_completed'] ?? 0)) === 1 ? 'terminee' : 'a_faire';
}

if ($currentStatus === 'terminee') {
    if ($deleteCompleted) {
        $delete = $pdo->prepare('DELETE FROM tasks WHERE id = :id AND user_id = :user_id');
        $delete->execute([
            'id' => $taskId,
            'user_id' => user_id(),
        ]);
        set_flash('success', 'Tache terminee supprimee.');
        redirect('../../pages/tache_a_fair.php');
    }

    set_flash('success', 'Tache deja terminee.');
    redirect('../../pages/tache_a_fair.php');
}

$nextStatus = $currentStatus === 'a_faire' ? 'en_cours' : 'terminee';
$isCompletedFlag = $nextStatus === 'terminee' ? 1 : 0;

$update = $pdo->prepare('UPDATE tasks SET status = :status, is_completed = :is_completed WHERE id = :id AND user_id = :user_id');
$update->bindValue(':status', $nextStatus, PDO::PARAM_STR);
$update->bindValue(':is_completed', $isCompletedFlag, PDO::PARAM_INT);
$update->bindValue(':id', $taskId, PDO::PARAM_INT);
$update->bindValue(':user_id', user_id(), PDO::PARAM_INT);
$update->execute();

redirect('../../pages/tache_a_fair.php');
