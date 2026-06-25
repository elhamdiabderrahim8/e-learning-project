<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../student/database/database.php';

try {
    $pdo = db();

    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $user_id   = $data['user_id'] ?? '';
    $user_type = $data['user_type'] ?? '';
    $user_name = $data['user_name'] ?? '';
    $subject   = $data['subject'] ?? 'Support';
    $message   = trim($data['message'] ?? '');
    $thread_id = $data['thread_id'] ?? null;

    if (!$message || !$user_id || !$user_type) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing fields']);
        exit();
    }

    if (!$thread_id) {
        $stmt = $pdo->prepare("INSERT INTO support_threads (user_id, user_type, user_name, subject) VALUES (?,?,?,?)");
        $stmt->execute([$user_id, $user_type, $user_name, $subject]);
        $thread_id = $pdo->lastInsertId();
    }

    $sender = $user_type;
    $stmt = $pdo->prepare("INSERT INTO support_messages (thread_id, sender, message) VALUES (?,?,?)");
    $stmt->execute([$thread_id, $sender, $message]);

    echo json_encode(['success' => true, 'thread_id' => $thread_id]);
} catch (Throwable $e) {
    error_log('Send message API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur lors de l envoi du message.']);
}
