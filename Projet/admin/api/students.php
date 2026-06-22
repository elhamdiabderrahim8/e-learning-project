<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../student/database/database.php';
$pdo = db();
$result = $pdo->query("SELECT CIN, nom, prenom, email, date_inscription FROM etudiant ORDER BY date_inscription DESC");
$rows = [];
if ($result) {
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $row;
    }
}
echo json_encode($rows);
