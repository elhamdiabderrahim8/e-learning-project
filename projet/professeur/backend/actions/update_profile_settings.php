<?php

require_once __DIR__ . '/../includes/bootstrap.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/profil.php');
}

$firstName = trim((string) ($_POST['first_name'] ?? ''));
$lastName = trim((string) ($_POST['last_name'] ?? ''));
$language = (string) ($_POST['preferred_language'] ?? 'fr');

if ($firstName === '' || $lastName === '') {
    set_flash('error', 'Le prenom et le nom sont obligatoires.');
    redirect('../../pages/profil.php');
}

if ($language !== 'en' && $language !== 'fr') {
    $language = 'fr';
}

$pdo = db();

$update = $pdo->prepare('UPDATE professeur SET prenom = :prenom, nom = :nom WHERE CIN = :cin');
$update->execute([
    'prenom' => $firstName,
    'nom' => $lastName,
    'cin' => user_id(),
]);

try {
    $pdo->prepare('UPDATE professeur SET preferred_language = :lang WHERE CIN = :cin')->execute([
        'lang' => $language,
        'cin' => user_id(),
    ]);
} catch (Throwable $e) {
    // Column may not exist on older schemas.
}

$_SESSION['nom'] = trim($lastName . ' ' . $firstName);
$_SESSION['preferred_language'] = $language;
$_SESSION['preferred_language_synced'] = true;

set_flash('success', 'Profil mis a jour avec succes.');
redirect('../../pages/profil.php');
