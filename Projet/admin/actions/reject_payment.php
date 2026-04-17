<?php
require_once __DIR__ . '/../auth_guard.php';
require_once __DIR__ . '/../../professeur/config/connexion.php';

$id = isset($_POST['id_inscription']) ? (int) $_POST['id_inscription'] : 0;
if ($id <= 0) {
    header('Location: ../payments.php');
    exit();
}

$conn->query("ALTER TABLE inscription ADD COLUMN IF NOT EXISTS paiement_valide TINYINT(1) NOT NULL DEFAULT 0");
$conn->query("ALTER TABLE inscription ADD COLUMN IF NOT EXISTS payment_status_note VARCHAR(255) NULL");

$stmt = $conn->prepare("UPDATE inscription SET paiement_valide = -1, payment_status_note = 'Problème carte' WHERE id_inscription = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

header('Location: ../payments.php');
exit();
