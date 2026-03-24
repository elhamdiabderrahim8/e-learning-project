<?php

declare(strict_types=1);

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
    <title>Login - Smart Learning</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="login-page">

    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="logo">Smart Learning</div>
                <h1>Bon retour !</h1>
                <p>Veuillez vous connecter pour acceder a vos cours.</p>
            </div>

            <?php if ($error): ?>
                <p style="color: #b42318; margin-bottom: 10px;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p style="color: #027a48; margin-bottom: 10px;"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <form class="login-form" action="../backend/actions/login.php" method="post">
                <div class="input-group">
                    <label for="email">Adresse Email</label>
                    <input type="email" id="email" name="email" placeholder="nom@exemple.com" required>
                </div>

                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                </div>

                <button type="submit" class="btn-login">Se connecter</button>

                <div class="login-footer">
                    Pas encore de compte ? <a href="registre.php">S'inscrire gratuitement</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
