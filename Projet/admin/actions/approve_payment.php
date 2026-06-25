<?php
require_once __DIR__ . '/../auth_guard.php';
require_once __DIR__ . '/../../student/database/database.php';
require_once __DIR__ . '/../../student/database/migrate_inscription.php';
try {
    $pdo = db();

    $id = isset($_POST['id_inscription']) ? (int)$_POST['id_inscription'] : 0;
    if ($id <= 0) {
        header('Location: ../index.php');
        exit();
    }

    ensure_inscription_columns($pdo);

    $stmt = $pdo->prepare("UPDATE inscription SET paiement_valide = 1, payment_status_note = NULL WHERE id_inscription = ?");
    $stmt->execute([$id]);
} catch (Throwable $e) {
    error_log('Admin approve payment error: ' . $e->getMessage());
}

header('Location: ../payments.php');
exit();
