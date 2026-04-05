<?php
$message = trim((string) ($_GET['msg'] ?? ''));
$success = isset($_GET['success']) && $_GET['success'] === '1';
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Enjah</title>
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
                <h1>Inscription</h1>
                <p>Creez votre compte pour acceder a l'espace professeur.</p>
                <p class="input-help auth-subtitle">Espace professeur</p>
            </div>

            <?php if ($message !== ''): ?>
                <div class="auth-message <?php echo $success ? 'auth-success' : 'auth-error'; ?>">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form class="register-form" action="register.php" method="POST">
                <div class="input-row">
                    <div class="input-group">
                        <label for="CIN">CIN</label>
                        <input type="text" id="CIN" name="CIN" placeholder="13456789" inputmode="numeric" autocomplete="username" required>
                    </div>
                    <div class="input-group">
                        <label for="prenom">Prenom</label>
                        <input type="text" id="prenom" name="prenom" placeholder="Jean" autocomplete="given-name" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="Dupont" autocomplete="family-name" required>
                </div>

                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="Minimum 8 caracteres" minlength="8" autocomplete="new-password" required>
                    <p class="input-help">Utilisez au moins 8 caracteres pour securiser votre compte.</p>
                </div>

                <div class="input-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="********" minlength="8" autocomplete="new-password" required>
                </div>

                <div class="terms">
                    <label>
                        <input type="checkbox" required> J'accepte les conditions d'utilisation.
                    </label>
                </div>

                <button type="submit" class="btn-login">S'inscrire</button>

                <div class="login-footer">
                    Vous avez deja un compte ? <a href="login.php">Se connecter</a>
                    <div style="margin-top: 10px;">
                        Vous etes etudiant ? <a href="../kmr/student/pages/registre.php">Inscription etudiant</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
