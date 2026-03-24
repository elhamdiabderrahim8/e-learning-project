<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/profil.php');
}

$firstName = trim((string) ($_POST['first_name'] ?? ''));
$lastName = trim((string) ($_POST['last_name'] ?? ''));
$language = (string) ($_POST['preferred_language'] ?? 'en');

if ($firstName === '' || $lastName === '') {
    set_flash('error', 'Le prenom et le nom sont obligatoires.');
    redirect('../../pages/profil.php');
}

$allowedLanguages = ['en', 'fr'];
if (!in_array($language, $allowedLanguages, true)) {
    $language = 'en';
}

$pdo = db();

$update = $pdo->prepare('UPDATE users SET first_name = :first_name, last_name = :last_name, preferred_language = :preferred_language WHERE id = :id');
$update->execute([
    'first_name' => $firstName,
    'last_name' => $lastName,
    'preferred_language' => $language,
    'id' => user_id(),
]);

$_SESSION['full_name'] = trim($firstName . ' ' . $lastName);
$_SESSION['preferred_language'] = $language;
$_SESSION['preferred_language_synced'] = true;

set_flash('success', 'Profil mis a jour avec succes.');
redirect('../../pages/profil.php');
