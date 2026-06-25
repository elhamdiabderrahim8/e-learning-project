<?php
require_once __DIR__ . '/session_prof.php';

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
    require_once __DIR__ . '/../student/database/database.php';
    $pdo = db();

    $stmt = $pdo->prepare('SELECT CIN, nom, prenom, password FROM professeur WHERE CIN = :cin LIMIT 1');
    $stmt->execute(['cin' => $cin]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $storedPassword = (string) ($user['password'] ?? '');
    $passwordMatches = $user && password_verify($password, $storedPassword);

    // Backward compatibility for older rows that still store plain-text passwords.
    if (!$passwordMatches && $user && hash_equals($storedPassword, $password)) {
        $passwordMatches = true;

        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $update = $pdo->prepare('UPDATE professeur SET password = :hash WHERE CIN = :cin');
        $update->execute(['hash' => $newHash, 'cin' => $user['CIN']]);
    }

    if (!$user || !$passwordMatches) {
        redirect_login('Identifiants invalides.');
    }

    session_regenerate_id(false);
    $_SESSION['CIN'] = (int) $user['CIN'];
    $_SESSION['prenom'] = (string) $user['prenom'];
    $_SESSION['nom'] = (string) $user['nom'];

    header('Location: offres.php');
    exit();
} catch (Exception $e) {
    error_log('Prof login_action error: ' . $e->getMessage());
    redirect_login("Erreur système. Veuillez réessayer.");
}
