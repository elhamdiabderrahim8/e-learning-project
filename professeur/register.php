<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registre.php');
    exit();
}

$cin = trim((string) ($_POST['CIN'] ?? ''));
$prenom = trim((string) ($_POST['prenom'] ?? ''));
$nom = trim((string) ($_POST['nom'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$confirmPassword = (string) ($_POST['confirm_password'] ?? '');

function redirect_with_message(string $message, bool $success = false): void
{
    $query = http_build_query([
        'msg' => $message,
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
    $conn = new mysqli('localhost', 'root', '', 'elearning');
    if ($conn->connect_error) {
        throw new Exception('Impossible de se connecter au serveur.');
    }

    $check = $conn->prepare('SELECT CIN FROM professeur WHERE CIN = ?');
    $check->bind_param('i', $cin);
    $check->execute();
    $exists = $check->get_result();

    if ($exists->num_rows > 0) {
        $check->close();
        $conn->close();
        redirect_with_message('Ce CIN est deja utilise.');
    }

    $check->close();

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $insert = $conn->prepare('INSERT INTO professeur (CIN, nom, prenom, password) VALUES (?, ?, ?, ?)');
    $insert->bind_param('isss', $cin, $nom, $prenom, $passwordHash);
    $insert->execute();

    if ($insert->affected_rows <= 0) {
        throw new Exception("La creation du compte a echoue.");
    }

    session_regenerate_id(true);
    $_SESSION['CIN'] = (int) $cin;
    $_SESSION['prenom'] = $prenom;
    $_SESSION['nom'] = $nom;

    $insert->close();
    $conn->close();

    header('Location: offres.php');
    exit();
} catch (Exception $e) {
    redirect_with_message($e->getMessage());
}
?>
