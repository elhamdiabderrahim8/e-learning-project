<?php

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/login.php');
}

// 1. On garde le CIN tel quel (pas de strtolower car c'est souvent numérique)
$CIN = trim((string) ($_POST['CIN'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if ($CIN === '' || $password === '') {
    set_flash('error', 'CIN et mot de passe requis.');
    redirect('../../pages/login.php');
}

$pdo = db();

// --- CORRECTION 1 : Sélection de la bonne colonne 'password' ---
$stmt = $pdo->prepare('SELECT CIN, nom, prenom, password, preferred_language FROM etudiant WHERE CIN = :CIN LIMIT 1');
$stmt->execute(['CIN' => $CIN]);
$user = $stmt->fetch();

// --- CORRECTION 2 : Utilisation de la clé correcte du tableau et vérification ---
if (!$user || !password_verify($password, $user['password'])) {
    set_flash('error', 'Identifiants invalides.');
    redirect('../../pages/login.php');
}

// --- CORRECTION 3 : Concaténation Nom + Prénom ---
$fullName = trim($user['nom'] . ' ' . $user['prenom']);

// --- CORRECTION 4 : Ne pas forcer le (int) sur le CIN si c'est ta clé primaire ---
login_user($user['CIN'], $fullName);

// On assure la présence du CIN en session pour tes requêtes SQL futures
$_SESSION['CIN'] = $user['CIN'];

$preferredLanguage = (string) ($user['preferred_language'] ?? 'fr');
if ($preferredLanguage !== 'fr' && $preferredLanguage !== 'en') {
    $preferredLanguage = 'fr';
}

$_SESSION['preferred_language'] = $preferredLanguage;
$_SESSION['preferred_language_synced'] = true;

set_flash('success', 'Connexion réussie.');
redirect('../../pages/cours.php');