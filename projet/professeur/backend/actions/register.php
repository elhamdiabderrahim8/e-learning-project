<?php

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/registre.php');
}

$cin = trim((string) ($_POST['CIN'] ?? ''));
$firstName = trim((string) ($_POST['prenom'] ?? ''));
$lastName = trim((string) ($_POST['nom'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$confirmPassword = (string) ($_POST['confirm_password'] ?? '');

if ($cin === '' || $firstName === '' || $lastName === '' || $password === '' || $confirmPassword === '') {
    set_flash('error', 'Tous les champs sont obligatoires.');
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

$existing = $pdo->prepare('SELECT CIN FROM professeur WHERE CIN = :cin LIMIT 1');
$existing->execute(['cin' => $cin]);
if ($existing->fetch()) {
    set_flash('error', 'Ce CIN est deja utilise.');
    redirect('../../pages/registre.php');
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$insert = $pdo->prepare('INSERT INTO professeur (CIN, nom, prenom, password) VALUES (:cin, :nom, :prenom, :password_hash)');
$insert->execute([
    'cin' => $cin,
    'nom' => $lastName,
    'prenom' => $firstName,
    'password_hash' => $hash,
]);

$fullName = $lastName . ' ' . $firstName;
login_user((int) $cin, $fullName);
$_SESSION['preferred_language'] = 'fr';
$_SESSION['preferred_language_synced'] = true;

set_flash('success', 'Compte cree avec succes.');
redirect('../../pages/offres.php');
