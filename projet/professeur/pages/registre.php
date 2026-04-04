<?php

require_once __DIR__ . '/../backend/includes/bootstrap.php';

if (is_authenticated()) {
    redirect('cours.php');
}

$error = get_flash('error');
$success = get_flash('success');
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Enjah</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../media/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../media/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../media/favicon_io/favicon-16x16.png">
    <link rel="shortcut icon" href="../media/favicon_io/favicon.ico">
    <link rel="manifest" href="../media/favicon_io/site.webmanifest">
</head>
<body class="login-page">

    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <a href="../index.php" class="logo-link">
                    <div class="logo"><img src="../media/logo.jpg" alt="Logo Enjah"><span>Enjah</span></div>
                </a>
                <h1>Inscription</h1>
                <p>Creez votre compte pour commencer votre parcours d'apprentissage.</p>
                <p class="input-help" style="margin-top: 8px;">Espace professeur</p>
            </div>

            <?php if ($error): ?>
                <p class="auth-message auth-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="auth-message auth-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <form class="register-form" action="../backend/actions/register.php" method="post">
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
                    <label for="confirm-password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="********" minlength="8" autocomplete="new-password" required>
                </div>

                <div class="terms">
                    <label>
                        <input type="checkbox" required> J'accepte les conditions d'utilisation.
                    </label>
                </div>

                <input type="submit" class="btn-login" value="S'inscrire">

                <div class="login-footer">
                        Vous avez deja un compte ? <a href="login.php">Se connecter</a>
                        <div style="margin-top: 10px;">
                            Vous etes etudiant ? <a href="../../taki/pages/registre.php">Inscription etudiant</a>
                        </div>
                </div>
            </form>
        </div>
    </div>

    </body>
</html>


