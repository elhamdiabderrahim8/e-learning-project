<?php

declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();
$cinEtudiant = (int) ($_SESSION['CIN'] ?? 0);

$messages = [];
$supportReady = true;
try {
    $stmt = $pdo->prepare('SELECT sender, message, created_at FROM support_messages WHERE student_cin = :cin ORDER BY id ASC LIMIT 100');
    $stmt->execute(['cin' => $cinEtudiant]);
    $messages = $stmt->fetchAll();
} catch (Throwable $e) {
    $supportReady = false;
}

$error = get_flash('error');
$success = get_flash('success');

?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - Enjah</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../media/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../media/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../media/favicon_io/favicon-16x16.png">
    <link rel="shortcut icon" href="../media/favicon_io/favicon.ico">
    <link rel="manifest" href="../media/favicon_io/site.webmanifest">
</head>
<body>
    <div class="dashboard-container">
        <?php $active = 'support'; require __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <h1>Support</h1>
                <p>Prototype: envoyez un message, un admin repondra plus tard.</p>
            </header>

            <?php if (!$supportReady): ?>
                <div class="task-alert task-alert-error">
                    Table <code>support_messages</code> manquante. Importez la migration SQL dans
                    <code>projet/taki/database/migrations/2026-04-04_support_and_remove_users.sql</code>.
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="task-alert task-alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="task-alert task-alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <section class="support-shell">
                <div class="support-thread card">
                    <?php if (!$messages): ?>
                        <p style="color: var(--muted); margin: 0;">Aucun message pour le moment. Expliquez votre probleme et envoyez votre premier message.</p>
                    <?php endif; ?>

                    <div class="support-messages" id="support-messages">
                        <?php foreach ($messages as $msg): ?>
                            <?php
                            $sender = (string) ($msg['sender'] ?? 'student');
                            $isAdmin = $sender === 'admin';
                            $className = $isAdmin ? 'support-bubble support-bubble-admin' : 'support-bubble support-bubble-student';
                            $timestamp = (string) ($msg['created_at'] ?? '');
                            ?>
                            <div class="<?php echo $className; ?>">
                                <div class="support-bubble-meta">
                                    <strong><?php echo $isAdmin ? 'Admin' : 'Vous'; ?></strong>
                                    <?php if ($timestamp !== ''): ?>
                                        <span><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($timestamp)), ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="support-bubble-text">
                                    <?php echo nl2br(htmlspecialchars((string) ($msg['message'] ?? ''), ENT_QUOTES, 'UTF-8')); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="support-compose card">
                    <h2 style="margin-top: 0;">Envoyer un message</h2>
                    <form action="../backend/actions/create_support_message.php" method="post" class="support-form">
                        <div class="input-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="5" placeholder="Decrivez votre probleme (prototype)..." required></textarea>
                        </div>
                        <input type="submit" class="btn-primary" value="Envoyer">
                    </form>
                </div>
            </section>

            <script>
                (function () {
                    const container = document.getElementById('support-messages');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                })();
            </script>
        </main>
    </div>
</body>
</html>
