<?php

require_once __DIR__ . '/../includes/bootstrap.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/tache_a_fair.php');
}

$title = trim((string) ($_POST['title'] ?? ''));
$priority = (string) ($_POST['priority'] ?? 'medium');
$dueDateInput = trim((string) ($_POST['due_date'] ?? ''));
$status = (string) ($_POST['status'] ?? 'a_faire');

if ($title === '') {
    set_flash('error', 'Le titre de la tache est obligatoire.');
    redirect('../../pages/tache_a_fair.php');
}

$allowedPriorities = ['high', 'medium', 'low'];
if (!in_array($priority, $allowedPriorities, true)) {
    $priority = 'medium';
}

$allowedStatuses = ['a_faire', 'en_cours', 'terminee'];
if (!in_array($status, $allowedStatuses, true)) {
    $status = 'a_faire';
}

$isCompletedFlag = $status === 'terminee' ? 1 : 0;

$dueDate = null;
if ($dueDateInput !== '') {
    $date = DateTime::createFromFormat('Y-m-d', $dueDateInput);
    $isValidDate = $date && $date->format('Y-m-d') === $dueDateInput;
    if (!$isValidDate) {
        set_flash('error', 'La date limite est invalide.');
        redirect('../../pages/tache_a_fair.php');
    }
    $dueDate = $dueDateInput;
}

$pdo = db();

$stmt = $pdo->prepare('INSERT INTO tasks (user_id, title, due_date, priority, status, is_completed) VALUES (:user_id, :title, :due_date, :priority, :status, :is_completed)');
$stmt->bindValue(':user_id', user_id(), PDO::PARAM_INT);
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':due_date', $dueDate, $dueDate === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
$stmt->bindValue(':priority', $priority, PDO::PARAM_STR);
$stmt->bindValue(':status', $status, PDO::PARAM_STR);
$stmt->bindValue(':is_completed', $isCompletedFlag, PDO::PARAM_INT);
$stmt->execute();

set_flash('success', 'Nouvelle tache ajoutee.');
redirect('../../pages/tache_a_fair.php');
