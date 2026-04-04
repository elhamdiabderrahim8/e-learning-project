<?php

declare(strict_types=1);

$active = (string) ($active ?? '');

$items = [
    'cours' => [
        'href' => 'cours.php',
        'icon' => '&#8962;',
        'label' => 'Mes Cours',
    ],
    'taches' => [
        'href' => 'tache_a_fair.php',
        'icon' => '&#128221;',
        'label' => 'Mes T&acirc;ches',
    ],
    'offres' => [
        'href' => 'offres.php',
        'icon' => '&#9671;',
        'label' => 'Offres',
    ],
    'certificats' => [
        'href' => 'certificat.php',
        'icon' => '&#127891;',
        'label' => 'Certificats',
    ],
    'reclamation' => [
        'href' => 'reclamation.php',
        'icon' => '&#128172;',
        'label' => 'R&eacute;clamation',
    ],
    'support' => [
        'href' => 'support.php',
        'icon' => '&#128172;',
        'label' => 'Support',
    ],
    'profil' => [
        'href' => 'profil.php',
        'icon' => '&#128100;',
        'label' => 'Mon Profil',
    ],
];

?>
<aside class="sidebar" aria-label="Navigation principale">
    <a class="logo" href="../index.php">
        <img src="../media/logo.jpg" alt="Logo Enjah">
        <span>Enjah</span>
    </a>

    <nav>
        <ul>
            <?php foreach ($items as $key => $item): ?>
                <li class="<?php echo $active === $key ? 'active' : ''; ?>">
                    <a href="<?php echo htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>">
                        <span class="nav-icon"><?php echo $item['icon']; ?></span>
                        <span><?php echo $item['label']; ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
            <li>
                <a href="../backend/actions/logout.php">
                    <span class="nav-icon">&#128682;</span>
                    <span>Se d&eacute;connecter</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

