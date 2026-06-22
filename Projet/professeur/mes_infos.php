<?php
require_once __DIR__ . '/session_prof.php';
if (empty($_SESSION["CIN"])) {
    header("Location: login.html");
    exit();
}

// 1. Connexion à la base de données
require_once __DIR__ . '/../student/database/database.php';
$pdo = db();

// 2. Récupérer les infos de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM etudiant WHERE CIN = :cin");
$stmt->execute(['cin' => $_SESSION["CIN"]]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

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