<?php
declare(strict_types=1);

session_start();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $CIN = trim((string) ($_POST['CIN'] ?? ''));
    $PASSWORD = (string) ($_POST['PASSWORD'] ?? '');

    try {
        $conn = new mysqli('localhost', 'root', '', 'elearning');
        if ($conn->connect_error) {
            throw new Exception('Impossible de se connecter au serveur.');
        }

        $stmt = $conn->prepare('SELECT * FROM professeur WHERE CIN = ?');
        $stmt->bind_param('i', $CIN);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($PASSWORD, $row['password'])) {
                session_regenerate_id(true);
                $_SESSION['CIN'] = $row['CIN'];
                $_SESSION['prenom'] = $row['prenom'];
                $_SESSION['nom'] = $row['nom'];
                header('Location: offres.php');
                exit();
            }

            $message = urlencode('Mot de passe incorrect. Veuillez reessayer.');
            header('Location: erreur_login.php?msg=' . $message);
            exit();
        }

        $message = urlencode('Aucun compte trouve avec ce CIN.');
        header('Location: erreur_login.php?msg=' . $message);
        exit();
    } catch (Exception $e) {
        $message = urlencode($e->getMessage());
        header('Location: erreur_login.php?msg=' . $message);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Learning</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="head">
                    <img src="enjah.png" alt="logo" id="logo">
                    <div class="logo">Enjah</div>
                </div>
                <h1>Bon retour !</h1>
                <div class="login-subtitle-link">
                    <a href="index.php">Espace Professeur</a>
                </div>
                <p>Veuillez vous connecter pour acceder a vos cours.</p>
            </div>

            <form class="login-form" action="login.php" method="POST">
                <div class="input-group">
                    <label for="CIN">CIN</label>
                    <input type="text" id="CIN" name="CIN" placeholder="13456789" required>
                </div>

                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="PASSWORD" placeholder="********" required>
                </div>

                <div class="login-options">
                    <label><input type="checkbox"> Se souvenir de moi</label>
                </div>

                <button type="submit" class="btn-login">Se connecter</button>

                <div class="login-footer">
                    Pas encore de compte ? <a href="registre.php">S'inscrire gratuitement</a>
                </div>
                <div class="login-footer login-footer-secondary">
                    Vous etes etudiant ? <a href="../kmr/student/pages/login.php">Login etudiant</a>
                </div>
                <div class="login-footer login-footer-secondary">
                    <a href="index.php">Retour vers l'accueil professeur</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
