<?php
session_start();

if (isset($_SESSION['CIN'])) {
    header('Location: offres.php');
    exit();
}

$message = trim((string) ($_GET['msg'] ?? ''));
$success = isset($_GET['success']) && $_GET['success'] === '1';
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Enjah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box auth-box">
            <div class="login-header">
                <a href="login.php" class="logo-link">
                    <div class="logo">
                        <img src="enjah.png" alt="Logo Enjah" id="logo">
                        <span>Enjah</span>
                    </div>
                </a>
                <h1>Connexion</h1>
                <p>Connectez-vous pour acceder a votre espace d'apprentissage.</p>
                <p class="input-help auth-subtitle">Espace professeur</p>
            </div>

            <?php if ($message !== ''): ?>
                <div class="auth-message <?php echo $success ? 'auth-success' : 'auth-error'; ?>">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form class="login-form" action="login_action.php" method="POST">
                <div class="input-group">
                    <label for="CIN">CIN</label>
                    <input type="text" id="CIN" name="CIN" placeholder="13456789" inputmode="numeric" autocomplete="username" required>
                </div>

                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="********" autocomplete="current-password" required>
                </div>

                <button type="submit" class="btn-login">Se connecter</button>

                <div class="login-footer">
                    Pas encore de compte ? <a href="registre.php">S'inscrire gratuitement</a>
                    <div style="margin-top: 10px;">
                        Vous etes etudiant ? <a href="../kmr/student/pages/login.php">Connexion etudiant</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
