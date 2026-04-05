<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'elearning');

// Vérifier si le professeur est bien sur une session de cours
if (!isset($_SESSION['id_cours_actuel'])) {
    die("Erreur : Aucun cours n'est sélectionné pour l'ajout de leçons.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fichier_lecon'])) {
    $id_cours = $_SESSION['id_cours_actuel'];
    $titre = mysqli_real_escape_string($conn, $_POST['titre']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Informations sur le fichier
    $fileName = basename($_FILES['fichier_lecon']['name']);
    $fileType = $_FILES['fichier_lecon']['type'];
    $tmpName  = $_FILES['fichier_lecon']['tmp_name'];
     $destination = "uploads/" . $fileName;
    move_uploaded_file($tmpName, $destination);
    if (!empty($tmpName)) {
        // Lecture du fichier en binaire
        $fileContent = file_get_contents($tmpName);

        // Préparation de la requête pour insérer le BLOB en toute sécurité
        $stmt = $conn->prepare("INSERT INTO lecon (titre, description, type_fichier, contenu_blob, nom_fichier, id_cours) VALUES (?, ?, ?, ?, ?, ?)");
        
        // "s" pour string, "b" pour blob, "i" pour integer
        $null = NULL; // Placeholder pour le blob
        $stmt->bind_param("sssbsi", $titre, $description, $fileType, $null, $fileName, $id_cours);
        $stmt->send_long_data(3, $fileContent); // Envoie les données binaires au 4ème paramètre

        if ($stmt->execute()) {
            // Redirection avec un message de succès
            header("Location: lesson.php?status=success");
        } else {
            echo "Erreur lors de l'insertion : " . $stmt->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>