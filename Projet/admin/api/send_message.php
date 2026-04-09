<?php
// Used by students and professors to send support messages
header('Content-Type: application/json');
require_once __DIR__ . '/../../professeur/config/connexion.php';

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$user_id   = $data['user_id'] ?? '';
$user_type = $data['user_type'] ?? ''; // 'etudiant' or 'professeur'
$user_name = $data['user_name'] ?? '';
$subject   = $data['subject'] ?? 'Support';
$message   = trim($data['message'] ?? '');
$thread_id = $data['thread_id'] ?? null;

if (!$message || !$user_id || !$user_type) {
    echo json_encode(['success' => false, 'error' => 'Missing fields']);
    exit();
}

// Create thread if none
if (!$thread_id) {
    $stmt = $conn->prepare("INSERT INTO support_threads (user_id, user_type, user_name, subject) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $user_id, $user_type, $user_name, $subject);
    $stmt->execute();
    $thread_id = $conn->insert_id;
    $stmt->close();
}

$sender = $user_type;
$stmt = $conn->prepare("INSERT INTO support_messages (thread_id, sender, message) VALUES (?,?,?)");
$stmt->bind_param("iss", $thread_id, $sender, $message);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true, 'thread_id' => $thread_id]);
