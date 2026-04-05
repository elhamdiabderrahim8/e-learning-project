<?php

declare(strict_types=1);

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'elearning';
$port = 3306;

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die('Erreur de connexion a la base de donnees: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
