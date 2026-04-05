<?php

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/login.php');
}

$cin = trim((string) ($_POST['CIN'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if ($cin === '' || $password === '') {
    set_flash('error', 'CIN et mot de passe requis.');
    redirect('../../pages/login.php');
}

$pdo = db();
$stmt = $pdo->prepare('SELECT CIN, nom, prenom, password FROM professeur WHERE CIN = :cin LIMIT 1');
$stmt->execute(['cin' => $cin]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, (string) $user['password'])) {
    set_flash('error', 'Identifiants invalides.');
    redirect('../../pages/login.php');
}

$fullName = trim((string) $user['nom'] . ' ' . (string) $user['prenom']);
login_user((int) $user['CIN'], $fullName);

$preferredLanguage = (string) ($_SESSION['preferred_language'] ?? 'fr');
if ($preferredLanguage !== 'fr' && $preferredLanguage !== 'en') {
    $preferredLanguage = 'fr';
}

$_SESSION['preferred_language'] = $preferredLanguage;
$_SESSION['preferred_language_synced'] = true;
set_flash('success', 'Connexion reussie.');

redirect('../../pages/cours.php');
