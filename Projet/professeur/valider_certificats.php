<?php
session_start();
require_once __DIR__ . '/../kmr/student/backend/config/database.php';

$pdo = db();

if (!isset($_SESSION['CIN'])) {
    header('Location: login.html');
    exit();
}

$id_prof_connecte = (string) $_SESSION['CIN'];
$profileName = trim((string) (($_SESSION['nom'] ?? '') . ' ' . ($_SESSION['prenom'] ?? '')));
$profileSrc = 'profil.avif';

try {
    $stmtProf = $pdo->prepare('SELECT nom, prenom, data, type FROM professeur WHERE CIN = :cin LIMIT 1');
    $stmtProf->execute(['cin' => $id_prof_connecte]);
    $prof = $stmtProf->fetch(PDO::FETCH_ASSOC) ?: [];

    if ($profileName === '') {
        $profileName = trim((string) (($prof['nom'] ?? '') . ' ' . ($prof['prenom'] ?? '')));
    }

    if (!empty($prof['data']) && !empty($prof['type'])) {
        $profileSrc = 'data:' . $prof['type'] . ';base64,' . base64_encode($prof['data']);
    }
} catch (Throwable $e) {
    // Keep fallback profile values.
}

$sql = "SELECT
            e.nom AS nom_etudiant,
            e.prenom,
            c.nom_cours AS nom_cours,
            c.id AS id_cours,
            e.CIN AS cin_etudiant
        FROM inscription i
        JOIN etudiant e ON i.id_etudiant = e.CIN
        JOIN cours c ON i.id_cours = c.id
        LEFT JOIN certificaton cert ON (cert.id_etudiant = e.CIN AND cert.id_cours = c.id)
        WHERE c.id_professeur = :id_prof
          AND i.progression = 100
          AND cert.id_certificat IS NULL
        ORDER BY c.id DESC, e.nom ASC, e.prenom ASC";

try {
    $query = $pdo->prepare($sql);
    $query->execute(['id_prof' => $id_prof_connecte]);
    $alertes = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erreur SQL : ' . $e->getMessage());
}

$totalAlertes = count($alertes);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation des certificats - ENJAH</title>
    <link rel="stylesheet" href="nouvel.css">
    <link rel="stylesheet" href="valider_certificats.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="topbar">
            <div class="container topbar-inner">
                <a class="brand" href="index.php" aria-label="Accueil professeur">
                    <img src="enjah.png" alt="Logo Enjah">
                    <span>Professeur</span>
                </a>

                <nav class="nav-links" aria-label="Navigation professeur">
                    <a href="offres.php">Vos Cours</a>
                    <a href="valider_certificats.php" class="is-active">Certificats</a>
                    <a href="reclamation.php">Reclamation</a>
                    <a href="infos.php">Mes Infos</a>
                </nav>

                <div class="top-actions">
                    <details class="profile-menu">
                        <summary class="profile-trigger" aria-label="Ouvrir le menu profil" title="Mon Profil">
                            <img src="<?php echo htmlspecialchars($profileSrc, ENT_QUOTES, 'UTF-8'); ?>" class="nav-avatar" alt="Profil">
                        </summary>
                        <div class="profile-dropdown" role="menu" aria-label="Menu profil">
                            <div class="profile-dropdown-header">
                                <div class="profile-dropdown-name"><?php echo htmlspecialchars($profileName !== '' ? $profileName : 'Professeur', ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="profile-dropdown-sub">Professeur</div>
                            </div>
                            <a href="infos.php" role="menuitem">Voir profil</a>
                            <a href="infos.php" role="menuitem">Mes infos</a>
                            <a href="logout.php" class="danger" role="menuitem">Se deconnecter</a>
                        </div>
                    </details>
                </div>
            </div>
        </header>

        <main class="main-content validation-page">
            <section class="content-header validation-hero">
                <div>
                    <span class="eyebrow">Validation academique</span>
                    <h1>Certificats en attente</h1>
                    <p>Retrouvez ici les apprenants ayant termine leurs cours et signez leurs certificats depuis une interface plus claire.</p>
                </div>
                <div class="hero-badge">
                    <strong><?php echo $totalAlertes; ?></strong>
                    <span><?php echo $totalAlertes > 1 ? 'certificats a traiter' : 'certificat a traiter'; ?></span>
                </div>
            </section>

            <section class="stats-strip" aria-label="Resume des validations">
                <article class="stat-card">
                    <span class="stat-label">Demandes en attente</span>
                    <strong class="stat-value"><?php echo $totalAlertes; ?></strong>
                </article>
                <article class="stat-card">
                    <span class="stat-label">Action principale</span>
                    <strong class="stat-value stat-copy">Verifier puis signer</strong>
                </article>
                <article class="stat-card">
                    <span class="stat-label">Etat</span>
                    <strong class="stat-value <?php echo $totalAlertes > 0 ? 'state-pending' : 'state-clear'; ?>"><?php echo $totalAlertes > 0 ? 'Des validations vous attendent' : 'Tout est a jour'; ?></strong>
                </article>
            </section>

            <?php if ($totalAlertes > 0): ?>
                <section class="validation-grid">
                    <?php foreach ($alertes as $alerte): ?>
                        <article class="validation-card">
                            <div class="validation-card-top">
                                <span class="request-pill">Nouvelle demande</span>
                                <span class="course-chip"><?php echo htmlspecialchars((string) $alerte['nom_cours'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>

                            <h2><?php echo htmlspecialchars((string) ($alerte['nom_etudiant'] . ' ' . $alerte['prenom']), ENT_QUOTES, 'UTF-8'); ?></h2>
                            <p class="student-meta">CIN : <strong><?php echo htmlspecialchars((string) $alerte['cin_etudiant'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
                            <p class="card-description">Cet etudiant a termine le cours et peut maintenant recevoir son certificat signe.</p>

                            <dl class="card-details">
                                <div>
                                    <dt>Cours</dt>
                                    <dd><?php echo htmlspecialchars((string) $alerte['nom_cours'], ENT_QUOTES, 'UTF-8'); ?></dd>
                                </div>
                                <div>
                                    <dt>Statut</dt>
                                    <dd>Pret pour validation</dd>
                                </div>
                            </dl>

                            <form action="traitter_validation.php" method="POST" class="validation-form">
                                <input type="hidden" name="id_etudiant" value="<?php echo htmlspecialchars((string) $alerte['cin_etudiant'], ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="id_cours" value="<?php echo (int) $alerte['id_cours']; ?>">
                                <button type="submit" class="btn-primary validate-btn">Valider et signer le certificat</button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </section>
            <?php else: ?>
                <section class="empty-state">
                    <div class="empty-icon">OK</div>
                    <h2>Aucun certificat en attente</h2>
                    <p>Toutes les demandes ont ete traitees pour le moment. Revenez plus tard lorsque des cours seront completes.</p>
                    <a href="offres.php" class="btn-primary empty-link">Voir vos cours</a>
                </section>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
