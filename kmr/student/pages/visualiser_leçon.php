<?php

declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();

$lessonId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($lessonId <= 0) {
    set_flash('error', 'Leçon invalide.');
    redirect('cours.php');
}

$stmt = $pdo->prepare('SELECT id_lecon, titre, description, type_fichier, nom_fichier, id_cours FROM lecon WHERE id_lecon = :id LIMIT 1');
$stmt->execute(['id' => $lessonId]);
$lesson = $stmt->fetch();
if (!$lesson) {
    set_flash('error', 'Leçon introuvable.');
    redirect('cours.php');
}

$courseId = (int) $lesson['id_cours'];
$filePath = '../../professeur/uploads/' . (string) $lesson['nom_fichier'];
$mimeType = (string) ($lesson['type_fichier'] ?? '');

$mode = 'file';
if (stripos($mimeType, 'video') !== false) {
    $mode = 'video';
} elseif (stripos($mimeType, 'pdf') !== false) {
    $mode = 'pdf';
} elseif (stripos($mimeType, 'audio') !== false || stripos($mimeType, 'mpeg') !== false) {
    $mode = 'audio';
}

?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars((string) $lesson['titre'], ENT_QUOTES, 'UTF-8'); ?> - Enjah</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../media/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../media/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../media/favicon_io/favicon-16x16.png">
    <link rel="shortcut icon" href="../media/favicon_io/favicon.ico">
    <link rel="manifest" href="../media/favicon_io/site.webmanifest">
</head>
<body>
    <div class="dashboard-container">
        <?php $active = 'cours'; require __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="header header-flex">
                <div>
                    <h1><?php echo htmlspecialchars((string) $lesson['titre'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p><?php echo htmlspecialchars((string) ($lesson['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="header-actions">
                    <a class="btn-nav" href="lesson(a).php?id=<?php echo $courseId; ?>">Retour aux leçons</a>
                </div>
            </header>

            <section class="lesson-viewer card">
                <div class="lesson-media lesson-media-<?php echo htmlspecialchars($mode, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if ($mode === 'video'): ?>
                        <video id="mainVideo" controls autoplay>
                            <source src="<?php echo htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8'); ?>" type="<?php echo htmlspecialchars($mimeType, ENT_QUOTES, 'UTF-8'); ?>">
                            Votre navigateur ne supporte pas la vidéo.
                        </video>
                    <?php elseif ($mode === 'pdf'): ?>
                        <embed class="lesson-pdf" src="<?php echo htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8'); ?>#toolbar=0" type="application/pdf">
                    <?php elseif ($mode === 'audio'): ?>
                        <div class="lesson-audio-shell">
                            <div class="lesson-audio-icon" aria-hidden="true">&#127911;</div>
                            <audio controls>
                                <source src="<?php echo htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8'); ?>" type="<?php echo htmlspecialchars($mimeType, ENT_QUOTES, 'UTF-8'); ?>">
                            </audio>
                        </div>
                    <?php else: ?>
                        <div class="lesson-file-shell">
                            <p style="color: var(--muted); margin-bottom: 14px;">Aperçu non disponible pour ce format.</p>
                            <a class="btn-primary" href="<?php echo htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8'); ?>" download>Télécharger le fichier</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="lesson-viewer-actions">
                    <a class="btn-primary" href="../backend/actions/valider_lesson.php?id_lecon=<?php echo $lessonId; ?>&id_cours=<?php echo $courseId; ?>">
                        Marquer comme terminée
                    </a>
                </div>
            </section>

            <script>
                function markLessonDone() {
                    const idLecon = <?php echo $lessonId; ?>;
                    const idCours = <?php echo $courseId; ?>;

                    fetch('../backend/actions/valider_lesson.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'id_lecon=' + encodeURIComponent(idLecon) + '&id_cours=' + encodeURIComponent(idCours)
                    })
                        .then(function (response) { return response.json(); })
                        .then(function () { window.location.href = 'lesson(a).php?id=' + idCours; })
                        .catch(function () { window.location.href = 'lesson(a).php?id=' + idCours; });
                }

                const video = document.getElementById('mainVideo');
                if (video) {
                    video.addEventListener('ended', function () {
                        markLessonDone();
                    });
                }
            </script>
        </main>
    </div>
</body>
</html>

