<?php

declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();
$stmt = $pdo->prepare('SELECT id, title, due_date, priority, is_completed FROM tasks WHERE user_id = :user_id ORDER BY is_completed ASC, created_at DESC');
$stmt->execute(['user_id' => user_id()]);
$tasks = $stmt->fetchAll();

$error = get_flash('error');
$success = get_flash('success');
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taches</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">Smart Learning</div>
            <nav>
                <ul>
                    <li><a href="cours.php">Mes Cours</a></li>
                    <li class="active"><a href="tache_a_fair.php">Mes Taches</a></li>
                    <li><a href="offres.php">Choisir une offre</a></li>
                    <li><a href="calendrier.php">Calendrier</a></li>
                    <li><a href="certificats.php">Certificats</a></li>
                    <li><a href="reclamation.php">Reclamation</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <h1>Mes Taches a faire</h1>
                <p>Suivez vos devoirs et projets en cours.</p>
                <p><a href="../backend/actions/logout.php">Se deconnecter</a></p>
            </header>

            <?php if ($error): ?>
                <p style="color: #b42318;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p style="color: #027a48;"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <section class="task-section">
                <?php foreach ($tasks as $task): ?>
                    <?php
                    $priorityClass = 'priority-medium';
                    if ($task['priority'] === 'high') {
                        $priorityClass = 'priority-high';
                    } elseif ($task['priority'] === 'low') {
                        $priorityClass = 'priority-low';
                    }
                    ?>
                    <div class="task-card" style="opacity: <?php echo ((int) $task['is_completed']) === 1 ? '0.65' : '1'; ?>;">
                        <div class="status-indicator <?php echo $priorityClass; ?>"></div>
                        <div class="task-info">
                            <h3><?php echo htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p>Priorite : <?php echo htmlspecialchars((string) $task['priority'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <span class="deadline">
                                <?php echo $task['due_date'] ? 'Echeance : ' . htmlspecialchars((string) $task['due_date'], ENT_QUOTES, 'UTF-8') : 'Sans date limite'; ?>
                            </span>
                        </div>
                        <form action="../backend/actions/toggle_task.php" method="post">
                            <input type="hidden" name="task_id" value="<?php echo (int) $task['id']; ?>">
                            <button type="submit" class="btn-complete">
                                <?php echo ((int) $task['is_completed']) === 1 ? 'Reouvrir' : 'Terminer'; ?>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>

                <?php if (!$tasks): ?>
                    <p>Aucune tache pour le moment.</p>
                <?php endif; ?>
            </section>

            <footer class="add-task">
                <form action="../backend/actions/create_task.php" method="post" style="display: flex; width: 100%; gap: 10px;">
                    <input type="text" name="title" placeholder="Ajouter une nouvelle tache..." required style="flex:1;">
                    <button type="submit" class="btn-add">Ajouter</button>
                </form>
            </footer>
        </main>
    </div>

</body>
</html>
