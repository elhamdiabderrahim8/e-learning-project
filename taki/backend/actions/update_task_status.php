<?php

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
if ($currentStatus !== 'a_faire' && $currentStatus !== 'en_cours' && $currentStatus !== 'terminee') {
    if ((int) ($task['is_completed'] ?? 0) === 1) {
        $currentStatus = 'terminee';
    } else {
        $currentStatus = 'a_faire';
    }
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

$nextStatus = 'terminee';
if ($currentStatus === 'a_faire') {
    $nextStatus = 'en_cours';
}

$isCompletedFlag = $nextStatus === 'terminee' ? 1 : 0;

$update = $pdo->prepare('UPDATE tasks SET status = :status, is_completed = :is_completed WHERE id = :id AND user_id = :user_id');
$update->bindValue(':status', $nextStatus, PDO::PARAM_STR);
$update->bindValue(':is_completed', $isCompletedFlag, PDO::PARAM_INT);
$update->bindValue(':id', $taskId, PDO::PARAM_INT);
$update->bindValue(':user_id', user_id(), PDO::PARAM_INT);
$update->execute();

redirect('../../pages/tache_a_fair.php');
