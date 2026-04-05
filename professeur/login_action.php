<?php
session_start();

$cin = trim((string) ($_POST['CIN'] ?? ''));
$password = (string) ($_POST['password'] ?? ($_POST['PASSWORD'] ?? ''));

function redirect_login(string $message, bool $success = false): void
{
    $query = http_build_query([
        'msg' => $message,
        'success' => $success ? '1' : '0',
    ]);

    header('Location: login.php?' . $query);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

if ($cin === '' || $password === '') {
    redirect_login('CIN et mot de passe requis.');
}

try {
    $conn = new mysqli('localhost', 'root', '', 'elearning');
    if ($conn->connect_error) {
        throw new Exception('Impossible de se connecter au serveur.');
    }

    $stmt = $conn->prepare('SELECT CIN, nom, prenom, password FROM professeur WHERE CIN = ? LIMIT 1');
    $stmt->bind_param('i', $cin);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $storedPassword = (string) ($user['password'] ?? '');
    $passwordMatches = $user && password_verify($password, $storedPassword);

    // Backward compatibility for older rows that still store plain-text passwords.
    if (!$passwordMatches && $user && hash_equals($storedPassword, $password)) {
        $passwordMatches = true;

        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare('UPDATE professeur SET password = ? WHERE CIN = ?');
        if ($update) {
            $update->bind_param('si', $newHash, $user['CIN']);
            $update->execute();
            $update->close();
        }
    }

    if (!$user || !$passwordMatches) {
        $stmt->close();
        $conn->close();
        redirect_login('Identifiants invalides.');
    }

    session_regenerate_id(true);
    $_SESSION['CIN'] = (int) $user['CIN'];
    $_SESSION['prenom'] = (string) $user['prenom'];
    $_SESSION['nom'] = (string) $user['nom'];

    $stmt->close();
    $conn->close();

    header('Location: offres.php');
    exit();
} catch (Exception $e) {
    redirect_login($e->getMessage());
}
