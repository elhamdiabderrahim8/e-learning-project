<?php
require_once __DIR__ . '/session_prof.php';
require_once __DIR__ . '/course_image_utils.php';
require_once __DIR__ . '/../student/database/database.php';

if (!isset($_SESSION['CIN'])) {
    $debug = [
        'time' => date('Y-m-d H:i:s'),
        'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
        'cookies' => $_COOKIE,
        'session_id' => session_id(),
        'session_data' => $_SESSION,
        'headers' => getallheaders()
    ];
    file_put_contents(__DIR__ . '/session_debug.txt', print_r($debug, true) . "\n---\n", FILE_APPEND);

    set_course_flash('error', 'Veuillez vous reconnecter.');
    redirect_course_offers();
}

// Détection d'un POST vide causé par une image trop lourde (dépassement upload_max_filesize)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES)) {
    set_course_flash('error', 'Image trop lourde. Choisissez une image de moins de 10 Mo.');
    redirect_course_offers();
}

try {
    $pdo = db();
} catch (Exception $e) {
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

    $stmt = $pdo->prepare('INSERT INTO cours (nom_cours, prix, categorie, image_data, image_type, image_name, id_professeur) VALUES (:nom, :prix, :categorie, :data, :type, :name, :idProf)');

    $data = $image['data'];
    $type = $image['type'];
    $name = $image['name'];
    
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prix', $prix);
    $stmt->bindParam(':categorie', $categorie);
    $stmt->bindParam(':data', $data, PDO::PARAM_LOB);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':idProf', $idProf, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        throw new RuntimeException('Erreur lors de l insertion.');
    }

    set_course_flash('success', 'Cours ajoute avec succes.');
    redirect_course_offers();
} catch (Throwable $e) {
    set_course_flash('error', $e->getMessage());
    redirect_course_offers();
}
