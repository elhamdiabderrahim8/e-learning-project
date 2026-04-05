<?php
// Paramètres de connexion (Ajuste selon ton XAMPP)
$host = "localhost";
$user = "root";
$pass = ""; // Laisse vide si tu n'as pas mis de mot de passe sur XAMPP
$dbname = "elearning"; // REMPLACE par le nom de la base créé par le script de ton ami

// Création de la connexion MySQLi
$conn = new mysqli($host, $user, $pass, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    // Si ça échoue, on arrête tout et on affiche l'erreur
    die("Erreur de connexion : " . $conn->connect_error);
}

// Pour que les accents (Français/Arabe) s'affichent correctement
$conn->set_charset("utf8mb4");
?>