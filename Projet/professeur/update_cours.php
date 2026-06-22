<?php
session_start();
require_once __DIR__ . '/course_image_utils.php';

if (!isset($_SESSION['CIN'])) {
    header('Location: login.html');
    exit();
}

require_once __DIR__ . '/../student/database/database.php';

try {
    $pdo = db();
} catch (Exception $e) {
    set_course_flash('error', 'La connexion a la base a echoue.');
    redirect_course_offers();
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect_course_offers();
}

$idProf = (int) $_SESSION['CIN'];
$idCours = (int) ($_POST['id_cours'] ?? 0);
$nomCours = trim((string) ($_POST['nom_cours'] ?? ''));
$categorie = trim((string) ($_POST['categorie'] ?? 'Premium'));
$prix = (float) ($_POST['prix'] ?? 0);

if ($idCours <= 0 || $nomCours === '') {
    set_course_flash('error', 'Donnees invalides.');
    redirect_course_offers();
}

if ($categorie !== 'Premium' && $categorie !== 'Free') {
    $categorie = 'Premium';
}

if ($categorie === 'Free') {
    $prix = 0;
}

$hasNewImage = isset($_FILES['file'])
    && is_array($_FILES['file'])
    && (int) ($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;

try {
    if ($hasNewImage) {
        $image = normalize_course_upload($_FILES['file']);
        $data = $image['data'];
        $type = $image['type'];
        $name = $image['name'];

        $stmt = $pdo->prepare('UPDATE cours SET nom_cours = :nom, prix = :prix, categorie = :cat, image_data = :data, image_type = :type, image_name = :name WHERE id = :id AND id_professeur = :prof');

        $stmt->bindParam(':nom', $nomCours);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':cat', $categorie);
        $stmt->bindParam(':data', $data, PDO::PARAM_LOB);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $idCours, PDO::PARAM_INT);
        $stmt->bindParam(':prof', $idProf, PDO::PARAM_INT);
    } else {
        $stmt = $pdo->prepare('UPDATE cours SET nom_cours = :nom, prix = :prix, categorie = :cat WHERE id = :id AND id_professeur = :prof');
        $stmt->bindParam(':nom', $nomCours);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':cat', $categorie);
        $stmt->bindParam(':id', $idCours, PDO::PARAM_INT);
        $stmt->bindParam(':prof', $idProf, PDO::PARAM_INT);
    }

    if (!$stmt->execute()) {
        throw new RuntimeException('Erreur lors de la mise a jour.');
    }

    set_course_flash('success', 'Cours mis a jour avec succes.');
    redirect_course_offers();
} catch (Throwable $e) {
    set_course_flash('error', $e->getMessage());
    redirect_course_offers();
}
