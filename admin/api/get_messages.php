<?php
// Poll for new messages (for students/professors)
header('Content-Type: application/json');
require_once __DIR__ . '/../../professeur/config/connexion.php';

$thread_id = (int)($_GET['thread_id'] ?? 0);
if (!$thread_id) { echo json_encode([]); exit(); }

$msgs = $conn->query("SELECT sender, message, created_at FROM support_messages WHERE thread_id=$thread_id ORDER BY created_at ASC");
$out = [];
while ($m = $msgs->fetch_assoc()) $out[] = $m;
echo json_encode($out);
