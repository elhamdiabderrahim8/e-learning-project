<?php
session_start();
require_once __DIR__ . '/course_image_utils.php';

if (!isset($_SESSION['CIN'])) {
    set_course_flash('error', 'Veuillez vous reconnecter.');
    redirect_course_offers();
}

$conn = new mysqli('localhost', 'root', '', 'elearning');
if ($conn->connect_error) {
    set_course_flash('error', 'La connexion a la base a echoue.');
    redirect_course_offers();
}

if (!isset($_POST['submit'])) {
    redirect_course_offers();
}

$idProf = (int) $_SESSION['CIN'];
$nom = trim((string) ($_POST['nom_cours'] ?? ''));
$categorie = (string) ($_POST['categorie'] ?? 'Premium');
$prix = $categorie === 'Free' ? 0 : (float) ($_POST['prix'] ?? 0);

if ($nom === '') {
    set_course_flash('error', 'Le nom du cours est obligatoire.');
    redirect_course_offers();
}

try {
    $image = normalize_course_upload($_FILES['file'] ?? []);

    $stmt = $conn->prepare('INSERT INTO cours (nom_cours, prix, categorie, image_data, image_type, image_name, id_professeur) VALUES (?, ?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        throw new RuntimeException('Erreur de preparation SQL.');
    }

    $data = $image['data'];
    $type = $image['type'];
    $name = $image['name'];
    $stmt->bind_param('sdssssi', $nom, $prix, $categorie, $data, $type, $name, $idProf);

    if (!$stmt->execute()) {
        throw new RuntimeException('Erreur lors de l insertion : ' . $stmt->error);
    }

    $stmt->close();
    set_course_flash('success', 'Cours ajoute avec succes.');
    redirect_course_offers();
} catch (Throwable $e) {
    set_course_flash('error', $e->getMessage());
    redirect_course_offers();
} finally {
    $conn->close();
}
