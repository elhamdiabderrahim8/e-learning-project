<?php
session_start();
require_once __DIR__ . '/../kmr/student/backend/config/database.php';
require_once __DIR__ . '/../kmr/student/backend/includes/migrate_support_tables.php';

if (!isset($_SESSION['CIN'])) {
    header('Location: login.html');
    exit();
}

$pdo = db();
$cin = (string) $_SESSION['CIN'];
$profileName = trim((string) (($_SESSION['nom'] ?? '') . ' ' . ($_SESSION['prenom'] ?? '')));
$profileSrc = 'profil.avif';

try {
    $stmtProf = $pdo->prepare('SELECT nom, prenom, data, type FROM professeur WHERE CIN = :cin LIMIT 1');
    $stmtProf->execute(['cin' => $cin]);
    $prof = $stmtProf->fetch(PDO::FETCH_ASSOC) ?: [];

    if ($profileName === '') {
        $profileName = trim((string) (($prof['nom'] ?? '') . ' ' . ($prof['prenom'] ?? '')));
    }

    if (!empty($prof['data']) && !empty($prof['type'])) {
        $profileSrc = 'data:' . $prof['type'] . ';base64,' . base64_encode($prof['data']);
    }
} catch (Throwable $e) {
    // Keep fallback profile values if loading the avatar fails.
}

$flash = $_SESSION['prof_reclamation_flash'] ?? null;
unset($_SESSION['prof_reclamation_flash']);

$error = null;
$success = null;
if (is_array($flash)) {
    if (($flash['type'] ?? '') === 'error') {
        $error = (string) ($flash['message'] ?? '');
    } elseif (($flash['type'] ?? '') === 'success') {
        $success = (string) ($flash['message'] ?? '');
    }
}

$threads = [];
$loadError = null;

try {
    migrate_support_tables();
    $stmt = $pdo->prepare(
        "SELECT
            t.id,
            t.subject,
            t.created_at,
            (
                SELECT sm.message
                FROM support_messages sm
                WHERE sm.thread_id = t.id AND sm.sender = 'professeur'
                ORDER BY sm.created_at ASC, sm.id ASC
                LIMIT 1
            ) AS user_message,
            (
                SELECT sm.message
                FROM support_messages sm
                WHERE sm.thread_id = t.id AND sm.sender = 'admin'
                ORDER BY sm.created_at DESC, sm.id DESC
                LIMIT 1
            ) AS admin_reply,
            (
                SELECT sm.created_at
                FROM support_messages sm
                WHERE sm.thread_id = t.id AND sm.sender = 'admin'
                ORDER BY sm.created_at DESC, sm.id DESC
                LIMIT 1
            ) AS admin_reply_at
         FROM support_threads t
         WHERE t.user_id = :user_id AND t.user_type = 'professeur'
         ORDER BY t.created_at DESC, t.id DESC
         LIMIT 20"
    );
    $stmt->execute(['user_id' => $cin]);
    $threads = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Throwable $e) {
    $loadError = 'Impossible de charger vos reclamations pour le moment.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reclamation - ENJAH</title>
    <link rel="stylesheet" href="nouvel.css">
    <link rel="stylesheet" href="reclamation.css">
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
                    <a href="valider_certificats.php">Certificats</a>
                    <a href="reclamation.php" class="is-active">Reclamation</a>
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

        <main class="main-content reclamation-page">
            <section class="content-header reclamation-hero">
                <div>
                    <span class="eyebrow">Support professeur</span>
                    <h1>Envoyer une reclamation a l'admin</h1>
                    <p>Expliquez votre probleme, posez votre question ou signalez un blocage. Votre message sera enregistre et transmis directement a l'administration.</p>
                </div>
                <div class="hero-note">
                    <strong>Canal direct</strong>
                    <span>Suivi de vos demandes et reponses admin</span>
                </div>
            </section>

            <?php if ($error): ?>
                <div class="reclamation-alert reclamation-alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="reclamation-alert reclamation-alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <section class="reclamation-layout">
                <aside class="support-info-card">
                    <h2>Avant d'envoyer</h2>
                    <ul>
                        <li>Choisissez un sujet clair pour accelerer le traitement.</li>
                        <li>Decrivez le probleme avec le plus de contexte possible.</li>
                        <li>Consultez plus bas l'historique de vos reclamations.</li>
                    </ul>
                    <div class="support-meta">
                        <span class="meta-badge">Admin support</span>
                        <span class="meta-badge meta-soft">Espace professeur</span>
                    </div>
                </aside>

                <section class="support-panel-page">
                    <div class="support-start-card">
                        <form method="post" action="create_reclamation.php">
                            <label for="subject">Sujet de la reclamation</label>
                            <select id="subject" name="subject" required>
                                <option value="Probleme d acces au cours">Probleme d acces au cours</option>
                                <option value="Probleme avec un etudiant">Probleme avec un etudiant</option>
                                <option value="Bug technique sur la plateforme">Bug technique sur la plateforme</option>
                                <option value="Paiement / Facturation">Paiement / Facturation</option>
                                <option value="Autre">Autre</option>
                            </select>

                            <label for="message">Votre message</label>
                            <textarea id="message" name="message" rows="7" placeholder="Expliquez votre probleme de maniere claire..." required></textarea>

                            <button type="submit" class="btn-primary support-submit">Envoyer la reclamation</button>
                            <p class="support-hint">Votre demande sera sauvegardee puis visible dans la liste ci-dessous.</p>
                        </form>
                    </div>

                    <div class="support-thread-card">
                        <div class="thread-head">
                            <div>
                                <h2>Mes reclamations</h2>
                                <p>Retrouvez ici vos messages et les reponses de l'administration.</p>
                            </div>
                        </div>

                        <?php if ($loadError): ?>
                            <div class="reclamation-alert reclamation-alert-error"><?php echo htmlspecialchars($loadError, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php elseif (!$threads): ?>
                            <div class="empty-state">Aucune reclamation pour le moment. Envoyez votre premiere demande.</div>
                        <?php else: ?>
                            <div class="reclamation-thread-list">
                                <?php foreach ($threads as $thread): ?>
                                    <article class="reclamation-thread-item">
                                        <div class="reclamation-thread-head">
                                            <strong><?php echo htmlspecialchars((string) ($thread['subject'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong>
                                            <small><?php echo htmlspecialchars((string) ($thread['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></small>
                                        </div>

                                        <?php if (!empty($thread['user_message'])): ?>
                                            <p class="thread-label">Votre message</p>
                                            <div class="thread-bubble thread-bubble-user"><?php echo nl2br(htmlspecialchars((string) $thread['user_message'], ENT_QUOTES, 'UTF-8')); ?></div>
                                        <?php endif; ?>

                                        <?php if (!empty($thread['admin_reply'])): ?>
                                            <p class="thread-label">Reponse admin</p>
                                            <div class="thread-bubble thread-bubble-admin"><?php echo nl2br(htmlspecialchars((string) $thread['admin_reply'], ENT_QUOTES, 'UTF-8')); ?></div>
                                            <small class="thread-date"><?php echo htmlspecialchars((string) ($thread['admin_reply_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></small>
                                        <?php else: ?>
                                            <p class="thread-waiting">En attente de la reponse admin...</p>
                                        <?php endif; ?>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </section>
        </main>
    </div>
</body>
</html>
