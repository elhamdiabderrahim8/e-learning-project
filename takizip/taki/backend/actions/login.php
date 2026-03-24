<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/login.php');
}

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$password = (string) ($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    set_flash('error', 'Email et mot de passe requis.');
    redirect('../../pages/login.php');
}

$pdo = db();
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS preferred_language VARCHAR(10) NOT NULL DEFAULT 'en'");
} catch (Throwable $e) {
    // Ignore if DB permissions restrict alter operations.
}

$stmt = $pdo->prepare('SELECT id, first_name, last_name, password_hash, preferred_language FROM users WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    set_flash('error', 'Identifiants invalides.');
    redirect('../../pages/login.php');
}

$fullName = trim($user['first_name'] . ' ' . $user['last_name']);
login_user((int) $user['id'], $fullName);
$_SESSION['preferred_language'] = in_array((string) ($user['preferred_language'] ?? 'en'), ['en', 'fr'], true)
    ? (string) $user['preferred_language']
    : 'en';
set_flash('success', 'Connexion reussie.');

redirect('../../pages/cours.php');
