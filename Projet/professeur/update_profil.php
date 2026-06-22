<?php
session_start();
require_once __DIR__ . '/../student/database/database.php';
$pdo = db();
$cin_session = $_SESSION['CIN']; // On garde le CIN de la session pour la clause WHERE

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_pwd = $_POST['old_password'];
    
    // 1. Vérifier le mot de passe
    $stmt = $pdo->prepare("SELECT password FROM professeur WHERE CIN = :cin");
    $stmt->execute(['cin' => $cin_session]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($old_pwd, $row['password'])) {
        
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $nouveau_cin = $_POST['CIN']; // Si tu permets de modifier le CIN

        // 2. Gestion de l'image en mode BLOB
        if (!empty($_FILES['nouvelle_image']['tmp_name'])) {
            $fileName = basename($_FILES['nouvelle_image']['name']);
            $fileType = $_FILES['nouvelle_image']['type'];
            $imageContent = file_get_contents($_FILES['nouvelle_image']['tmp_name']);
            
            $updateStmt = $pdo->prepare("UPDATE professeur SET nom=:nom, prenom=:prenom, CIN=:new_cin, data=:data, type=:type, name=:name WHERE CIN=:cin_session");
            $updateStmt->bindParam(':nom', $nom);
            $updateStmt->bindParam(':prenom', $prenom);
            $updateStmt->bindParam(':new_cin', $nouveau_cin);
            $updateStmt->bindParam(':data', $imageContent, PDO::PARAM_LOB);
            $updateStmt->bindParam(':type', $fileType);
            $updateStmt->bindParam(':name', $fileName);
            $updateStmt->bindParam(':cin_session', $cin_session);
            $success = $updateStmt->execute();
        } else {
            // Sans image
            $updateStmt = $pdo->prepare("UPDATE professeur SET nom=:nom, prenom=:prenom, CIN=:new_cin WHERE CIN=:cin_session");
            $success = $updateStmt->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'new_cin' => $nouveau_cin,
                'cin_session' => $cin_session
            ]);
        }
        
        if ($success) {
            // Si le CIN a changé, on met à jour la session
            $_SESSION['CIN'] = $nouveau_cin;
            $_SESSION['nom'] = $nom;
            $_SESSION['prenom'] = $prenom;
            header("Location: infos.php");
        } else {
            echo "Erreur lors de la mise à jour.";
        }
    } else {
        echo "<script>alert('Mot de passe actuel incorrect !'); window.history.back();</script>";
    }
}
?>