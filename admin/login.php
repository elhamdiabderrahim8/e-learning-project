<?php
session_start();

// Admin credentials (change these as needed)
define('ADMIN_EMAIL', 'admin@enjah.com');
define('ADMIN_PASSWORD', 'admin123');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email']     = $email;
        header('Location: index.php');
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
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
    <style>
        body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:#f0f4f8; }
        .login-box { background:#fff; padding:40px; border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,.12); width:100%; max-width:400px; }
        .login-box h2 { margin-bottom:24px; color:#2d3748; text-align:center; font-size:1.5rem; }
        .login-box label { display:block; font-weight:600; color:#4a5568; margin-bottom:6px; font-size:.9rem; }
        .login-box input[type=email], .login-box input[type=password] {
            width:100%; padding:10px 14px; border:1px solid #cbd5e0; border-radius:8px;
            font-size:1rem; margin-bottom:16px; box-sizing:border-box;
        }
        .login-box button { width:100%; padding:12px; background:#4f46e5; color:#fff; border:none;
            border-radius:8px; font-size:1rem; font-weight:600; cursor:pointer; }
        .login-box button:hover { background:#4338ca; }
        .error { background:#fed7d7; color:#c53030; padding:10px 14px; border-radius:8px; margin-bottom:16px; font-size:.9rem; }
        .logo { text-align:center; margin-bottom:20px; }
        .logo img { height:36px; }
        .logo span { font-weight:700; font-size:1.2rem; color:#4f46e5; margin-left:8px; vertical-align:middle; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <img src="../professeur/enjah.png" alt="Enjah">
            <span>Admin Panel</span>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin:-4px 0 18px;">
            <a href="../index.php" style="color:#4f46e5;text-decoration:none;font-weight:600;">Hub projet</a>
            <a href="../professeur/index.php" style="color:#4f46e5;text-decoration:none;font-weight:600;">Professeur</a>
            <a href="../kmr/student/home.php" style="color:#4f46e5;text-decoration:none;font-weight:600;">Etudiant</a>
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
