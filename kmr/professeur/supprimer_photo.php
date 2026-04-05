<?php
session_start();

// 1. Connexion à la base de données avec MySQLi

$conn = new mysqli("localhost", "root", "", "elearning");

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// 2. Vérification de la session (CIN d'après ta table)
if (!isset($_SESSION['CIN'])) {
    header('Location: login.php');
    exit();
}

$cin = $_SESSION['CIN'];

// 3. Préparation de la requête pour vider les colonnes data, name et type
// On utilise une requête préparée pour la sécurité (contre les injections SQL)
$sql = "UPDATE professeur SET data = NULL, name = NULL, type = NULL WHERE CIN = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    // "i" car ton CIN est de type int(50) d'après ta capture
    $stmt->bind_param("i", $cin);
    
    if ($stmt->execute()) {
        // Redirection vers le profil avec un message de succès
        header('Location: infos.php?status=success');
    } else {
        header('Location: infos.php?status=error');
    }
    
    $stmt->close();
}

$conn->close();
exit();