<?php
session_start();

if (!isset($_SESSION['CIN'])) {
    header('Location: login.html');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'elearning');
if ($conn->connect_error) {
    die('La connexion a echoue : ' . $conn->connect_error);
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: offres.php');
    exit();
}

$idProf = (int) $_SESSION['CIN'];
$idCours = (int) ($_POST['id_cours'] ?? 0);
$nomCours = trim((string) ($_POST['nom_cours'] ?? ''));
$categorie = trim((string) ($_POST['categorie'] ?? 'Premium'));
$prix = (float) ($_POST['prix'] ?? 0);

if ($idCours <= 0 || $nomCours === '') {
    die('Donnees invalides.');
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

if ($hasNewImage) {
    $file = $_FILES['file'];
    $type = (string) ($file['type'] ?? '');
    $name = (string) ($file['name'] ?? '');
    $tmpName = (string) ($file['tmp_name'] ?? '');

    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        die('Image invalide.');
    }

    $data = file_get_contents($tmpName);
    if ($data === false) {
        die('Impossible de lire l\'image.');
    }

    $stmt = $conn->prepare('UPDATE cours SET nom_cours = ?, prix = ?, categorie = ?, image_data = ?, image_type = ?, image_name = ? WHERE id = ? AND id_professeur = ?');
    if (!$stmt) {
        die('Erreur de preparation SQL.');
    }
    $stmt->bind_param('sdssssii', $nomCours, $prix, $categorie, $data, $type, $name, $idCours, $idProf);
} else {
    $stmt = $conn->prepare('UPDATE cours SET nom_cours = ?, prix = ?, categorie = ? WHERE id = ? AND id_professeur = ?');
    if (!$stmt) {
        die('Erreur de preparation SQL.');
    }
    $stmt->bind_param('sdsii', $nomCours, $prix, $categorie, $idCours, $idProf);
}

if ($stmt->execute()) {
    header('Location: offres.php');
    exit();
}

die('Erreur lors de la mise a jour : ' . $stmt->error);
