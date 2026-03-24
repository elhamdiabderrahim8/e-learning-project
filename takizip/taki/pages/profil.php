<?php

declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS preferred_language VARCHAR(10) NOT NULL DEFAULT 'en'");
} catch (Throwable $e) {
    // Ignore if DB permissions restrict alter operations.
}

$stmt = $pdo->prepare('SELECT id, first_name, last_name, email, created_at, preferred_language FROM users WHERE id = :user_id');
$stmt->execute(['user_id' => user_id()]);
$user = $stmt->fetch();

if (!$user) {
    set_flash('error', 'Utilisateur introuvable.');
    redirect('../backend/actions/logout.php');
}

$createdDate = new DateTime((string) $user['created_at']);
$memberSince = $createdDate->format('d F Y');
$initials = strtoupper(substr((string) $user['first_name'], 0, 1) . substr((string) $user['last_name'], 0, 1));
$fullName = trim((string) $user['first_name'] . ' ' . (string) $user['last_name']);
$preferredLanguage = (string) ($user['preferred_language'] ?? 'en');
if (!in_array($preferredLanguage, ['en', 'fr'], true)) {
    $preferredLanguage = 'en';
}

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
                <div class="profile-alert profile-alert-error" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="profile-alert profile-alert-success" role="status"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <section class="profile-shell">
                <article class="profile-hero profile-card">
                    <div class="profile-avatar"><?php echo htmlspecialchars($initials, ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="profile-hero-text">
                        <h2><?php echo htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'); ?></h2>
                        <p><?php echo htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="profile-badges">
                        <span class="profile-chip">Compte actif</span>
                        <span class="profile-chip profile-chip-soft">Membre depuis <?php echo htmlspecialchars($memberSince, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </article>

                <article class="profile-details profile-card">
                    <h3>Informations du compte</h3>
                    <div class="profile-info">
                        <div class="info-row">
                            <span class="info-label">Prenom</span>
                            <span class="info-value"><?php echo htmlspecialchars((string) $user['first_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Nom</span>
                            <span class="info-value"><?php echo htmlspecialchars((string) $user['last_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Adresse email</span>
                            <span class="info-value"><?php echo htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Langue</span>
                            <span class="info-value"><?php echo $preferredLanguage === 'fr' ? 'Francais' : 'English'; ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Membre depuis</span>
                            <span class="info-value"><?php echo htmlspecialchars($memberSince, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>

                    <h3>Parametres</h3>
                    <form class="profile-settings-form" action="../backend/actions/update_profile_settings.php" method="post">
                        <div class="input-row">
                            <div class="input-group">
                                <label for="first_name">Prenom</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars((string) $user['first_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="input-group">
                                <label for="last_name">Nom</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars((string) $user['last_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="preferred_language">Langue du site</label>
                            <select id="preferred_language" name="preferred_language">
                                <option value="en" <?php echo $preferredLanguage === 'en' ? 'selected' : ''; ?>>English (default)</option>
                                <option value="fr" <?php echo $preferredLanguage === 'fr' ? 'selected' : ''; ?>>Francais</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-primary profile-settings-btn">Enregistrer les parametres</button>
                    </form>

                    <div class="profile-actions">
                        <a href="../backend/actions/logout.php" class="btn-logout">Se deconnecter</a>
                        <form action="../backend/actions/delete_profile.php" method="post" onsubmit="return confirm('Voulez-vous vraiment supprimer votre profil ? Cette action est definitive.');">
                            <button type="submit" class="btn-danger">Supprimer le profil</button>
                        </form>
                    </div>
                </article>
            </section>
        </main>
    </div>

    </body>
</html>



