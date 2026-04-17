<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../professeur/config/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_thread'])) {
    $thread_id = (int) ($_POST['thread_id'] ?? 0);
    if ($thread_id > 0) {
        $stmtDeleteMsgs = $conn->prepare('DELETE FROM support_messages WHERE thread_id = ?');
        $stmtDeleteMsgs->bind_param('i', $thread_id);
        $stmtDeleteMsgs->execute();
        $stmtDeleteMsgs->close();

        $stmtDeleteThread = $conn->prepare('DELETE FROM support_threads WHERE id = ?');
        $stmtDeleteThread->bind_param('i', $thread_id);
        $stmtDeleteThread->execute();
        $stmtDeleteThread->close();
    }

    header('Location: chat.php');
    exit();
}

// Handle admin reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $thread_id  = (int)$_POST['thread_id'];
    $message    = trim($_POST['message']);
    if ($message !== '') {
        $stmt = $conn->prepare("INSERT INTO support_messages (thread_id, sender, message) VALUES (?,?,?)");
        $sender = 'admin';
        $stmt->bind_param("iss", $thread_id, $sender, $message);
        $stmt->execute();
        $stmt->close();
    }
    // Mark all user messages in this thread as read
    $conn->query("UPDATE support_messages SET admin_read=1 WHERE thread_id=$thread_id AND sender!='admin'");
    header("Location: chat.php?thread=$thread_id");
    exit();
}

// Mark thread as read when opened
$active_thread = isset($_GET['thread']) ? (int)$_GET['thread'] : 0;
if ($active_thread) {
    $conn->query("UPDATE support_messages SET admin_read=1 WHERE thread_id=$active_thread AND sender!='admin'");
}

// Get all threads
$threads = $conn->query("
    SELECT t.id, t.subject, t.user_type, t.user_name, t.created_at,
           COUNT(m.id) as msg_count,
           SUM(CASE WHEN m.sender!='admin' AND m.admin_read=0 THEN 1 ELSE 0 END) as unread
    FROM support_threads t
    LEFT JOIN support_messages m ON m.thread_id = t.id
    GROUP BY t.id ORDER BY t.created_at DESC
");

// Get messages for active thread
$messages = [];
$thread_info = null;
if ($active_thread) {
    $thread_info = $conn->query("SELECT * FROM support_threads WHERE id=$active_thread")->fetch_assoc();
    $msgs = $conn->query("SELECT * FROM support_messages WHERE thread_id=$active_thread ORDER BY created_at ASC");
    while ($m = $msgs->fetch_assoc()) $messages[] = $m;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Support Chat - Admin</title>
    <link rel="stylesheet" href="../professeur/nouvel.css">
    <link rel="stylesheet" href="admin.css">
    <style>
    </style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="logo"><img src="../professeur/enjah.png" alt="logo"><span class="brand-name">Admin</span></div>
        <nav><ul>
            <li><a href="index.php">Tableau de bord</a></li>
            <li><a href="students.php">Étudiants</a></li>
            <li><a href="professors.php">Professeurs</a></li>
            <li><a href="payments.php">Paiements</a></li>
            <li class="active"><a href="chat.php">Support Chat</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul></nav>
    </aside>
    <main class="main-content">
        <header class="header"><h1>Support Chat</h1></header>
        <div class="chat-layout">
            <div class="thread-list">
                <?php if ($threads && $threads->num_rows > 0): ?>
                <?php while ($t = $threads->fetch_assoc()): ?>
                <div class="thread-item <?=$t['id']==$active_thread?'active':''?>" onclick="location='chat.php?thread=<?=$t['id']?>'" role="button" tabindex="0" onkeydown="if(event.key==='Enter'){location='chat.php?thread=<?=$t['id']?>';}">
                    <?php if ($t['unread'] > 0): ?><span class="tbadge"><?=$t['unread']?></span><?php endif; ?>
                    <div class="tname"><?=htmlspecialchars($t['user_name'])?></div>
                    <div class="tsub"><?=htmlspecialchars($t['subject'])?></div>
                    <div style="margin-top:4px;"><span class="badge-type <?=$t['user_type']?>"><?=$t['user_type']?></span></div>
                </div>
                <?php endwhile; ?>
                <?php else: ?>
                <div class="thread-empty">
                    <div class="empty-icon" aria-hidden="true"></div>
                    <h3>Aucune conversation</h3>
                    <p>Les nouveaux messages de support apparaîtront ici.</p>
                </div>
                <?php endif; ?>
            </div>
            <?php if ($active_thread && $thread_info): ?>
            <div class="chat-body">
                <div class="thread-header">
                    <div class="thread-header-main">
                        <strong><?=htmlspecialchars($thread_info['subject'])?></strong>
                        <small> — <?=htmlspecialchars($thread_info['user_name'])?></small>
                        <span class="badge-type <?=$thread_info['user_type']?>" style="margin-left:8px;"><?=$thread_info['user_type']?></span>
                    </div>
                    <div class="thread-controls">
                        <a href="chat.php" class="thread-close">Fermer</a>
                        <form method="POST" onsubmit="return confirm('Supprimer cette conversation ?');" class="thread-delete-form">
                            <input type="hidden" name="delete_thread" value="1">
                            <input type="hidden" name="thread_id" value="<?=$active_thread?>">
                            <button type="submit" class="thread-delete">Supprimer</button>
                        </form>
                    </div>
                </div>
                <div class="chat-messages" id="chatMessages">
                    <?php foreach ($messages as $m): ?>
                    <div class="msg <?=$m['sender']==='admin'?'admin':'user'?>">
                        <?=nl2br(htmlspecialchars($m['message']))?>
                        <div class="meta"><?=$m['sender']==='admin'?'Admin':htmlspecialchars($thread_info['user_name'])?> · <?=$m['created_at']?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($messages)): ?><div class="message-empty">Aucun message</div><?php endif; ?>
                </div>
                <form class="chat-input" method="POST" id="chatForm">
                    <input type="hidden" name="reply" value="1">
                    <input type="hidden" name="thread_id" value="<?=$active_thread?>">
                    <textarea name="message" id="chatMessageInput" placeholder="Votre réponse..." required></textarea>
                    <button type="submit">Envoyer</button>
                </form>
            </div>
            <?php else: ?>
            <div class="no-thread">
                <div class="empty-icon" aria-hidden="true"></div>
                <h3>Sélectionnez une conversation</h3>
                <p>Choisissez un expéditeur dans la liste pour afficher les messages.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<script>
const cm = document.getElementById('chatMessages');
if (cm) cm.scrollTop = cm.scrollHeight;

const chatInput = document.getElementById('chatMessageInput');
const chatForm = document.getElementById('chatForm');

if (chatInput && chatForm) {
    chatInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            if (chatInput.value.trim() !== '') {
                chatForm.submit();
            }
        }
    });
}
</script>
</body>
</html>
