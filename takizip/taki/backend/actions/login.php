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
$stmt = $pdo->prepare('SELECT id, first_name, last_name, password_hash FROM users WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    set_flash('error', 'Identifiants invalides.');
    redirect('../../pages/login.php');
}

$fullName = trim($user['first_name'] . ' ' . $user['last_name']);
login_user((int) $user['id'], $fullName);
set_flash('success', 'Connexion reussie.');

redirect('../../pages/cours.php');
