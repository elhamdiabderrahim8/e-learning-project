<?php

declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();
$stmt = $pdo->prepare('SELECT id, first_name, last_name, email, created_at FROM users WHERE id = :user_id');
$stmt->execute(['user_id' => user_id()]);
$user = $stmt->fetch();

$error = get_flash('error');
$success = get_flash('success');
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Enjah</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../media/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../media/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../media/favicon_io/favicon-16x16.png">
    <link rel="shortcut icon" href="../media/favicon_io/favicon.ico">
    <link rel="manifest" href="../media/favicon_io/site.webmanifest">
</head>
<body>

    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo"><img src="../media/logo.jpg" alt="Logo Enjah"><span>Enjah</span></div>
            <nav>
                <ul>
                    <li><a href="cours.php">Mes Cours</a></li>
                    <li><a href="tache_a_fair.php">Mes Taches</a></li>
                    <li><a href="offres.php">Choisir une offre</a></li>
                    <li><a href="calendrier.php">Calendrier</a></li>
                    <li><a href="certificats.php">Certificats</a></li>
                    <li><a href="reclamation.php">Reclamation</a></li>
                    <li class="active"><a href="profil.php">Mon Profil</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <h1>Mon Profil</h1>
                <p>Gerez vos informations personnelles.</p>
            </header>

            <?php if ($error): ?>
                <p class="auth-message auth-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="auth-message auth-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar"><?php echo htmlspecialchars(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1), ENT_QUOTES, 'UTF-8'); ?></div>
                    <h2><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                </div>

                <div class="profile-info">
                    <div class="info-row">
                        <span class="info-label">Prenom</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['first_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nom</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['last_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Membre depuis</span>
                        <span class="info-value">
                            <?php 
                            $createdDate = new DateTime($user['created_at']);
                            echo htmlspecialchars($createdDate->format('d F Y'), ENT_QUOTES, 'UTF-8');
                            ?>
                        </span>
                    </div>
                </div>

                <div class="profile-actions">
                    <a href="../backend/actions/logout.php" class="btn-logout">Se deconnecter</a>
                </div>
            </div>
        </main>
    </div>

    </body>
</html>



