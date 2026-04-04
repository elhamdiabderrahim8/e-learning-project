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
    <title>Connexion - Enjah</title>
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
                <h1>Connexion</h1>
                <p>Connectez-vous pour acceder a votre espace d'apprentissage.</p>
                <p class="input-help" style="margin-top: 8px;">Espace etudiant</p>
            </div>

            <?php if ($error): ?>
                <p style="color: #b42318; margin-bottom: 10px;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p style="color: #027a48; margin-bottom: 10px;"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <form class="login-form" action="../backend/actions/login.php" method="post">
                <div class="input-group">
                    <label for="CIN">CIN</label>
                    <input type="text" id="CIN" name="CIN" placeholder="13456789" inputmode="numeric" autocomplete="username" required>
                </div>

                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                </div>

                <input type="submit" class="btn-login" value="Se connecter">

                <div class="login-footer">
                    Pas encore de compte ? <a href="registre.php">S'inscrire gratuitement</a><br>
                    or <a href="invité(a).php">mode invité</a>
                    <div style="margin-top: 10px;">
                        Vous etes professeur ? <a href="../../professeur/pages/login.php">Connexion professeur</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    </body>
</html>


