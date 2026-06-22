<?php
session_start();

// 1. Vérification de sécurité
if (empty($_SESSION["CIN"]) || !isset($_POST['modifier_infos'])) {
    header("Location: login.html");
    exit();
}

// 2. Connexion à la base de données
require_once __DIR__ . '/../student/database/database.php';
$pdo = db();

// 3. Récupération des données du formulaire
$cin_actuel = (int)$_SESSION["CIN"]; 
$nom = $_POST['NOM'];
$prenom = $_POST['PRENOM'];
$nouveau_cin = (int)$_POST['CIN'];
$nouveau_mdp = $_POST['PASSWORDN']; // Nouveau mot de passe
$mdp_confirmation = $_POST['PASSWORD']; // Mot de passe actuel tapé

// 4. Récupérer le hash actuel pour vérification
$stmt = $pdo->prepare("SELECT password FROM etudiant WHERE CIN = :cin");
$stmt->execute(['cin' => $cin_actuel]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // 5. Vérification du mot de passe actuel
    if (password_verify($mdp_confirmation, $user['password'])) {
        
        // 6. Gestion du mot de passe (on hache le nouveau s'il est rempli)
        // Sinon, on garde l'ancien hash
        if (!empty($nouveau_mdp)) {
            $mdp_final_hache = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
        } else {
            $mdp_final_hache = $user['password'];
        }

        // 7. Mise à jour (nom:s, prenom:s, CIN:i, password:s, WHERE CIN:i)
        $update = $pdo->prepare("UPDATE etudiant SET nom = :nom, prenom = :prenom, CIN = :new_cin, password = :pass WHERE CIN = :cin_actuel");
        $success = $update->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'new_cin' => $nouveau_cin,
            'pass' => $mdp_final_hache,
            'cin_actuel' => $cin_actuel
        ]);
        
        if ($success) {
            // Mettre à jour la session avec le nouveau CIN et prénom
            $_SESSION["CIN"] = $nouveau_cin;
            $_SESSION["prenom"] = $prenom;
            header("Location: infos_reussit.php");
        } else {
            echo "Erreur lors de la mise à jour.";
        }
        
    } else {
        echo "<script>alert('Mot de passe actuel incorrect !'); window.history.back();</script>";
    }
}
?>