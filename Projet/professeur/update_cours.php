<?php
session_start();
require_once __DIR__ . '/course_image_utils.php';

if (!isset($_SESSION['CIN'])) {
    header('Location: login.html');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'elearning');
if ($conn->connect_error) {
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

        $stmt = $conn->prepare('UPDATE cours SET nom_cours = ?, prix = ?, categorie = ?, image_data = ?, image_type = ?, image_name = ? WHERE id = ? AND id_professeur = ?');
        if (!$stmt) {
            throw new RuntimeException('Erreur de preparation SQL.');
        }

        $stmt->bind_param('sdssssii', $nomCours, $prix, $categorie, $data, $type, $name, $idCours, $idProf);
    } else {
        $stmt = $conn->prepare('UPDATE cours SET nom_cours = ?, prix = ?, categorie = ? WHERE id = ? AND id_professeur = ?');
        if (!$stmt) {
            throw new RuntimeException('Erreur de preparation SQL.');
        }

        $stmt->bind_param('sdsii', $nomCours, $prix, $categorie, $idCours, $idProf);
    }

    if (!$stmt->execute()) {
        throw new RuntimeException('Erreur lors de la mise a jour : ' . $stmt->error);
    }

    $stmt->close();
    set_course_flash('success', 'Cours mis a jour avec succes.');
    redirect_course_offers();
} catch (Throwable $e) {
    set_course_flash('error', $e->getMessage());
    redirect_course_offers();
} finally {
    $conn->close();
}
