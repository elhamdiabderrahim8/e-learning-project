<?php
require_once __DIR__ . '/session_prof.php';
require_once __DIR__ . '/../shared/database.php';
require_once __DIR__ . '/../shared/validation.php';

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

$requiredError = validate_required_fields([$cin, $prenom, $nom, $password, $confirmPassword]);
if ($requiredError !== null) {
    redirect_with_message($requiredError);
}

if (!ctype_digit($cin)) {
    redirect_with_message('Le CIN doit contenir uniquement des chiffres.');
}

$passwordError = validate_password($password, $confirmPassword);
if ($passwordError !== null) {
    redirect_with_message($passwordError);
}

try {
    $pdo = db();

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
