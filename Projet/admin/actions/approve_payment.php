<?php
require_once __DIR__ . '/../auth_guard.php';
require_once __DIR__ . '/../../student/database/database.php';
$pdo = db();

$id = isset($_POST['id_inscription']) ? (int)$_POST['id_inscription'] : 0;
if ($id <= 0) {
    header('Location: ../index.php');
    exit();
}

// Ensure column exists when approval is attempted
$pdo->query("ALTER TABLE inscription ADD COLUMN IF NOT EXISTS paiement_valide TINYINT(1) NOT NULL DEFAULT 0");
$pdo->query("ALTER TABLE inscription ADD COLUMN IF NOT EXISTS payment_status_note VARCHAR(255) NULL");

$stmt = $pdo->prepare("UPDATE inscription SET paiement_valide = 1, payment_status_note = NULL WHERE id_inscription = ?");
$stmt->execute([$id]);
$stmt = null;

header('Location: ../payments.php');
exit();
