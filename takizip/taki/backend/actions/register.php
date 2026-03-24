<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/registre.php');
}

$firstName = trim((string) ($_POST['firstname'] ?? ''));
$lastName = trim((string) ($_POST['lastname'] ?? ''));
$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$password = (string) ($_POST['password'] ?? '');
$confirmPassword = (string) ($_POST['confirm_password'] ?? '');

if ($firstName === '' || $lastName === '' || $email === '' || $password === '' || $confirmPassword === '') {
    set_flash('error', 'Tous les champs sont obligatoires.');
    redirect('../../pages/registre.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('error', 'Adresse email invalide.');
    redirect('../../pages/registre.php');
}

if (strlen($password) < 8) {
    set_flash('error', 'Le mot de passe doit contenir au moins 8 caracteres.');
    redirect('../../pages/registre.php');
}

if ($password !== $confirmPassword) {
    set_flash('error', 'Les mots de passe ne correspondent pas.');
    redirect('../../pages/registre.php');
}

$pdo = db();

try {
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS preferred_language VARCHAR(10) NOT NULL DEFAULT 'en'");
} catch (Throwable $e) {
    // Ignore if DB permissions restrict alter operations.
}

$existing = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$existing->execute(['email' => $email]);
if ($existing->fetch()) {
    set_flash('error', 'Cet email est deja utilise.');
    redirect('../../pages/registre.php');
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$insert = $pdo->prepare('INSERT INTO users (first_name, last_name, email, password_hash, preferred_language) VALUES (:first_name, :last_name, :email, :password_hash, :preferred_language) RETURNING id');
$insert->execute([
    'first_name' => $firstName,
    'last_name' => $lastName,
    'email' => $email,
    'password_hash' => $hash,
    'preferred_language' => 'en',
]);

$userId = (int) $insert->fetchColumn();
$fullName = $firstName . ' ' . $lastName;
login_user($userId, $fullName);
$_SESSION['preferred_language'] = 'en';

set_flash('success', 'Compte cree avec succes.');
redirect('../../pages/offres.php');
