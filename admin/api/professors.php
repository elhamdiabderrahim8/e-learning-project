<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../professeur/config/connexion.php';
$result = $conn->query("SELECT CIN, nom, prenom, name, type FROM professeur ORDER BY nom, prenom");
$rows = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}
echo json_encode($rows);
