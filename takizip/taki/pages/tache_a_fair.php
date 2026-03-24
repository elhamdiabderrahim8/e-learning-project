<?php

declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();

try {
    $pdo->exec("ALTER TABLE tasks ADD COLUMN IF NOT EXISTS status VARCHAR(20) NOT NULL DEFAULT 'a_faire'");
} catch (Throwable $e) {
    // Ignore if DB permissions restrict alter operations.
}

$pdo->prepare("UPDATE tasks SET status = CASE WHEN is_completed = TRUE THEN 'terminee' ELSE 'a_faire' END WHERE status IS NULL OR status NOT IN ('a_faire', 'en_cours', 'terminee')")
    ->execute();

$stmt = $pdo->prepare("SELECT id, title, due_date, priority, status, is_completed FROM tasks WHERE user_id = :user_id ORDER BY CASE status WHEN 'a_faire' THEN 1 WHEN 'en_cours' THEN 2 WHEN 'terminee' THEN 3 ELSE 4 END, due_date IS NULL, due_date ASC, created_at DESC");
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
                <div class="task-alert task-alert-error" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="task-alert task-alert-success" role="status"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
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
                    <button type="submit" class="btn-add">Ajouter la tache</button>
                </form>
            </section>

            <section class="task-board">
                <?php foreach ($statusGroups as $statusKey => $group): ?>
                    <div class="task-column card">
                        <div class="task-column-header">
                            <h2><?php echo htmlspecialchars($group['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                            <span class="task-count" data-task-count><?php echo count($group['tasks']); ?></span>
                        </div>

                        <div class="task-filters" data-filter-container>
                            <div class="task-filter-row">
                                <input type="search" class="task-filter-search" data-filter-search placeholder="Rechercher une tache" aria-label="Rechercher une tache">
                            </div>
                            <div class="task-filter-row task-filter-row-inline">
                                <select class="task-filter-select" data-filter-priority aria-label="Filtrer par priorite">
                                    <option value="all">Toutes priorites</option>
                                    <option value="high">Haute</option>
                                    <option value="medium">Moyenne</option>
                                    <option value="low">Basse</option>
                                </select>
                                <select class="task-filter-select" data-filter-due aria-label="Filtrer par echeance">
                                    <option value="all">Toutes echeances</option>
                                    <option value="with_date">Avec date limite</option>
                                    <option value="without_date">Sans date limite</option>
                                    <option value="late">En retard</option>
                                    <option value="today">A rendre aujourd'hui</option>
                                    <option value="this_week">Cette semaine</option>
                                </select>
                            </div>
                            <div class="task-filter-row task-filter-row-inline">
                                <select class="task-filter-select" data-filter-sort aria-label="Trier les taches">
                                    <option value="default">Tri par defaut</option>
                                    <option value="due_asc">Date limite proche</option>
                                    <option value="due_desc">Date limite lointaine</option>
                                    <option value="priority_desc">Priorite haute d'abord</option>
                                    <option value="priority_asc">Priorite basse d'abord</option>
                                    <option value="title_asc">Titre A-Z</option>
                                </select>
                                <button type="button" class="task-filter-reset" data-filter-reset>Reinitialiser</button>
                            </div>
                        </div>

                        <?php if (!$group['tasks']): ?>
                            <p class="task-empty">Aucune tache.</p>
                        <?php endif; ?>

                        <div class="task-list" data-task-list>
                            <?php foreach ($group['tasks'] as $task): ?>
                                <?php
                                $priorityClass = 'priority-medium';
                                $priorityLabel = 'Moyenne';
                                if ($task['priority'] === 'high') {
                                    $priorityClass = 'priority-high';
                                    $priorityLabel = 'Haute';
                                } elseif ($task['priority'] === 'low') {
                                    $priorityClass = 'priority-low';
                                    $priorityLabel = 'Basse';
                                }
                                ?>
                                <article
                                    class="task-card task-card-compact"
                                    data-task-card
                                    data-title="<?php echo htmlspecialchars((string) $task['title'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-priority="<?php echo htmlspecialchars((string) $task['priority'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-due-date="<?php echo htmlspecialchars((string) ($task['due_date'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                >
                                    <div class="task-title-row">
                                        <span class="status-indicator <?php echo $priorityClass; ?>"></span>
                                        <h3><?php echo htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    </div>

                                    <div class="task-meta-row">
                                        <span class="priority-pill <?php echo $priorityClass; ?>"><?php echo htmlspecialchars($priorityLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span class="deadline <?php echo $task['due_date'] ? '' : 'deadline-soft'; ?>">
                                            <?php echo $task['due_date'] ? 'Date limite: ' . htmlspecialchars((string) $task['due_date'], ENT_QUOTES, 'UTF-8') : 'Sans date limite'; ?>
                                        </span>
                                    </div>

                                    <form action="../backend/actions/update_task_status.php" method="post" class="task-status-form" data-current-status="<?php echo htmlspecialchars($statusKey, ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="task_id" value="<?php echo (int) $task['id']; ?>">
                                        <input type="hidden" name="delete_completed" value="0">
                                        <button type="submit" class="btn-complete"><?php echo $statusKey === 'terminee' ? 'Supprimer la tache' : 'Mettre a jour'; ?></button>
                                    </form>
                                </article>
                            <?php endforeach; ?>
                        </div>
                        <p class="task-empty task-empty-filter" data-filter-empty hidden>Aucune tache ne correspond aux filtres.</p>
                    </div>
                <?php endforeach; ?>
            </section>
        </main>
    </div>

    <script>
    document.querySelectorAll('.task-status-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            var currentStatus = form.getAttribute('data-current-status');
            if (currentStatus !== 'terminee') {
                return;
            }

            event.preventDefault();

            var shouldDelete = window.confirm('Cette tache est deja terminee. Voulez-vous la supprimer ?');
            if (!shouldDelete) {
                return;
            }

            var deleteInput = form.querySelector('input[name="delete_completed"]');
            if (deleteInput) {
                deleteInput.value = '1';
            }
            form.submit();
        });
    });

    document.querySelectorAll('.task-column').forEach(function (column) {
        var list = column.querySelector('[data-task-list]');
        if (!list) {
            return;
        }

        var cards = Array.prototype.slice.call(list.querySelectorAll('[data-task-card]'));
        var countNode = column.querySelector('[data-task-count]');
        var emptyFiltered = column.querySelector('[data-filter-empty]');
        var searchInput = column.querySelector('[data-filter-search]');
        var prioritySelect = column.querySelector('[data-filter-priority]');
        var dueSelect = column.querySelector('[data-filter-due]');
        var sortSelect = column.querySelector('[data-filter-sort]');
        var resetButton = column.querySelector('[data-filter-reset]');

        if (!cards.length) {
            if (searchInput) {
                searchInput.disabled = true;
            }
            if (prioritySelect) {
                prioritySelect.disabled = true;
            }
            if (dueSelect) {
                dueSelect.disabled = true;
            }
            if (sortSelect) {
                sortSelect.disabled = true;
            }
            if (resetButton) {
                resetButton.disabled = true;
            }
            return;
        }

        var priorityRank = { high: 3, medium: 2, low: 1 };

        function getTodayISO() {
            var today = new Date();
            return today.toISOString().slice(0, 10);
        }

        function getWeekEndISO() {
            var today = new Date();
            var day = today.getDay();
            var shiftToSunday = day === 0 ? 0 : 7 - day;
            today.setDate(today.getDate() + shiftToSunday);
            return today.toISOString().slice(0, 10);
        }

        function matchesDueFilter(filterValue, dueDate, todayISO, weekEndISO) {
            if (filterValue === 'all') {
                return true;
            }
            if (filterValue === 'with_date') {
                return dueDate !== '';
            }
            if (filterValue === 'without_date') {
                return dueDate === '';
            }
            if (dueDate === '') {
                return false;
            }
            if (filterValue === 'late') {
                return dueDate < todayISO;
            }
            if (filterValue === 'today') {
                return dueDate === todayISO;
            }
            if (filterValue === 'this_week') {
                return dueDate >= todayISO && dueDate <= weekEndISO;
            }
            return true;
        }

        function compareCards(a, b, sortValue) {
            var aDue = a.getAttribute('data-due-date') || '';
            var bDue = b.getAttribute('data-due-date') || '';
            var aPriority = a.getAttribute('data-priority') || 'medium';
            var bPriority = b.getAttribute('data-priority') || 'medium';
            var aTitle = (a.getAttribute('data-title') || '').toLowerCase();
            var bTitle = (b.getAttribute('data-title') || '').toLowerCase();

            if (sortValue === 'due_asc') {
                if (aDue === '' && bDue !== '') {
                    return 1;
                }
                if (aDue !== '' && bDue === '') {
                    return -1;
                }
                return aDue.localeCompare(bDue);
            }

            if (sortValue === 'due_desc') {
                if (aDue === '' && bDue !== '') {
                    return 1;
                }
                if (aDue !== '' && bDue === '') {
                    return -1;
                }
                return bDue.localeCompare(aDue);
            }

            if (sortValue === 'priority_desc') {
                return (priorityRank[bPriority] || 0) - (priorityRank[aPriority] || 0);
            }

            if (sortValue === 'priority_asc') {
                return (priorityRank[aPriority] || 0) - (priorityRank[bPriority] || 0);
            }

            if (sortValue === 'title_asc') {
                return aTitle.localeCompare(bTitle);
            }

            return 0;
        }

        function applyColumnFilters() {
            var query = ((searchInput && searchInput.value) || '').trim().toLowerCase();
            var priority = (prioritySelect && prioritySelect.value) || 'all';
            var dueFilter = (dueSelect && dueSelect.value) || 'all';
            var sortValue = (sortSelect && sortSelect.value) || 'default';
            var todayISO = getTodayISO();
            var weekEndISO = getWeekEndISO();

            var visibleCards = cards.filter(function (card) {
                var title = (card.getAttribute('data-title') || '').toLowerCase();
                var cardPriority = card.getAttribute('data-priority') || 'medium';
                var dueDate = card.getAttribute('data-due-date') || '';

                var matchesQuery = query === '' || title.indexOf(query) !== -1;
                var matchesPriority = priority === 'all' || cardPriority === priority;
                var matchesDue = matchesDueFilter(dueFilter, dueDate, todayISO, weekEndISO);

                return matchesQuery && matchesPriority && matchesDue;
            });

            var sortedVisible = visibleCards.slice().sort(function (a, b) {
                return compareCards(a, b, sortValue);
            });

            cards.forEach(function (card) {
                card.hidden = true;
            });

            sortedVisible.forEach(function (card) {
                card.hidden = false;
                list.appendChild(card);
            });

            if (countNode) {
                countNode.textContent = String(sortedVisible.length);
            }

            if (emptyFiltered) {
                emptyFiltered.hidden = sortedVisible.length !== 0;
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', applyColumnFilters);
        }
        if (prioritySelect) {
            prioritySelect.addEventListener('change', applyColumnFilters);
        }
        if (dueSelect) {
            dueSelect.addEventListener('change', applyColumnFilters);
        }
        if (sortSelect) {
            sortSelect.addEventListener('change', applyColumnFilters);
        }
        if (resetButton) {
            resetButton.addEventListener('click', function () {
                if (searchInput) {
                    searchInput.value = '';
                }
                if (prioritySelect) {
                    prioritySelect.value = 'all';
                }
                if (dueSelect) {
                    dueSelect.value = 'all';
                }
                if (sortSelect) {
                    sortSelect.value = 'default';
                }
                applyColumnFilters();
            });
        }

        applyColumnFilters();
    });
    </script>

    </body>
</html>


