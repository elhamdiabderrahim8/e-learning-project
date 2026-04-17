<?php

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$error = get_flash('error');
$success = get_flash('success');
$isEnglish = current_language() === 'en';

$replies = [];
$replyLoadError = null;

try {
    require_once __DIR__ . '/../backend/includes/migrate_support_tables.php';
    migrate_support_tables();
    $pdo = db();
    $stmt = $pdo->prepare(
        "SELECT
            t.id,
            t.subject,
            t.created_at,
            (
                SELECT sm.message
                FROM support_messages sm
                WHERE sm.thread_id = t.id AND sm.sender = 'etudiant'
                ORDER BY sm.created_at ASC
                LIMIT 1
            ) AS user_message,
            (
                SELECT sm.message
                FROM support_messages sm
                WHERE sm.thread_id = t.id AND sm.sender = 'admin'
                ORDER BY sm.created_at DESC
                LIMIT 1
            ) AS admin_reply,
            (
                SELECT sm.created_at
                FROM support_messages sm
                WHERE sm.thread_id = t.id AND sm.sender = 'admin'
                ORDER BY sm.created_at DESC
                LIMIT 1
            ) AS admin_reply_at
         FROM support_threads t
         WHERE t.user_id = :user_id AND t.user_type = 'etudiant'
         ORDER BY t.created_at DESC
         LIMIT 20"
    );
    $stmt->execute(['user_id' => (string) user_id()]);
    $replies = $stmt->fetchAll() ?: [];
} catch (Throwable $e) {
    error_log('Reclamation reply error: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
    $replyLoadError = $isEnglish
        ? ('Replies unavailable. Error: ' . $e->getMessage())
        : ('Les reponses indisponibles. Erreur: ' . $e->getMessage());
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reclamation - Enjah</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../media/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../media/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../media/favicon_io/favicon-16x16.png">
    <link rel="shortcut icon" href="../media/favicon_io/favicon.ico">
    <link rel="manifest" href="../media/favicon_io/site.webmanifest">
</head>
<body>
    <div class="dashboard-container">
        <?php $active = 'reclamation'; require __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <h1><?php echo $isEnglish ? 'Send a support request' : 'Envoyer une reclamation'; ?></h1>
                <p><?php echo $isEnglish ? 'Having an issue? Our team will reply within 24h.' : 'Un probleme ? Notre equipe vous repondra sous 24h.'; ?></p>
            </header>

            <?php if ($error): ?>
                <div class="reclamation-alert reclamation-alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="reclamation-alert reclamation-alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <section class="reclamation-shell">
                <div class="reclamation-intro card">
                    <h2><?php echo $isEnglish ? 'Student Support' : 'Support Etudiant'; ?></h2>
                    <p><?php echo $isEnglish ? 'Fill out this form and attach files if needed. Our team will review your request quickly.' : 'Remplissez ce formulaire et joignez un fichier si necessaire. Notre equipe analysera votre demande rapidement.'; ?></p>
                    <ul class="reclamation-points">
                        <li><?php echo $isEnglish ? 'Reply within 24 business hours' : 'Reponse sous 24h ouvrables'; ?></li>
                        <li><?php echo $isEnglish ? 'Personalized follow-up' : 'Suivi personnalise de votre demande'; ?></li>
                        <li><?php echo $isEnglish ? 'Your information stays confidential' : 'Confidentialite de vos informations'; ?></li>
                    </ul>
                </div>

                <div class="reclamation-form-card card">
                    <form class="reclamation-form" action="../backend/actions/create_reclamation.php" method="post" enctype="multipart/form-data">
                        <div class="input-group">
                            <label for="subject"><?php echo $isEnglish ? 'Request subject' : 'Sujet de la reclamation'; ?></label>
                            <select id="subject" name="subject" required>
                                <option value="Probleme d acces au cours"><?php echo $isEnglish ? 'Course access issue' : 'Probleme d acces au cours'; ?></option>
                                <option value="Erreur de paiement / Facturation"><?php echo $isEnglish ? 'Payment / billing error' : 'Erreur de paiement / Facturation'; ?></option>
                                <option value="Bug technique sur la plateforme"><?php echo $isEnglish ? 'Platform technical bug' : 'Bug technique sur la plateforme'; ?></option>
                                <option value="Autre"><?php echo $isEnglish ? 'Other' : 'Autre'; ?></option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="message"><?php echo $isEnglish ? 'Detailed description' : 'Description detaillee'; ?></label>
                            <textarea id="message" name="message" rows="7" placeholder="<?php echo $isEnglish ? 'Explain your issue clearly...' : 'Expliquez votre probleme de maniere claire...'; ?>" required></textarea>
                            <p class="input-help"><?php echo $isEnglish ? 'Add the steps that triggered the issue to speed up resolution.' : 'Ajoutez les etapes qui ont provoque le probleme pour accelerer la resolution.'; ?></p>
                        </div>

                        <div class="input-group">
                            <label for="attachments"><?php echo $isEnglish ? 'Attachments (optional)' : 'Pieces jointes (facultatif)'; ?></label>
                            <input type="file" id="attachments" name="attachments[]" multiple>
                            <p class="input-help"><?php echo $isEnglish ? 'Recommended formats: PDF, PNG, JPG. Max size: 5 MB per file.' : 'Formats recommandes: PDF, PNG, JPG. Taille maximale: 5 Mo par fichier.'; ?></p>
                        </div>

                        <input type="submit" class="btn-primary reclamation-submit" value="<?php echo $isEnglish ? 'Send request' : 'Envoyer la reclamation'; ?>">
                    </form>

                    <div style="margin-top: 16px;">
                        <button type="button" id="toggleRepliesBtn" class="btn-primary reclamation-submit" style="background:#0f172a;">
                            <?php echo $isEnglish ? 'See admin replies' : 'Voir les reponses admin'; ?>
                        </button>
                    </div>
                </div>
            </section>

            <section id="adminRepliesSection" class="reclamation-shell" style="display:none; margin-top:18px;">
                <div class="reclamation-form-card card">
                    <h2 style="margin-top:0;"><?php echo $isEnglish ? 'Admin replies' : 'Reponses de l\'admin'; ?></h2>

                    <?php if ($replyLoadError): ?>
                        <div class="reclamation-alert reclamation-alert-error"><?php echo htmlspecialchars($replyLoadError, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php elseif (!$replies): ?>
                        <p><?php echo $isEnglish ? 'No requests yet. Submit your first reclamation.' : 'Aucune reclamation pour le moment. Envoyez votre premiere demande.'; ?></p>
                    <?php else: ?>
                        <?php foreach ($replies as $row): ?>
                            <article style="border:1px solid #e2e8f0; border-radius:10px; padding:12px; margin-bottom:12px;">
                                <div style="display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                                    <strong><?php echo htmlspecialchars((string) ($row['subject'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <small><?php echo htmlspecialchars((string) ($row['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>

                                <?php if (!empty($row['user_message'])): ?>
                                    <p style="margin:10px 0 0;"><strong><?php echo $isEnglish ? 'Your message:' : 'Votre message :'; ?></strong></p>
                                    <p style=\"margin:4px 0 0; white-space:pre-wrap; background:#f8fafc; padding:8px; border-radius:6px; border-left:3px solid #4f46e5;\">
                                        <?php echo htmlspecialchars((string) $row['user_message'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($row['admin_reply'])): ?>
                                    <p style="margin:10px 0 0;"><strong><?php echo $isEnglish ? 'Admin reply:' : 'Reponse admin :'; ?></strong></p>
                                    <p style=\"margin:4px 0 0; white-space:pre-wrap; background:#eef2ff; padding:8px; border-radius:6px; border-left:3px solid #10b981;\">
                                        <?php echo htmlspecialchars((string) $row['admin_reply'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                    <small style="display:block; margin-top:6px;"><?php echo htmlspecialchars((string) ($row['admin_reply_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></small>
                                <?php else: ?>
                                    <p style="margin:10px 0 0; color:#64748b;"><?php echo $isEnglish ? 'Waiting for admin reply...' : 'En attente de la reponse admin...'; ?></p>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <script>
    (function () {
        var button = document.getElementById('toggleRepliesBtn');
        var section = document.getElementById('adminRepliesSection');

        if (!button || !section) {
            return;
        }

        button.addEventListener('click', function () {
            var isHidden = section.style.display === 'none';
            section.style.display = isHidden ? 'block' : 'none';
            button.textContent = isHidden
                ? <?php echo json_encode($isEnglish ? 'Hide admin replies' : 'Masquer les reponses admin'); ?>
                : <?php echo json_encode($isEnglish ? 'See admin replies' : 'Voir les reponses admin'); ?>;

            if (isHidden) {
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    })();
    </script>
    </body>
</html>


