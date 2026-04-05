<?php

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();
$stmt = $pdo->prepare('SELECT CIN, nom, prenom FROM professeur WHERE CIN = :cin LIMIT 1');
$stmt->execute(['cin' => user_id()]);
$user = $stmt->fetch();

if (!$user) {
    set_flash('error', 'Utilisateur introuvable.');
    redirect('../backend/actions/logout.php');
}

$initials = strtoupper(substr((string) $user['nom'], 0, 1) . substr((string) $user['prenom'], 0, 1));
$fullName = trim((string) $user['nom'] . ' ' . (string) $user['prenom']);
$preferredLanguage = (string) ($_SESSION['preferred_language'] ?? 'fr');
if (!in_array($preferredLanguage, ['en', 'fr'], true)) {
    $preferredLanguage = 'fr';
}

$languageLabel = 'Francais';
$selectedEn = 'selected';
$selectedFr = '';
if ($preferredLanguage === 'fr') {
    $selectedEn = '';
    $selectedFr = 'selected';
} else {
    $languageLabel = 'English';
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
        <?php $active = 'profil'; require __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <h1>Mon Profil</h1>
                <p>Gerez vos informations personnelles.</p>
            </header>

            <?php if ($error): ?>
                <div class="profile-alert profile-alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="profile-alert profile-alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <section class="profile-shell">
                <article class="profile-hero profile-card">
                    <div class="profile-avatar"><?php echo htmlspecialchars($initials, ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="profile-hero-text">
                        <h2><?php echo htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'); ?></h2>
                        <p style="color: var(--muted); font-weight: 700;">CIN : <?php echo htmlspecialchars((string) $user['CIN'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="profile-badges">
                        <span class="profile-chip">Compte actif</span>
                        <span class="profile-chip profile-chip-soft">Espace professeur</span>
                    </div>
                </article>

                <article class="profile-details profile-card">
                    <h3>Informations du compte</h3>
                    <div class="profile-info">
                        <div class="info-row">
                            <span class="info-label">Prenom</span>
                            <span class="info-value"><?php echo htmlspecialchars((string) $user['prenom'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Nom</span>
                            <span class="info-value"><?php echo htmlspecialchars((string) $user['nom'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Langue</span>
                            <span class="info-value"><?php echo $languageLabel; ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Role</span>
                            <span class="info-value">Professeur</span>
                        </div>
                    </div>

                    <h3>Parametres</h3>
                    <form class="profile-settings-form" action="../backend/actions/update_profile_settings.php" method="post">
                        <div class="input-row">
                            <div class="input-group">
                                <label for="first_name">Prenom</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars((string) $user['prenom'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="input-group">
                                <label for="last_name">Nom</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars((string) $user['nom'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="preferred_language">Langue du site</label>
                            <select id="preferred_language" name="preferred_language">
                                <option value="en" <?php echo $selectedEn; ?>>English</option>
                                <option value="fr" <?php echo $selectedFr; ?>>Francais</option>
                            </select>
                        </div>

                        <input type="submit" class="btn-primary profile-settings-btn" value="Enregistrer les parametres">
                    </form>

                    <div class="profile-actions">
                        <a href="../backend/actions/logout.php" class="btn-logout">Se deconnecter</a>
                        <form action="../backend/actions/delete_profile.php" method="post" onsubmit="var answer = prompt('Tapez OUI pour confirmer la suppression du profil', ''); if (answer == null) { return false; } answer = answer.trim().toUpperCase(); if (answer != 'OUI') { alert('Suppression annulee.'); return false; } return true;">
                            <input type="submit" class="btn-danger" value="Supprimer le profil">
                        </form>
                    </div>
                </article>
            </section>
        </main>
    </div>

    </body>
</html>



