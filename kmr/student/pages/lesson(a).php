<?php

declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();

$cinEtudiant = (int) ($_SESSION['CIN'] ?? 0);
$courseId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($courseId <= 0) {
    set_flash('error', 'Cours invalide.');
    redirect('cours.php');
}

$stmtCourse = $pdo->prepare('SELECT id, nom_cours FROM cours WHERE id = :id LIMIT 1');
$stmtCourse->execute(['id' => $courseId]);
$course = $stmtCourse->fetch();
if (!$course) {
    set_flash('error', 'Cours introuvable.');
    redirect('cours.php');
}

$stmtLessons = $pdo->prepare('SELECT id_lecon, titre, description, type_fichier FROM lecon WHERE id_cours = :id_cours ORDER BY id_lecon ASC');
$stmtLessons->execute(['id_cours' => $courseId]);
$lessons = $stmtLessons->fetchAll();

$stmtDone = $pdo->prepare('SELECT id_lecon FROM suivi_lecons WHERE id_etudiant = :cin AND id_cours = :id_cours');
$stmtDone->execute(['cin' => $cinEtudiant, 'id_cours' => $courseId]);
$doneLessons = [];
foreach ($stmtDone->fetchAll() as $row) {
    $doneLessons[(int) $row['id_lecon']] = true;
}

$error = get_flash('error');
$success = get_flash('success');

function lesson_icon(string $mimeType): string
{
    $mimeType = strtolower($mimeType);
    if (str_contains($mimeType, 'video')) {
        return '&#9654;';
    }
    if (str_contains($mimeType, 'pdf')) {
        return '&#128196;';
    }
    if (str_contains($mimeType, 'audio') || str_contains($mimeType, 'mpeg')) {
        return '&#127911;';
    }
    return '&#128196;';
}

?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leçons - <?php echo htmlspecialchars((string) $course['nom_cours'], ENT_QUOTES, 'UTF-8'); ?></title>
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
                    <h1>Leçons</h1>
                    <p>Contenu du cours : <strong><?php echo htmlspecialchars((string) $course['nom_cours'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
                </div>
                <div class="header-actions">
                    <a class="btn-nav" href="cours.php">Retour aux cours</a>
                </div>
            </header>

            <?php if ($error): ?>
                <div class="task-alert task-alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="task-alert task-alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if (!$lessons): ?>
                <section class="card" style="padding: 18px;">
                    <p style="color: var(--muted);">Aucune leçon disponible pour le moment.</p>
                </section>
            <?php endif; ?>

            <section class="lesson-list">
                <?php foreach ($lessons as $lesson): ?>
                    <?php
                    $lessonId = (int) $lesson['id_lecon'];
                    $isDone = isset($doneLessons[$lessonId]);
                    ?>
                    <article class="lesson-card card <?php echo $isDone ? 'lesson-card-done' : ''; ?>">
                        <div class="lesson-icon" aria-hidden="true"><?php echo lesson_icon((string) ($lesson['type_fichier'] ?? '')); ?></div>
                        <div class="lesson-body">
                            <h3><?php echo htmlspecialchars((string) $lesson['titre'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p><?php echo htmlspecialchars((string) ($lesson['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php if ($isDone): ?>
                                <span class="lesson-badge lesson-badge-done">Terminée</span>
                            <?php else: ?>
                                <span class="lesson-badge">À faire</span>
                            <?php endif; ?>
                        </div>
                        <div class="lesson-actions">
                            <a class="btn-primary" href="visualiser_leçon.php?id=<?php echo $lessonId; ?>">
                                <?php echo $isDone ? 'Revoir' : 'Ouvrir'; ?>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        </main>
    </div>
</body>
</html>

