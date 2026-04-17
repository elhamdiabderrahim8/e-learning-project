<?php
session_start();

$adminUsers = require __DIR__ . '/admin_credentials.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $matchedAdmin = null;
    foreach ($adminUsers as $adminUser) {
        $registeredEmail = strtolower(trim((string) ($adminUser['email'] ?? '')));
        if ($registeredEmail !== '' && $registeredEmail === strtolower($email)) {
            $matchedAdmin = $adminUser;
            break;
        }
    }

    if ($matchedAdmin === null) {
        $error = 'Email admin introuvable.';
    } elseif (!password_verify($password, (string) ($matchedAdmin['password_hash'] ?? ''))) {
        $error = 'Mot de passe incorrect.';
    } else {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email']     = $matchedAdmin['email'];
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - Enjah</title>
    <link rel="stylesheet" href="../professeur/nouvel.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        body { display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .login-box { width:100%; max-width:400px; }
        .login-box h2 { margin-bottom:24px; text-align:center; font-size:1.5rem; }
        .login-box label { display:block; margin-bottom:6px; font-size:.9rem; }
        .login-box input[type=email], .login-box input[type=password] { margin-bottom:16px; }
        .login-box button { width:100%; }
        .error { margin-bottom:16px; font-size:.9rem; }
        .logo { text-align:center; margin-bottom:20px; }
        .logo span { font-weight:700; font-size:1.2rem; margin-left:8px; vertical-align:middle; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <img src="../professeur/enjah.png" alt="Enjah">
            <span>Admin Panel</span>
        </div>
        <h2>Connexion Administrateur</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <label for="email">Email admin</label>
            <input type="email" id="email" name="email" placeholder="admin@enjah.com" required>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
