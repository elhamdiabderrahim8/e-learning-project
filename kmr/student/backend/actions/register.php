<?php

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/registre.php');
}

// Nettoyage des données
$CIN = trim((string) ($_POST['CIN'] ?? ''));
$firstName = trim((string) ($_POST['nom'] ?? ''));
$lastName = trim((string) ($_POST['prenom'] ?? ''));
$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$password = (string) ($_POST['password'] ?? '');
$confirmPassword = (string) ($_POST['confirm_password'] ?? '');

// Validation
if ($CIN === '' || $firstName === '' || $lastName === '' || $email === '' || $password === '' || $confirmPassword === '') {
    set_flash('error', 'Tous les champs sont obligatoires.');
    redirect('../../pages/registre.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('error', 'Adresse email invalide.');
    redirect('../../pages/registre.php');
}

if (strlen($password) < 8) {
    set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
    redirect('../../pages/registre.php');
}

if ($password !== $confirmPassword) {
    set_flash('error', 'Les mots de passe ne correspondent pas.');
    redirect('../../pages/registre.php');
}

if (strlen($CIN) < 8) {
    set_flash('error', 'Le CIN doit contenir au moins 8 caractères.');
    redirect('../../pages/registre.php');
}

$pdo = db();

// Vérifier si le CIN existe déjà
$existing = $pdo->prepare('SELECT CIN FROM etudiant WHERE CIN = :CIN LIMIT 1');
$existing->execute(['CIN' => $CIN]);
if ($existing->fetch()) {
    set_flash('error', 'Ce CIN est déjà utilisé.');
    redirect('../../pages/registre.php');
}

// Hachage du mot de passe
$hash = password_hash($password, PASSWORD_DEFAULT);

// --- CORRECTION DE LA REQUÊTE ---
// Ajout de la virgule manquante et mise en correspondance exacte des colonnes
$insert = $pdo->prepare('INSERT INTO etudiant (CIN, nom, prenom, email, password, preferred_language) 
                         VALUES (:CIN, :nom, :prenom, :email, :password_hash, :lang)');

$success = $insert->execute([
    'CIN'           => $CIN,
    'nom'           => $lastName,
    'prenom'        => $firstName,
    'email'         => $email,
    'password_hash' => $hash,
    'lang'          => 'fr', // Tu peux mettre 'fr' par défaut pour Enjah
]);

if ($success) {
    $fullName = $firstName . ' ' . $lastName;
    
    // On utilise le CIN comme identifiant unique pour la session
    login_user($CIN, $fullName); 
    
    $_SESSION['CIN'] = $CIN; // Très important pour tes futures requêtes de cours
    $_SESSION['preferred_language'] = 'fr';
    
    set_flash('success', 'Compte créé avec succès.');
    redirect('../../pages/offres.php');
} else {
    set_flash('error', 'Une erreur est survenue lors de l\'inscription.');
    redirect('../../pages/registre.php');
}
