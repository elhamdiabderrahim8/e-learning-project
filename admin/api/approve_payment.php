<?php
require_once __DIR__ . '/../../professeur/config/connexion.php';
$id = isset($_POST['id_inscription']) ? (int)$_POST['id_inscription'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'identifiant invalide']);
    exit;
}
$conn->query("ALTER TABLE inscription ADD COLUMN IF NOT EXISTS paiement_valide TINYINT(1) NOT NULL DEFAULT 0");
$conn->query("ALTER TABLE etudiant ADD COLUMN IF NOT EXISTS premium TINYINT(1) NOT NULL DEFAULT 0");
$stmt = $conn->prepare("UPDATE inscription SET paiement_valide = 1 WHERE id_inscription = ?");
if ($stmt) {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // Mise à jour de la table etudiant pour indiquer accès premium
    $stmtUser = $conn->prepare("UPDATE etudiant e JOIN inscription i ON e.CIN=i.id_etudiant SET e.premium = 1 WHERE i.id_inscription = ?");
    if ($stmtUser) {
        $stmtUser->bind_param('i', $id);
        $stmtUser->execute();
        $stmtUser->close();
    }

    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'échec update']);
}
