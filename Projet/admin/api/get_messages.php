<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../student/database/database.php';

try {
    $pdo = db();

    $thread_id = (int)($_GET['thread_id'] ?? 0);
    if (!$thread_id) { echo json_encode([]); exit(); }

    $stmt = $pdo->prepare('SELECT sender, message, created_at FROM support_messages WHERE thread_id = :thread_id ORDER BY created_at ASC');
    $stmt->execute(['thread_id' => $thread_id]);
    $out = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($out);
} catch (Throwable $e) {
    error_log('Get messages API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors du chargement des messages.']);
}
