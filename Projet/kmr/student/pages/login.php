<?php
require_once __DIR__ . '/../backend/includes/bootstrap.php';

if (is_authenticated()) {
    redirect('cours.php');
}

$error = get_flash('error');
$success = get_flash('success');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Etudiant - Enjah</title>
    <link rel="stylesheet" href="../style_log.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../media/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../media/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../media/favicon_io/favicon-16x16.png">
</head>
<body class="login-page">

    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <a href="../index.php" class="logo-link">
                    <div class="logo">
                        <img src="../media/logo.jpg" alt="Logo Enjah">
                        <span>Enjah</span>
                    </div>
                </a>
                <h1>Connexion</h1>
                <p>Connectez-vous pour acceder a votre espace d'apprentissage.</p>
                <p class="input-help" style="margin-top: 8px;">Espace etudiant</p>
            </div>

            <?php if ($error): ?>
                <div class="auth-message auth-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="auth-message auth-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form class="login-form" action="../backend/actions/login.php" method="post">
                <div class="input-group">
                    <label for="CIN">CIN</label>
                    <input type="text" id="CIN" name="CIN" placeholder="13456789" inputmode="numeric" autocomplete="username" required>
                </div>

                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="********" autocomplete="current-password" required>
                </div>

                <input type="submit" class="btn-login" value="Se connecter">

                <div class="login-footer">
                    Pas encore de compte ? <a href="registre.php">S'inscrire gratuitement</a><br>
                    ou <a href="invité(a).php">Mode invité</a>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #f1f5f9;">
                        Vous etes professeur ? <a href="../../../professeur/login.php">Connexion professeur</a>
                    </div>
                    <div style="margin-top: 10px;">
                        <a href="../index.php">Retour vers l'accueil etudiant</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
