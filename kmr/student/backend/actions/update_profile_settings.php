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
$cin = (string) ($_SESSION['CIN'] ?? '');

if ($cin === '') {
    set_flash('error', 'Session invalide. Veuillez vous reconnecter.');
    redirect('../../pages/login.php');
}

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

    // 3. Gestion de la photo de profil (colonnes data/type)
    if (isset($_FILES['profile_image']) && is_array($_FILES['profile_image'])) {
        $fileError = (int) ($_FILES['profile_image']['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($fileError !== UPLOAD_ERR_NO_FILE) {
            if ($fileError !== UPLOAD_ERR_OK) {
                $uploadErrors = [
                    UPLOAD_ERR_INI_SIZE => 'Image trop volumineuse (limite serveur).',
                    UPLOAD_ERR_FORM_SIZE => 'Image trop volumineuse (limite formulaire).',
                    UPLOAD_ERR_PARTIAL => 'Televersement incomplet. Reessayez.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant sur le serveur.',
                    UPLOAD_ERR_CANT_WRITE => 'Impossible d\'ecrire le fichier sur le disque.',
                    UPLOAD_ERR_EXTENSION => 'Televersement bloque par une extension PHP.',
                ];
                $message = $uploadErrors[$fileError] ?? 'Echec de televersement de l\'image.';
                set_flash('error', $message);
                redirect('../../pages/profil.php');
            }

            $tmpName = (string) ($_FILES['profile_image']['tmp_name'] ?? '');
            $fileSize = (int) ($_FILES['profile_image']['size'] ?? 0);

            if ($tmpName === '' || !is_uploaded_file($tmpName)) {
                set_flash('error', 'Fichier image invalide.');
                redirect('../../pages/profil.php');
            }

            // 5 MB max for profile pictures.
            if ($fileSize <= 0 || $fileSize > 5 * 1024 * 1024) {
                set_flash('error', 'Image trop volumineuse (max 5 MB).');
                redirect('../../pages/profil.php');
            }

            $detectedType = '';
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $detectedType = (string) finfo_file($finfo, $tmpName);
                    finfo_close($finfo);
                }
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($detectedType, $allowedTypes, true)) {
                // Fallback for hosts where FILEINFO is unavailable or misconfigured.
                $imageInfo = @getimagesize($tmpName);
                $detectedType = is_array($imageInfo) ? (string) ($imageInfo['mime'] ?? '') : '';
            }

            if (!in_array($detectedType, $allowedTypes, true)) {
                set_flash('error', 'Format d\'image non supporte.');
                redirect('../../pages/profil.php');
            }

            $imageData = file_get_contents($tmpName);
            if ($imageData === false) {
                set_flash('error', 'Impossible de lire le fichier image.');
                redirect('../../pages/profil.php');
            }

            $stmtImg = $pdo->prepare('UPDATE etudiant SET data = :binData, type = :mimeType WHERE CIN = :cin');
            $stmtImg->bindValue(':binData', $imageData, PDO::PARAM_LOB);
            $stmtImg->bindValue(':mimeType', $detectedType, PDO::PARAM_STR);
            $stmtImg->bindValue(':cin', $cin, PDO::PARAM_STR);
            $stmtImg->execute();
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