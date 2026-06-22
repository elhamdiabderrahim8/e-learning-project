<?php
require_once __DIR__ . '/session_prof.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registre.php');
    exit();
}

$cin             = trim((string) ($_POST['CIN'] ?? ''));
$prenom          = trim((string) ($_POST['prenom'] ?? ''));
$nom             = trim((string) ($_POST['nom'] ?? ''));
$password        = (string) ($_POST['password'] ?? '');
$confirmPassword = (string) ($_POST['confirm_password'] ?? '');

function redirect_with_message(string $message, bool $success = false): void
{
    $query = http_build_query([
        'msg'     => $message,
        'success' => $success ? '1' : '0',
    ]);
    header('Location: registre.php?' . $query);
    exit();
}

if ($cin === '' || $prenom === '' || $nom === '' || $password === '' || $confirmPassword === '') {
    redirect_with_message('Tous les champs sont obligatoires.');
}

if (!ctype_digit($cin)) {
    redirect_with_message('Le CIN doit contenir uniquement des chiffres.');
}

if (strlen($password) < 8) {
    redirect_with_message('Le mot de passe doit contenir au moins 8 caracteres.');
}

if ($password !== $confirmPassword) {
    redirect_with_message('Les mots de passe ne correspondent pas.');
}

try {
    $host   = getenv('DB_HOST') ?: 'localhost';
    $port   = getenv('DB_PORT') ?: '3306';
    $dbname = getenv('DB_NAME') ?: 'elearning';
    $user   = getenv('DB_USER') ?: 'root';
    $pass   = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );

    $check = $pdo->prepare('SELECT CIN FROM professeur WHERE CIN = :cin LIMIT 1');
    $check->execute(['cin' => $cin]);
    if ($check->fetch()) {
        redirect_with_message('Ce CIN est deja utilise.');
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $insert = $pdo->prepare('INSERT INTO professeur (CIN, nom, prenom, password) VALUES (:cin, :nom, :prenom, :password)');
    $insert->execute([
        'cin'      => (int) $cin,
        'nom'      => $nom,
        'prenom'   => $prenom,
        'password' => $passwordHash,
    ]);

    session_regenerate_id(true);
    $_SESSION['CIN']    = (int) $cin;
    $_SESSION['prenom'] = $prenom;
    $_SESSION['nom']    = $nom;

    header('Location: offres.php');
    exit();

} catch (Exception $e) {
    redirect_with_message($e->getMessage());
}
?>
