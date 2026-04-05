<?php
session_start();
if (empty($_SESSION["CIN"])) {
    header("Location: login.html");
    exit();
}

// 1. Connexion à la base de données avec mysqli
$mysqli = new mysqli("localhost", "root", "", "elearning");

// Vérifier la connexion
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

// 2. Récupérer les infos de l'utilisateur
$stmt = $mysqli->prepare("SELECT *FROM etudiant WHERE CIN = ?");
// "s" indique que le CIN est traité comme une chaîne de caractères (string)
$stmt->bind_param("s", $_SESSION["CIN"]); 
$stmt->execute();
$result = $stmt->get_result();
$etudiant = $result->fetch_assoc();

// Fermer le statement
$stmt->close();

// Si l'étudiant n'a pas de photo, on lui donne l'image par défaut
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Infos - Smart Learning</title>
</head>
<body>
    <div class="dashboard-container">
        <main class="main-content">
            <h1>Mes Informations Personnelles</h1>
            
            <form action="traitement_infos.php" method="POST" enctype="multipart/form-data" class="form-profil">
                
                

                <label>Nom :</label>
                <input type="text" name="NOM" value="<?php echo htmlspecialchars($etudiant['nom']); ?>" required>

                <label>Prénom :</label>
                <input type="text" name="PRENOM" value="<?php echo htmlspecialchars($etudiant['prenom']); ?>" required>

                <label>CIN :</label>
                <input type="text" name="CIN" value="<?php echo htmlspecialchars($etudiant['CIN']); ?>" required>
                <label>Mot de passe:</label>
                <input type="password" name="PASSWORDN" value="<?php echo htmlspecialchars($etudiant['CIN']); ?>" required>

                <hr>

                <h3>Confirmation de sécurité</h3>
                <p>Pour appliquer les modifications, veuillez saisir votre mot de passe actuel.</p>
                <input type="password" name="PASSWORD" required placeholder="Mot de passe actuel">

                <button type="submit" name="modifier_infos" class="btn-primary">Enregistrer les modifications</button>
            </form>
        </main>
    </div>
</body>
</html>