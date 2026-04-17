<?php

require_once __DIR__ . '/../includes/bootstrap.php';

/**
 * Fonction pour rediriger vers la page d'erreur avec un message
 */
function redirect_error(string $message): void
{
    $query = http_build_query([
        'msg' => $message,
        'success' => '0',
    ]);
    // Adaptez le chemin vers votre page d'affichage d'erreur spécifique
    header('Location: ../../pages/erreur_login.php?' . $query);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/login.php');
}

$CIN = trim((string) ($_POST['CIN'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if ($CIN === '' || $password === '') {
    redirect_error('CIN et mot de passe requis.');
}

try {
    $pdo = db();

    $stmt = $pdo->prepare('SELECT CIN, nom, prenom, password, preferred_language FROM etudiant WHERE CIN = :CIN LIMIT 1');
    $stmt->execute(['CIN' => $CIN]);
    $user = $stmt->fetch();

    // Vérification de l'utilisateur et du mot de passe
    if (!$user || !password_verify($password, $user['password'])) {
        redirect_error('Identifiants invalides ou mot de passe incorrect.');
    }

    // --- CONNEXION RÉUSSIE ---
    
    // Concaténation Nom + Prénom pour la session
    $fullName = trim($user['nom'] . ' ' . $user['prenom']);
    
    // Initialisation de la session via votre fonction bootstrap
    login_user($user['CIN'], $fullName);

    // Stockage des informations spécifiques en session
    $_SESSION['CIN'] = $user['CIN'];
    $_SESSION['nom'] = $user['nom'];
    $_SESSION['prenom'] = $user['prenom'];

    // Gestion de la langue
    $preferredLanguage = (string) ($user['preferred_language'] ?? 'fr');
    if ($preferredLanguage !== 'fr' && $preferredLanguage !== 'en') {
        $preferredLanguage = 'fr';
    }

    $_SESSION['preferred_language'] = $preferredLanguage;
    $_SESSION['preferred_language_synced'] = true;

    // Redirection vers l'espace étudiant
    set_flash('success', 'Connexion réussie.');
    redirect('../../pages/cours.php');

} catch (Exception $e) {
    redirect_error("Erreur système : " . $e->getMessage());
}