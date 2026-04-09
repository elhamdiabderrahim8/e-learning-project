<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur de connexion Etudiant - Enjah</title>
    <link rel="stylesheet" href="../../../professeur/style.css">
</head>
<body class="login-page">

    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="head">
                    <img src="../../../professeur/enjah.png" alt="logo" id="logo">
                    <div class="logo">Enjah</div>
                </div>
                <h1>Bon retour !</h1>
                <div class="login-subtitle-link">
                    <a href="../index.php">Espace Etudiant</a>
                </div>
                <p>Veuillez vous connecter pour acceder a vos cours.</p>
            </div>
            <div class="error">
                <img id="error" src="../../../professeur/icons8-erreur-48.png" alt="Erreur">
                <p><?php echo htmlspecialchars($_GET['msg'] ?? 'Une erreur est survenue.', ENT_QUOTES, 'UTF-8'); ?></p>
            </div>

            <form class="login-form" action="../backend/actions/login.php" method="POST">
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
                    Vous etes professeur ? <a href="../../../professeur/login.php">Login professeur</a>
                </div>
                <div class="login-footer login-footer-secondary">
                    <a href="../index.php">Retour vers l'accueil etudiant</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
