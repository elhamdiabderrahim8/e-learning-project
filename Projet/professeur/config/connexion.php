<?php
// connexion.php — Compatibilité rétroactive
// Inclut la connexion PDO et expose $conn pour les anciens fichiers
require_once __DIR__ . '/db_prof.php';
$conn = db_prof();
?>