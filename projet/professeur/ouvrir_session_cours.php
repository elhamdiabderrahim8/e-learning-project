<?php
session_start();

if (isset($_GET['id'])) {
    // On stocke l'ID du cours dans la session
    $_SESSION['id_cours_actuel'] = intval($_GET['id']);
    
    // On redirige vers ta page d'interface (celle de image_a6e19e.png)
    header("Location: lesson.php");
    exit();
}
?>