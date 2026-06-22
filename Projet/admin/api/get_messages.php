<?php
// Poll for new messages (for students/professors)
header('Content-Type: application/json');
require_once __DIR__ . '/../../student/database/database.php';
$pdo = db();

$thread_id = (int)($_GET['thread_id'] ?? 0);
if (!$thread_id) { echo json_encode([]); exit(); }

$msgs = $pdo->query("SELECT sender, message, created_at FROM support_messages WHERE thread_id=$thread_id ORDER BY created_at ASC");
$out = [];
while ($m = $msgs->fetch(PDO::FETCH_ASSOC)) $out[] = $m;
echo json_encode($out);
