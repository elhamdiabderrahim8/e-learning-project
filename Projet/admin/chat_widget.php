<?php
/**
 * Support Chat Widget
 * Before including, define:
 *   $chat_user_id   - the user's CIN or ID
 *   $chat_user_type - 'etudiant' or 'professeur'
 *   $chat_user_name - display name
 */

// Derive the URL to the admin/api folder from THIS file's location
// Works no matter which page includes this widget
$_widget_doc_root = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
$_widget_file_dir = rtrim(str_replace('\\', '/', dirname(__FILE__)), '/');
$_widget_api_url  = str_replace($_widget_doc_root, '', $_widget_file_dir) . '/api';
?>
<style>
#support-fab {
    position:fixed; bottom:24px; right:24px; z-index:9999;
    width:56px; height:56px; background:#4d68e1; border-radius:50%; border:none;
    cursor:pointer; box-shadow:0 4px 16px rgba(79,70,229,.4);
    display:flex; align-items:center; justify-content:center; transition:.2s;
}
#support-fab:hover { background:#3b52c1; transform:scale(1.08); }
#support-fab svg { width:26px; height:26px; fill:#fff; }

#support-panel {
    position:fixed; bottom:90px; right:24px; z-index:9999; width:340px;
    max-height:480px; background:#fff; border-radius:16px;
    box-shadow:0 8px 32px rgba(0,0,0,.15);
    display:none; flex-direction:column; overflow:hidden;
}
#support-panel.open { display:flex; }
#sp-header {
    background:#4d68e1; color:#fff; padding:14px 16px;
    font-weight:600; font-size:.95rem;
    display:flex; justify-content:space-between; align-items:center;
}
#sp-header button { background:none; border:none; color:#fff; cursor:pointer; font-size:1.2rem; line-height:1; }
#sp-start { padding:16px; }
#sp-start input, #sp-start textarea {
    width:100%; padding:8px 10px; border:1px solid #e2e8f0;
    border-radius:8px; margin-bottom:10px; font-size:.88rem; box-sizing:border-box;
    font-family:inherit;
}
#sp-start button {
    width:100%; padding:10px; background:#4d68e1; color:#fff;
    border:none; border-radius:8px; font-weight:600; cursor:pointer;
}
#sp-messages {
    flex:1; overflow-y:auto; padding:12px;
    display:flex; flex-direction:column; gap:8px;
}
.sp-msg {
    max-width:75%; padding:8px 12px; border-radius:10px;
    font-size:.86rem; line-height:1.45; word-break:break-word;
}
.sp-msg.user  { background:rgba(77,104,225,.1); align-self:flex-end; }
.sp-msg.admin { background:#f1f5f9; align-self:flex-start; }
.sp-msg .sp-time { font-size:.7rem; opacity:.55; margin-top:3px; }
#sp-input-row { border-top:1px solid #e2e8f0; padding:10px 12px; display:flex; gap:8px; }
#sp-input-row input {
    flex:1; padding:8px 10px; border:1px solid #e2e8f0;
    border-radius:8px; font-size:.88rem; font-family:inherit;
}
#sp-input-row button {
    padding:8px 14px; background:#4d68e1; color:#fff;
    border:none; border-radius:8px; cursor:pointer; font-size:1rem;
}
</style>

<button id="support-fab" onclick="spToggle()" title="Support">
    <svg viewBox="0 0 24 24"><path d="M12 3C6.48 3 2 6.92 2 11.7c0 2.65 1.35 5.04 3.5 6.65V21l3.72-1.95c.88.24 1.81.38 2.78.38 5.52 0 10-3.92 10-8.73S17.52 3 12 3z"/></svg>
</button>

<div id="support-panel">
    <div id="sp-header">
        💬 Support
        <button onclick="spToggle()">✕</button>
    </div>
    <div id="sp-start">
        <input type="text" id="sp-subject" placeholder="Sujet" value="Question">
        <textarea id="sp-first-msg" rows="3" placeholder="Écrivez votre message..."></textarea>
        <button onclick="spStart()">Envoyer</button>
    </div>
    <div id="sp-messages" style="display:none;"></div>
    <div id="sp-input-row" style="display:none;">
        <input type="text" id="sp-text" placeholder="Votre message..."
               onkeydown="if(event.key==='Enter' && !event.shiftKey){ event.preventDefault(); spSend(); }">
        <button onclick="spSend()">➤</button>
    </div>
</div>

<script>
(function(){
    // All config is injected from PHP — no path guessing in JS
    const API_SEND = '<?= $_widget_api_url ?>/send_message.php';
    const API_GET  = '<?= $_widget_api_url ?>/get_messages.php';
    const USER_ID   = <?= json_encode((string)($chat_user_id   ?? '')) ?>;
    const USER_TYPE = <?= json_encode($chat_user_type ?? 'etudiant') ?>;
    const USER_NAME = <?= json_encode($chat_user_name ?? 'Utilisateur') ?>;
    const STORE_KEY = 'sp_thread_' + USER_ID;

    let threadId  = localStorage.getItem(STORE_KEY) || null;
    let pollTimer = null;

    window.spToggle = function() {
        const p = document.getElementById('support-panel');
        p.classList.toggle('open');
        if (p.classList.contains('open') && threadId) spLoad();
    };

    window.spStart = async function() {
        const subject = document.getElementById('sp-subject').value.trim() || 'Support';
        const message = document.getElementById('sp-first-msg').value.trim();
        if (!message) return;

        const res  = await fetch(API_SEND, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: USER_ID, user_type: USER_TYPE,
                                   user_name: USER_NAME, subject, message })
        });
        const data = await res.json();
        if (data.success) {
            threadId = data.thread_id;
            localStorage.setItem(STORE_KEY, threadId);
            spShowChat();
            spLoad();
            spPoll();
        }
    };

    window.spSend = async function() {
        const input   = document.getElementById('sp-text');
        const message = input.value.trim();
        if (!message || !threadId) return;
        input.value = '';
        await fetch(API_SEND, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: USER_ID, user_type: USER_TYPE,
                                   user_name: USER_NAME, subject: 'Support',
                                   message, thread_id: threadId })
        });
        spLoad();
    };

    async function spLoad() {
        if (!threadId) return;
        const res  = await fetch(API_GET + '?thread_id=' + threadId);
        const msgs = await res.json();
        const box  = document.getElementById('sp-messages');
        box.innerHTML = '';
        msgs.forEach(function(m) {
            const d = document.createElement('div');
            d.className = 'sp-msg ' + (m.sender === 'admin' ? 'admin' : 'user');
            d.innerHTML = m.message.replace(/\n/g,'<br>')
                        + '<div class="sp-time">' + m.created_at + '</div>';
            box.appendChild(d);
        });
        box.scrollTop = box.scrollHeight;
    }

    function spShowChat() {
        document.getElementById('sp-start').style.display    = 'none';
        document.getElementById('sp-messages').style.display = 'flex';
        document.getElementById('sp-input-row').style.display= 'flex';
    }

    function spPoll() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(function(){
            if (document.getElementById('support-panel').classList.contains('open')) spLoad();
        }, 5000);
    }

    // Restore existing session
    if (threadId) {
        spShowChat();
        spPoll();
    }
})();
</script>
