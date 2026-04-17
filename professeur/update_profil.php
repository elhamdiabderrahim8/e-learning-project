<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'elearning');
$cin_session = $_SESSION['CIN']; // On garde le CIN de la session pour la clause WHERE

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_pwd = $_POST['old_password'];
    
    // 1. Vérifier le mot de passe
    $sql = "SELECT password FROM professeur WHERE CIN = '$cin_session'";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();

    if ($row && password_verify($old_pwd, $row['password'])) {
        
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $nouveau_cin = $_POST['CIN']; // Si tu permets de modifier le CIN
        
        $image_update = "";

        // 2. Gestion de l'image en mode BLOB
        if (!empty($_FILES['nouvelle_image']['tmp_name'])) {
            $fileName = basename($_FILES['nouvelle_image']['name']);
            $fileType = $_FILES['nouvelle_image']['type'];
            $imageContent = addslashes(file_get_contents($_FILES['nouvelle_image']['tmp_name']));
            
            // On prépare la mise à jour des colonnes data, type et name
            $image_update = ", data='$imageContent', type='$fileType', name='$fileName'";
        }

        // 3. Mise à jour finale (Attention : on utilise $cin_session pour trouver la ligne)
        $update = "UPDATE professeur SET nom='$nom', prenom='$prenom', CIN='$nouveau_cin' $image_update WHERE CIN='$cin_session'";
        
        if ($conn->query($update)) {
            // Si le CIN a changé, on met à jour la session
            $_SESSION['CIN'] = $nouveau_cin;
            header("Location: infos.php");
        } else {
            echo "Erreur lors de la mise à jour : " . $conn->error;
        }
    } else {
        echo "<script>alert('Mot de passe actuel incorrect !'); window.history.back();</script>";
    }
}
?>