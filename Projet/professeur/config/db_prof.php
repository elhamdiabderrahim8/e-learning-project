<?php
// db_prof.php - Connexion PDO centralisée pour l'espace professeur
// Compatible Railway (variables d'environnement) et XAMPP (localhost)

function db_prof(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host   = getenv('DB_HOST') ?: 'localhost';
    $port   = getenv('DB_PORT') ?: '3306';
    $dbname = getenv('DB_NAME') ?: 'elearning';
    $user   = getenv('DB_USER') ?: 'root';
    $pass   = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    return $pdo;
}
?>
