<?php
require_once __DIR__ . '/../auth_guard.php';
require_once __DIR__ . '/../../professeur/config/connexion.php';

$id = isset($_POST['id_inscription']) ? (int)$_POST['id_inscription'] : 0;
if ($id <= 0) {
    header('Location: ../index.php');
    exit();
}

// Ensure column exists when approval is attempted
$conn->query("ALTER TABLE inscription ADD COLUMN IF NOT EXISTS paiement_valide TINYINT(1) NOT NULL DEFAULT 0");

$stmt = $conn->prepare("UPDATE inscription SET paiement_valide = 1 WHERE id_inscription = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

header('Location: ../payments.php');
exit();
