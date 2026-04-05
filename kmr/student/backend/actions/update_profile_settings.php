<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../pages/profil.php');
}

// 1. Récupération des données textuelles
$firstName = trim((string) ($_POST['first_name'] ?? ''));
$lastName = trim((string) ($_POST['last_name'] ?? ''));
$language = (string) ($_POST['preferred_language'] ?? 'en');
$cin = $_SESSION['CIN']; 

if ($firstName === '' || $lastName === '') {
    set_flash('error', 'Le prénom et le nom sont obligatoires.');
    redirect('../../pages/profil.php');
}

if ($language !== 'fr' && $language !== 'en') {
    $language = 'fr';
}

$pdo = db();

try {
    // 2. Mise à jour des informations textuelles
    $update = $pdo->prepare('UPDATE etudiant SET prenom = :prenom, nom = :nom, preferred_language = :lang WHERE CIN = :cin');
    $update->execute([
        'prenom' => $firstName,
        'nom'    => $lastName,
        'lang'   => $language,
        'cin'    => $cin,
    ]);

    // 3. Gestion de la PHOTO (Colonnes 'data' et 'type')
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $fileType = $_FILES['profile_image']['type'];

        if (in_array($fileType, $allowedTypes)) {
            $imageData = file_get_contents($_FILES['profile_image']['tmp_name']);

            // --- CORRECTION ICI : Utilisation de 'data' et 'type' ---
            $stmtImg = $pdo->prepare('UPDATE etudiant SET data = :binData, type = :mimeType WHERE CIN = :cin');
            $stmtImg->execute([
                'binData'  => $imageData,
                'mimeType' => $fileType,
                'cin'      => $cin
            ]);
        } else {
            set_flash('error', 'Format d\'image non supporté.');
        }
    }

    $_SESSION['full_name'] = trim($firstName . ' ' . $lastName);
    $_SESSION['preferred_language'] = $language;
    $_SESSION['preferred_language_synced'] = true;

    set_flash('success', 'Profil mis à jour avec succès.');

} catch (PDOException $e) {
    set_flash('error', 'Erreur SQL : ' . $e->getMessage());
}

redirect('../../pages/profil.php');