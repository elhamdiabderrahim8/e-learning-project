<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../student/database/database.php';

try {
    $pdo = db();
    $result = $pdo->query("SELECT CIN, nom, prenom, email, date_inscription FROM etudiant ORDER BY date_inscription DESC");
    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
} catch (Throwable $e) {
    error_log('Students API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors du chargement des etudiants.']);
}
