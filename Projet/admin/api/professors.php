<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../student/database/database.php';

try {
    $pdo = db();
    $result = $pdo->query("SELECT CIN, nom, prenom, name, type FROM professeur ORDER BY nom, prenom");
    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
} catch (Throwable $e) {
    error_log('Professors API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors du chargement des professeurs.']);
}
