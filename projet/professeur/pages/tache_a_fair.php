<?php

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();

$stmt = $pdo->prepare("SELECT id, title, due_date, priority, status, is_completed FROM tasks WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute([
    'user_id' => user_id(),
]);
$tasks = $stmt->fetchAll();

$statusGroups = [
    'a_faire' => [
        'title' => 'A faire',
        'tasks' => [],
    ],
    'en_cours' => [
        'title' => 'En cours',
        'tasks' => [],
    ],
    'terminee' => [
        'title' => 'Terminee',
        'tasks' => [],
    ],
];

foreach ($tasks as $task) {
    $statusKey = (string) ($task['status'] ?? 'a_faire');
    if (!isset($statusGroups[$statusKey])) {
        $statusKey = ((int) $task['is_completed']) === 1 ? 'terminee' : 'a_faire';
    }
    $statusGroups[$statusKey]['tasks'][] = $task;
}

$error = get_flash('error');
$success = get_flash('success');
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taches</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../media/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../media/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../media/favicon_io/favicon-16x16.png">
    <link rel="shortcut icon" href="../media/favicon_io/favicon.ico">
    <link rel="manifest" href="../media/favicon_io/site.webmanifest">
</head>
<body>

    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo"><img src="../media/logo.jpg" alt="Logo Enjah"><span>Enjah</span></div>
            <nav>
                <ul>
                    <li><a href="cours.php"><span class="nav-icon">&#8962;</span><span>Mes Cours</span></a></li>
                    <li class="active"><a href="tache_a_fair.php"><span class="nav-icon">&#128221;</span><span>Mes Taches</span></a></li>
                    <li><a href="offres.php"><span class="nav-icon">&#9671;</span><span>Choisir une offre</span></a></li>
                    <li><a href="reclamation.php"><span class="nav-icon">&#128172;</span><span>Reclamation</span></a></li>
                    <li><a href="profil.php"><span class="nav-icon">&#128100;</span><span>Mon Profil</span></a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <h1>Tableau des taches</h1>
                <p>Organisez votre travail en 3 etapes simples : A faire, En cours, Terminee.</p>
            </header>

            <?php if ($error): ?>
                <div class="task-alert task-alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="task-alert task-alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <section class="task-create-panel card">
                <h2>Ajouter une tache</h2>
                <form action="../backend/actions/create_task.php" method="post" class="task-create-form">
                    <div class="input-group">
                        <label for="title">Titre</label>
                        <input type="text" id="title" name="title" placeholder="Ex: Projet Java" required>
                    </div>
                    <div class="input-group">
                        <label for="priority">Priorite</label>
                        <select id="priority" name="priority">
                            <option value="high">Haute</option>
                            <option value="medium" selected>Moyenne</option>
                            <option value="low">Basse</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="due_date">Date limite (optionnel)</label>
                        <input type="date" id="due_date" name="due_date">
                    </div>
                    <input type="hidden" name="status" value="a_faire">
                    <input type="submit" class="btn-add" value="Ajouter la tache">
                </form>
            </section>

            <section class="task-board">
                <?php foreach ($statusGroups as $statusKey => $group): ?>
                    <div class="task-column card">
                        <div class="task-column-header">
                            <h2><?php echo htmlspecialchars($group['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                            <span class="task-count" data-task-count><?php echo count($group['tasks']); ?></span>
                        </div>

                        <?php if (!$group['tasks']): ?>
                            <p class="task-empty">Aucune tache.</p>
                        <?php endif; ?>

                        <div class="task-list">
                            <?php foreach ($group['tasks'] as $task): ?>
                                <?php
                                $priorityClass = 'priority-medium';
                                $priorityLabel = 'Moyenne';
                                $dueText = 'Sans date limite';
                                $buttonText = 'Passer a l\'etape suivante';

                                if ($task['priority'] === 'high') {
                                    $priorityClass = 'priority-high';
                                    $priorityLabel = 'Haute';
                                } elseif ($task['priority'] === 'low') {
                                    $priorityClass = 'priority-low';
                                    $priorityLabel = 'Basse';
                                }

                                if (!empty($task['due_date'])) {
                                    $dueText = 'Date limite: ' . htmlspecialchars((string) $task['due_date'], ENT_QUOTES, 'UTF-8');
                                }

                                if ($statusKey === 'terminee') {
                                    $buttonText = 'Supprimer la tache';
                                }
                                ?>
                                <article
                                    class="task-card task-card-compact"
                                >
                                    <div class="task-title-row">
                                        <span class="status-indicator <?php echo $priorityClass; ?>"></span>
                                        <h3><?php echo htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    </div>

                                    <div class="task-meta-row">
                                        <span class="priority-pill <?php echo $priorityClass; ?>"><?php echo htmlspecialchars($priorityLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span class="deadline <?php echo $task['due_date'] ? '' : 'deadline-soft'; ?>">
                                            <?php echo $dueText; ?>
                                        </span>
                                    </div>

                                    <form action="../backend/actions/update_task_status.php" method="post" class="task-status-form" data-current-status="<?php echo htmlspecialchars($statusKey, ENT_QUOTES, 'UTF-8'); ?>" onsubmit="return taskStatusSubmit(this);">
                                        <input type="hidden" name="task_id" value="<?php echo (int) $task['id']; ?>">
                                        <input type="hidden" name="delete_completed" value="0">
                                        <input type="submit" class="btn-complete" value="<?php echo $buttonText; ?>">
                                    </form>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>
        </main>
    </div>

    <script>
    function taskStatusSubmit(form) {
        var currentStatus = form.getAttribute('data-current-status');
        if (currentStatus != 'terminee') {
            return true;
        }

        var answer = prompt('Tapez OUI pour supprimer cette tache terminee', '');
        if (answer == null) {
            return false;
        }

        answer = answer.trim().toUpperCase();
        if (answer != 'OUI') {
            alert('Suppression annulee.');
            return false;
        }

        var deleteInput = form.querySelector('input[name="delete_completed"]');
        if (deleteInput) {
            deleteInput.value = '1';
        }

        return true;
    }
    </script>

    </body>
</html>


