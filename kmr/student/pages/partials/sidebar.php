<?php

declare(strict_types=1);

$active = (string) ($active ?? '');

$items = [
    'cours' => [
        'href' => 'cours.php',
        'label' => 'Mes Cours',
    ],
    'taches' => [
        'href' => 'tache_a_fair.php',
        'label' => 'Mes T&acirc;ches',
    ],
    'offres' => [
        'href' => 'offres.php',
        'label' => 'Offres',
    ],
    'reclamation' => [
        'href' => 'reclamation.php',
        'label' => 'R&eacute;clamation',
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
<header class="topnav" aria-label="Navigation principale">
    <div class="topnav-inner">
        <a class="logo topnav-logo" href="../index.php" aria-label="Aller à l'accueil">
            <img src="../media/logo.jpg" alt="Logo Enjah">
            <span>Enjah</span>
        </a>

        <nav class="topnav-nav" aria-label="Menu">
            <ul class="topnav-links">
                <?php foreach ($items as $key => $item): ?>
                    <li class="<?php echo $active === $key ? 'active' : ''; ?>">
                        <a href="<?php echo htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>">
                            <span><?php echo $item['label']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <details class="profile-menu">
            <summary class="profile-trigger" aria-label="Ouvrir le menu profil" title="<?php echo htmlspecialchars((string) ($profileName ?? 'Profil'), ENT_QUOTES, 'UTF-8'); ?>">
                <?php if ($profileSrc): ?>
                    <img src="<?php echo htmlspecialchars($profileSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="Photo de profil">
                <?php else: ?>
                    <span class="profile-initials" aria-hidden="true"><?php echo htmlspecialchars($profileInitials, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
            </summary>
            <div class="profile-dropdown" role="menu" aria-label="Menu profil">
                <div class="profile-dropdown-header">
                    <div class="profile-dropdown-name"><?php echo htmlspecialchars((string) ($profileName ?? 'Mon compte'), ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="profile-dropdown-sub">Étudiant</div>
                </div>
                <a href="profil.php" role="menuitem">Voir profil</a>
                <a href="profil.php#settings" role="menuitem">Paramètres</a>
                <a href="../backend/actions/logout.php" class="danger" role="menuitem">Se d&eacute;connecter</a>
            </div>
        </details>
    </div>
</header>
