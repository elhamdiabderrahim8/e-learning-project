<?php

declare(strict_types=1);

require_once __DIR__ . '/auth_guard.php';

$credentialsFile = __DIR__ . '/admin_credentials.php';
$admins = [];
$message = '';
$messageType = 'success';

if (is_file($credentialsFile)) {
    $loaded = require $credentialsFile;
    if (is_array($loaded)) {
        $admins = $loaded;
    }
}

$normalizeEmail = static function (string $email): string {
    return strtolower(trim($email));
};

$saveAdmins = static function (array $admins, string $filePath): bool {
    $export = "<?php\n\ndeclare(strict_types=1);\n\nreturn " . var_export(array_values($admins), true) . ";\n";
    return file_put_contents($filePath, $export, LOCK_EX) !== false;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'add') {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Veuillez saisir un email valide.';
            $messageType = 'error';
        } elseif ($password === '') {
            $message = 'Veuillez saisir un mot de passe.';
            $messageType = 'error';
        } else {
            $emailKey = $normalizeEmail($email);
            $exists = false;

            foreach ($admins as $admin) {
                if ($normalizeEmail((string) ($admin['email'] ?? '')) === $emailKey) {
                    $exists = true;
                    break;
                }
            }

            if ($exists) {
                $message = 'Cet email admin existe déjà.';
                $messageType = 'error';
            } else {
                $admins[] = [
                    'email' => $email,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ];

                if ($saveAdmins($admins, $credentialsFile)) {
                    $message = 'Admin ajouté avec succès.';
                } else {
                    $message = 'Impossible de sauvegarder le fichier des administrateurs.';
                    $messageType = 'error';
                }
            }
        }
    }

    if ($action === 'delete') {
        $email = trim((string) ($_POST['email'] ?? ''));
        $emailKey = $normalizeEmail($email);

        $filtered = array_values(array_filter($admins, static function (array $admin) use ($normalizeEmail, $emailKey): bool {
            return $normalizeEmail((string) ($admin['email'] ?? '')) !== $emailKey;
        }));

        if (count($filtered) === count($admins)) {
            $message = 'Admin introuvable.';
            $messageType = 'error';
        } elseif (count($filtered) === 0) {
            $message = 'Vous ne pouvez pas supprimer le dernier admin.';
            $messageType = 'error';
        } elseif ($saveAdmins($filtered, $credentialsFile)) {
            $admins = $filtered;
            $message = 'Admin supprimé avec succès.';
        } else {
            $message = 'Impossible de sauvegarder le fichier des administrateurs.';
            $messageType = 'error';
        }
    }

    if ($action === 'update_password') {
        $email = trim((string) ($_POST['email'] ?? ''));
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $emailKey = $normalizeEmail($email);

        if ($newPassword === '') {
            $message = 'Veuillez saisir un nouveau mot de passe.';
            $messageType = 'error';
        } else {
            $updated = false;

            foreach ($admins as &$admin) {
                if ($normalizeEmail((string) ($admin['email'] ?? '')) === $emailKey) {
                    $admin['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updated = true;
                    break;
                }
            }
            unset($admin);

            if (!$updated) {
                $message = 'Admin introuvable.';
                $messageType = 'error';
            } elseif ($saveAdmins($admins, $credentialsFile)) {
                $message = 'Mot de passe mis à jour avec succès.';
            } else {
                $message = 'Impossible de sauvegarder le fichier des administrateurs.';
                $messageType = 'error';
            }
        }
    }

    if ($messageType === 'success' && $message !== '') {
        $loaded = require $credentialsFile;
        if (is_array($loaded)) {
            $admins = $loaded;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des admins - Enjah</title>
    <link rel="stylesheet" href="../professeur/nouvel.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        .admin-page { display:flex; flex-direction:column; gap:20px; }
        .admin-form .full { grid-column:1 / -1; }
        .message { margin-bottom:0; }
        .row-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
        .inline-form { display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin:0; }
        .inline-form input { min-width:220px; }
    </style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="logo">
            <img src="../professeur/enjah.png" alt="logo">
            <span class="brand-name">Admin</span>
        </div>
        <nav><ul>
            <li><a href="index.php">Tableau de bord</a></li>
            <li><a href="students.php">Étudiants</a></li>
            <li><a href="professors.php">Professeurs</a></li>
            <li><a href="payments.php">Paiements</a></li>
            <li><a href="chat.php">Support Chat</a></li>
            <li class="active"><a href="admins.php">Admins</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul></nav>
    </aside>

    <main class="main-content admin-page">
        <header class="header">
            <h1>Gestion des administrateurs</h1>
            <p>Ajoutez ou supprimez les comptes autorisés à se connecter au panneau admin.</p>
        </header>

        <?php if ($message !== ''): ?>
            <div class="message <?php echo htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <section class="panel">
            <h2>Ajouter un admin</h2>
            <form method="post" class="admin-form">
                <input type="hidden" name="action" value="add">
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="admin@enjah.com" required>
                </div>
                <div>
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                <div class="full">
                    <button type="submit">Ajouter l'admin</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <h2>Admins enregistrés</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Changer le mot de passe</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?php echo htmlspecialchars((string) ($admin['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <form method="post" class="inline-form">
                                    <input type="hidden" name="action" value="update_password">
                                    <input type="hidden" name="email" value="<?php echo htmlspecialchars((string) ($admin['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="password" name="new_password" placeholder="Nouveau mot de passe" required>
                                    <button type="submit">Mettre à jour</button>
                                </form>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <form method="post" onsubmit="return confirm('Supprimer cet admin ?');" style="margin:0;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="email" value="<?php echo htmlspecialchars((string) ($admin['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                        <button type="submit" class="danger-btn">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
</body>
</html>
