<?php

declare(strict_types=1);

$active = (string) ($active ?? '');
$isEnglish = function_exists('current_language') && current_language() === 'en';

$items = [
    'cours' => [
        'href' => 'cours.php',
        'label' => $isEnglish ? 'My Courses' : 'Mes Cours',
    ],
    'taches' => [
        'href' => 'tache_a_fair.php',
        'label' => $isEnglish ? 'My Tasks' : 'Mes Taches',
    ],
    'offres' => [
        'href' => 'offres.php',
        'label' => $isEnglish ? 'Offers' : 'Offres',
    ],
    'reclamation' => [
        'href' => 'reclamation.php',
        'label' => $isEnglish ? 'Support' : 'Reclamation',
    ],
    'certificat' => [
        'href' => 'certificat.php',
        'label' => $isEnglish ? 'My Certificates' : 'Mes certificats',
    ],
];

$profileSrc = null;
$profileName = null;
$profileInitials = 'U';

try {
    if (function_exists('is_authenticated') && is_authenticated()) {
        $navPdo = db();
        $navStmt = $navPdo->prepare('SELECT prenom, nom, data, type FROM etudiant WHERE CIN = :cin LIMIT 1');
        $navStmt->execute(['cin' => user_id()]);
        $navUser = $navStmt->fetch(\PDO::FETCH_ASSOC);

        if (is_array($navUser)) {
            $firstName = trim((string) ($navUser['prenom'] ?? ''));
            $lastName = trim((string) ($navUser['nom'] ?? ''));
            $profileName = trim($firstName . ' ' . $lastName) ?: null;

            $substr = static function (string $value): string {
                if (function_exists('mb_substr')) {
                    return (string) mb_substr($value, 0, 1);
                }

                return substr($value, 0, 1);
            };

            $initialA = $firstName !== '' ? $substr($firstName) : '';
            $initialB = $lastName !== '' ? $substr($lastName) : '';
            $profileInitials = strtoupper($initialA . $initialB) ?: 'U';

            if (!empty($navUser['data']) && !empty($navUser['type'])) {
                $base64 = base64_encode($navUser['data']);
                $profileSrc = 'data:' . $navUser['type'] . ';base64,' . $base64;
            }
        }
    }
} catch (\Throwable $e) {
    // Ignore: navbar should still render without profile info.
}

?>
<header class="topbar" aria-label="<?php echo $isEnglish ? 'Main navigation' : 'Navigation principale'; ?>">
    <div class="container topbar-inner">
        <a class="brand" href="../index.php" aria-label="<?php echo $isEnglish ? 'Go to home page' : 'Back to home'; ?>">
            <img src="../media/logo.jpg" alt="Logo Enjah">
            <span><?php echo $isEnglish ? 'Student' : 'Etudiant'; ?></span>
        </a>

        <nav class="nav-links" aria-label="<?php echo $isEnglish ? 'Menu' : 'Menu'; ?>">
            <?php foreach ($items as $key => $item): ?>
                <a href="<?php echo htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo $active === $key ? 'is-active' : ''; ?>">
                    <span><?php echo $item['label']; ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="top-actions">
            <details class="profile-menu">
                <summary class="profile-trigger" aria-label="<?php echo $isEnglish ? 'Open profile menu' : 'Ouvrir le menu profil'; ?>" title="<?php echo htmlspecialchars((string) ($profileName ?? ($isEnglish ? 'Profile' : 'Profil')), ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if ($profileSrc): ?>
                        <img src="<?php echo htmlspecialchars($profileSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo $isEnglish ? 'Profile picture' : 'Photo de profil'; ?>" class="nav-avatar">
                    <?php else: ?>
                        <span class="profile-initials" aria-hidden="true"><?php echo htmlspecialchars($profileInitials, ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                </summary>
                <div class="profile-dropdown" role="menu" aria-label="<?php echo $isEnglish ? 'Profile menu' : 'Menu profil'; ?>">
                    <div class="profile-dropdown-header">
                        <div class="profile-dropdown-name"><?php echo htmlspecialchars((string) ($profileName ?? ($isEnglish ? 'My account' : 'Mon compte')), ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="profile-dropdown-sub"><?php echo $isEnglish ? 'Student' : 'Etudiant'; ?></div>
                    </div>
                    <a href="profil.php" role="menuitem"><?php echo $isEnglish ? 'View profile' : 'Voir profil'; ?></a>
                    <a href="profil.php#settings" role="menuitem"><?php echo $isEnglish ? 'Settings' : 'Parametres'; ?></a>
                    <a href="../backend/actions/logout.php" class="danger" role="menuitem"><?php echo $isEnglish ? 'Log out' : 'Se deconnecter'; ?></a>
                </div>
            </details>
        </div>
    </div>
</header>

<script>
// Auto-logout on page unload intentionally disabled.
// Session now closes only when user explicitly clicks "Se deconnecter".
</script>
