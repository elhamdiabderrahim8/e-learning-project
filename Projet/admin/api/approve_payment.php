<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../student/database/database.php';
require_once __DIR__ . '/../../student/database/migrate_inscription.php';

try {
    $pdo = db();
    $id = isset($_POST['id_inscription']) ? (int)$_POST['id_inscription'] : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'identifiant invalide']);
        exit;
    }
    ensure_inscription_columns($pdo);

    $stmt = $pdo->prepare("UPDATE inscription SET paiement_valide = 1 WHERE id_inscription = ?");
    $stmt->execute([$id]);

    $stmtUser = $pdo->prepare("UPDATE etudiant e JOIN inscription i ON e.CIN=i.id_etudiant SET e.premium = 1 WHERE i.id_inscription = ?");
    $stmtUser->execute([$id]);

    echo json_encode(['ok' => true]);
} catch (Throwable $e) {
    error_log('Approve payment API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de l approbation du paiement.']);
}
