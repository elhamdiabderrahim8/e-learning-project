<?php
require_once __DIR__ . '/../auth_guard.php';
header('Content-Type: application/json');
require_once __DIR__ . '/../../student/database/database.php';
$pdo = db();
$result = $pdo->query("SELECT CIN, nom, prenom, name, type FROM professeur ORDER BY nom, prenom");
$rows = [];
if ($result) {
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $row;
    }
}
echo json_encode($rows);
