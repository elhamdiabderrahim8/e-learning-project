<?php
// Page de diagnostic temporaire - À supprimer après debug !
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== DIAGNOSTIC ENJAH ===\n\n";

// 1. Variables d'environnement
echo "--- Variables DB ---\n";
echo "DB_HOST : " . (getenv('DB_HOST') ?: 'NON DÉFINI ❌') . "\n";
echo "DB_PORT : " . (getenv('DB_PORT') ?: 'NON DÉFINI ❌') . "\n";
echo "DB_NAME : " . (getenv('DB_NAME') ?: 'NON DÉFINI ❌') . "\n";
echo "DB_USER : " . (getenv('DB_USER') ?: 'NON DÉFINI ❌') . "\n";
echo "DB_PASS : " . (getenv('DB_PASS') !== false ? 'DÉFINI ✅' : 'NON DÉFINI ❌') . "\n\n";

// 2. Test de connexion DB
echo "--- Test connexion MySQL ---\n";
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$name = getenv('DB_NAME') ?: 'elearning';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connexion MySQL : RÉUSSIE ✅\n";
    
    // Lister les tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables trouvées : " . count($tables) . "\n";
    foreach ($tables as $t) {
        echo "  - $t\n";
    }
} catch (PDOException $e) {
    echo "Connexion MySQL : ÉCHEC ❌\n";
    echo "Erreur : " . $e->getMessage() . "\n";
}

echo "\n=== FIN DU DIAGNOSTIC ===\n";
