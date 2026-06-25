<?php
require_once __DIR__ . '/session_prof.php';
require_once __DIR__ . '/../student/database/database.php';
$pdo = db();

// Vérifier si le professeur est bien sur une session de cours
if (!isset($_SESSION['id_cours_actuel'])) {
    die("Erreur : Aucun cours n'est sélectionné pour l'ajout de leçons.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fichier_lecon'])) {
    $id_cours = $_SESSION['id_cours_actuel'];
    $titre = trim((string) ($_POST['titre'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    
    // Informations sur le fichier
    $fileName = basename($_FILES['fichier_lecon']['name']);
    $fileType = $_FILES['fichier_lecon']['type'];
    $tmpName  = $_FILES['fichier_lecon']['tmp_name'];

    // Validate file type - allow only safe document/media types
    $allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'video/mp4', 'video/webm',
        'text/plain',
    ];
    $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'txt'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileType, $allowedTypes, true) || !in_array($ext, $allowedExtensions, true)) {
        die("Erreur : Type de fichier non autorisé.");
    }

    // Enforce max file size (50 MB)
    if ($_FILES['fichier_lecon']['size'] > 50 * 1024 * 1024) {
        die("Erreur : Le fichier dépasse la taille maximale autorisée (50 Mo).");
    }

    // Use a unique name to prevent overwrites and path traversal
    $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
    $destination = "uploads/" . $safeName;
    if (!is_dir("uploads")) {
        mkdir("uploads", 0755, true);
    }
    move_uploaded_file($tmpName, $destination);

    if (!empty($tmpName)) {
        // Lecture du fichier en binaire
        $fileContent = file_get_contents($destination);

        // Préparation de la requête PDO pour insérer le BLOB en toute sécurité
        $stmt = $pdo->prepare("INSERT INTO lecon (titre, description, type_fichier, contenu_blob, nom_fichier, id_cours) VALUES (:titre, :description, :type, :blob, :nom, :id_cours)");
        $stmt->bindParam(':titre',       $titre);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':type',        $fileType);
        $stmt->bindParam(':blob',        $fileContent, PDO::PARAM_LOB);
        $stmt->bindParam(':nom',         $fileName);
        $stmt->bindParam(':id_cours',    $id_cours, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: lesson.php?status=success");
        } else {
            echo "Erreur lors de l'insertion.";
        }
    }
}
?>