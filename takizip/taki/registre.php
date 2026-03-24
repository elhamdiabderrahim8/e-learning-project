<?php

declare(strict_types=1);

require_once __DIR__ . '/backend/includes/bootstrap.php';

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
    <title>Inscription - Smart Learning</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="logo">Smart Learning</div>
                <h1>Creer un compte</h1>
                <p>Rejoignez la communaute et commencez a apprendre.</p>
            </div>

            <?php if ($error): ?>
                <p style="color: #b42318; margin-bottom: 10px;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p style="color: #027a48; margin-bottom: 10px;"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <form class="register-form" action="backend/actions/register.php" method="post">
                <div class="input-row">
                    <div class="input-group">
                        <label for="firstname">Prenom</label>
                        <input type="text" id="firstname" name="firstname" placeholder="Jean" required>
                    </div>
                    <div class="input-group">
                        <label for="lastname">Nom</label>
                        <input type="text" id="lastname" name="lastname" placeholder="Dupont" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="email">Adresse Email</label>
                    <input type="email" id="email" name="email" placeholder="jean.dupont@exemple.com" required>
                </div>

                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="Minimum 8 caracteres" required>
                </div>

                <div class="input-group">
                    <label for="confirm-password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="********" required>
                </div>

                <button type="submit" class="btn-login">Creer mon compte</button>

                <div class="login-footer">
                    Deja inscrit ? <a href="login.php">Se connecter</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
