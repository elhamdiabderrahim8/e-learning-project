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
        <body class="login-page">

    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="head">
                    <img src="enjah.png" alt="logo" id="logo">
                    <div class="logo">Enjah</div>
                </div>
                <h1>Bon retour !</h1>
                <p>Veuillez vous connecter pour accéder à vos cours.</p>
            </div>
            <div class="error">
        <img  id="error"src="icons8-erreur-48.png" alt="!!!">
        <p>
             <?php
            $msg = $_GET["msg"] ?? "Une erreur est survenue.";
            echo htmlspecialchars($msg);
        ?>
        </p>
    </div>

            <form class="login-form" action="login.php"method="POST">
                <div class="input-group">
                    <label for="CIN">CIN</label>
                    <input type="text" id="email"  name="CIN" placeholder="13456789" required>
                </div>

                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="PASSWORD" placeholder="••••••••" required>
                </div>

                <div class="login-options">
                    <label><input type="checkbox"> Se souvenir de moi</label>
                </div>

                <button type="submit" class="btn-login">Se connecter</button>

                <div class="login-footer">
                    Pas encore de compte ? <a href="registre.html">S'inscrire gratuitement</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>