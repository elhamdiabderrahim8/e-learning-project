<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../professeur/config/connexion.php';

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
    <style>
        .chat-layout { display:flex; gap:0; height:calc(100vh - 120px); border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; background:#fff; }
        .thread-list { width:280px; border-right:1px solid #e2e8f0; overflow-y:auto; flex-shrink:0; }
        .thread-item { padding:14px 16px; cursor:pointer; border-bottom:1px solid #f1f5f9; transition:.15s; }
        .thread-item:hover { background:#f8fafc; }
        .thread-item.active { background:rgba(77,104,225,.08); border-left:3px solid #4d68e1; }
        .thread-item .tname { font-weight:600; color:#2d3748; font-size:.9rem; }
        .thread-item .tsub { color:#718096; font-size:.8rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .thread-item .tbadge { background:#ef4444; color:#fff; border-radius:999px; padding:1px 7px; font-size:.72rem; float:right; }
        .chat-body { flex:1; display:flex; flex-direction:column; }
        .chat-messages { flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:10px; }
        .msg { max-width:65%; padding:10px 14px; border-radius:12px; font-size:.9rem; line-height:1.5; }
        .msg.user { background:#f1f5f9; align-self:flex-start; border-bottom-left-radius:2px; }
        .msg.admin { background:#4d68e1; color:#fff; align-self:flex-end; border-bottom-right-radius:2px; }
        .msg .meta { font-size:.72rem; opacity:.65; margin-top:4px; }
        .chat-input { border-top:1px solid #e2e8f0; padding:12px 16px; display:flex; gap:8px; }
        .chat-input textarea { flex:1; padding:10px 12px; border:1px solid #cbd5e0; border-radius:8px; resize:none; font-size:.9rem; height:48px; }
        .chat-input button { padding:10px 20px; background:#4d68e1; color:#fff; border:none; border-radius:8px; font-weight:600; cursor:pointer; }
        .no-thread { flex:1; display:flex; align-items:center; justify-content:center; color:#a0aec0; font-size:1rem; }
        .thread-header { padding:14px 16px; border-bottom:1px solid #e2e8f0; background:#f8fafc; }
        .thread-header strong { color:#2d3748; } .thread-header small { color:#718096; }
        .badge-type { display:inline-block; padding:2px 8px; border-radius:999px; font-size:.72rem; font-weight:600; }
        .badge-type.etudiant { background:#dbeafe; color:#1d4ed8; }
        .badge-type.professeur { background:#dcfce7; color:#166534; }
    </style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="logo"><img src="../professeur/enjah.png" alt="logo" style="height:28px;"><span class="brand-name">Admin</span></div>
        <nav><ul>
            <li><a href="index.php">Tableau de bord</a></li>
            <li><a href="students.php">Étudiants</a></li>
            <li><a href="professors.php">Professeurs</a></li>
            <li><a href="payments.php">Paiements</a></li>
            <li class="active"><a href="chat.php">Support Chat</a></li>
            <li><a href="logout.php" style="color:#ef4444;">Déconnexion</a></li>
        </ul></nav>
    </aside>
    <main class="main-content">
        <header class="header"><h1>Support Chat</h1></header>
        <div class="chat-layout">
            <div class="thread-list">
                <?php if ($threads && $threads->num_rows > 0): ?>
                <?php while ($t = $threads->fetch_assoc()): ?>
                <div class="thread-item <?=$t['id']==$active_thread?'active':''?>" onclick="location='chat.php?thread=<?=$t['id']?>'">
                    <?php if ($t['unread'] > 0): ?><span class="tbadge"><?=$t['unread']?></span><?php endif; ?>
                    <div class="tname"><?=htmlspecialchars($t['user_name'])?></div>
                    <div class="tsub"><?=htmlspecialchars($t['subject'])?></div>
                    <div style="margin-top:4px;"><span class="badge-type <?=$t['user_type']?>"><?=$t['user_type']?></span></div>
                </div>
                <?php endwhile; ?>
                <?php else: ?>
                <div style="padding:20px;color:#a0aec0;font-size:.9rem;text-align:center;">Aucune conversation</div>
                <?php endif; ?>
            </div>
            <?php if ($active_thread && $thread_info): ?>
            <div class="chat-body">
                <div class="thread-header">
                    <strong><?=htmlspecialchars($thread_info['subject'])?></strong>
                    <small> — <?=htmlspecialchars($thread_info['user_name'])?></small>
                    <span class="badge-type <?=$thread_info['user_type']?>" style="margin-left:8px;"><?=$thread_info['user_type']?></span>
                </div>
                <div class="chat-messages" id="chatMessages">
                    <?php foreach ($messages as $m): ?>
                    <div class="msg <?=$m['sender']==='admin'?'admin':'user'?>">
                        <?=nl2br(htmlspecialchars($m['message']))?>
                        <div class="meta"><?=$m['sender']==='admin'?'Admin':htmlspecialchars($thread_info['user_name'])?> · <?=$m['created_at']?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($messages)): ?><div style="color:#a0aec0;text-align:center;margin-top:40px;">Aucun message</div><?php endif; ?>
                </div>
                <form class="chat-input" method="POST">
                    <input type="hidden" name="reply" value="1">
                    <input type="hidden" name="thread_id" value="<?=$active_thread?>">
                    <textarea name="message" placeholder="Votre réponse..." required></textarea>
                    <button type="submit">Envoyer</button>
                </form>
            </div>
            <?php else: ?>
            <div class="no-thread">Sélectionnez une conversation</div>
            <?php endif; ?>
        </div>
    </main>
</div>
<script>
const cm = document.getElementById('chatMessages');
if (cm) cm.scrollTop = cm.scrollHeight;
</script>
</body>
</html>
