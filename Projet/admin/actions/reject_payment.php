<?php
require_once __DIR__ . '/../auth_guard.php';
require_once __DIR__ . '/../../student/database/database.php';
require_once __DIR__ . '/../../student/database/migrate_inscription.php';
$pdo = db();

$id = isset($_POST['id_inscription']) ? (int) $_POST['id_inscription'] : 0;
if ($id <= 0) {
    header('Location: ../payments.php');
    exit();
}

ensure_inscription_columns($pdo);

$stmt = $pdo->prepare("UPDATE inscription SET paiement_valide = -1, payment_status_note = 'Problème carte' WHERE id_inscription = ?");
$stmt->execute([$id]);
$stmt = null;

header('Location: ../payments.php');
exit();
