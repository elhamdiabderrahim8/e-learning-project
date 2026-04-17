<?php

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$cin = (string) ($_SESSION['CIN'] ?? '');
$profileName = trim((string) (($_SESSION['nom'] ?? '') . ' ' . ($_SESSION['prenom'] ?? '')));
if ($profileName === '') {
    $profileName = 'Etudiant';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reclamation - Enjah</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="reclamation.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../media/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../media/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../media/favicon_io/favicon-16x16.png">
    <link rel="shortcut icon" href="../media/favicon_io/favicon.ico">
    <link rel="manifest" href="../media/favicon_io/site.webmanifest">
</head>
<body>
    <div class="dashboard-container">
        <?php $active = 'reclamation'; require __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content reclamation-page">
            <section class="content-header reclamation-hero">
                <div>
                    <span class="eyebrow">Support professeur</span>
                    <h1>Envoyer une reclamation a l'admin</h1>
                    <p>Expliquez votre probleme, posez votre question ou signalez un blocage. Votre message sera transmis directement a l'administration.</p>
                </div>
                <div class="hero-note">
                    <strong>Canal direct</strong>
                    <span>Messagerie reliee au support admin</span>
                </div>
            </section>

            <section class="reclamation-layout">
                <aside class="support-info-card">
                    <h2>Avant d'envoyer</h2>
                    <ul>
                        <li>Choisissez un sujet clair pour accelerer le traitement.</li>
                        <li>Decrivez le probleme avec le plus de contexte possible.</li>
                        <li>Vous pourrez continuer la discussion dans ce meme fil.</li>
                    </ul>
                </aside>

                <section class="support-panel-page">
                    <div class="support-start-card" id="page-support-start">
                        <label for="page-support-subject">Sujet</label>
                        <input type="text" id="page-support-subject" value="Reclamation professeur" placeholder="Sujet de votre message">

                        <label for="page-support-message">Votre message</label>
                        <textarea id="page-support-message" rows="7" placeholder="Expliquez votre probleme, la page concernee et ce dont vous avez besoin..."></textarea>

                        <button type="button" class="btn-primary support-submit" onclick="pageSupportStart()">Envoyer a l'admin</button>
                        <button type="button" class="thread-refresh thread-history" onclick="pageSupportToggleHistory()">Voir mes conversations</button>
                        <div id="page-support-history" class="support-history-list" style="display:none;"></div>
                        <p class="support-hint" id="page-support-feedback">Le support reviendra dans ce fil des qu'une reponse sera disponible.</p>
                    </div>

                    <div class="support-thread-card" id="page-support-thread" style="display:none;">
                        <div class="thread-head">
                            <div>
                                <h2>Conversation avec l'admin</h2>
                                <p>Vous pouvez poursuivre les echanges ici.</p>
                            </div>
                            <div class="thread-actions">
                                <button type="button" class="thread-refresh" onclick="pageSupportLoad()">Actualiser</button>
                                <button type="button" class="thread-refresh thread-new" onclick="pageSupportNew()">Nouvelle reclamation</button>
                            </div>
                        </div>

                        <div id="page-support-messages" class="page-support-messages"></div>

                        <div class="thread-compose">
                            <input type="text" id="page-support-text" placeholder="Votre reponse..." onkeydown="if(event.key==='Enter'){ event.preventDefault(); pageSupportSend(); }">
                            <button type="button" class="btn-primary" onclick="pageSupportSend()">Envoyer</button>
                        </div>
                    </div>
                </section>
            </section>
        </main>
    </div>

    <script>
    (function () {
        const API_SEND = '../../../admin/api/send_message.php';
        const API_GET = '../../../admin/api/get_messages.php';
        const USER_ID = <?php echo json_encode($cin); ?>;
        const USER_TYPE = 'etudiant';
        const USER_NAME = <?php echo json_encode($profileName); ?>;
        const STORE_KEY = 'student_support_thread_' + USER_ID;
        const HISTORY_KEY = 'student_support_thread_history_' + USER_ID;
        let threadId = localStorage.getItem(STORE_KEY) || null;
        let threadHistory = loadHistory();
        let pollTimer = null;

        const startCard = document.getElementById('page-support-start');
        const threadCard = document.getElementById('page-support-thread');
        const feedback = document.getElementById('page-support-feedback');
        const messagesBox = document.getElementById('page-support-messages');
        const historyBox = document.getElementById('page-support-history');
        const historyToggleButton = document.querySelector('.thread-history');

        function showThread() {
            startCard.style.display = 'none';
            threadCard.style.display = 'block';
        }

        function showStart() {
            threadCard.style.display = 'none';
            startCard.style.display = 'block';
        }

        function escapeHtml(value) {
            return value
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function loadHistory() {
            try {
                const raw = localStorage.getItem(HISTORY_KEY);
                const parsed = raw ? JSON.parse(raw) : [];
                if (!Array.isArray(parsed)) {
                    return [];
                }

                const unique = [];
                const seen = [];
                parsed.forEach(function (item) {
                    const legacyId = typeof item === 'string' || typeof item === 'number' ? String(item).trim() : '';
                    const objectId = item && typeof item === 'object' ? String(item.id || '').trim() : '';
                    const value = objectId || legacyId;

                    if (!value || seen.indexOf(value) !== -1) {
                        return;
                    }

                    seen.push(value);
                    unique.push({
                        id: value,
                        firstSeen: item && typeof item === 'object' && item.firstSeen ? String(item.firstSeen) : new Date().toISOString()
                    });
                });

                return unique;
            } catch (error) {
                return [];
            }
        }

        function saveHistory() {
            localStorage.setItem(HISTORY_KEY, JSON.stringify(threadHistory.slice(0, 20)));
        }

        function rememberThread(id) {
            const value = String(id || '').trim();
            if (!value) {
                return;
            }

            const existing = threadHistory.find(function (item) {
                return item.id === value;
            });

            const entry = {
                id: value,
                firstSeen: existing && existing.firstSeen ? existing.firstSeen : new Date().toISOString()
            };

            threadHistory = [entry].concat(threadHistory.filter(function (item) {
                return item.id !== value;
            })).slice(0, 20);

            saveHistory();
        }

        function formatDate(value) {
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) {
                return '';
            }

            return date.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        function renderHistoryList() {
            historyBox.innerHTML = '';
            const count = threadHistory.length;

            if (historyToggleButton) {
                historyToggleButton.textContent = 'Voir mes conversations (' + count + ')';
            }

            if (!count) {
                historyBox.innerHTML = '<p class="support-history-empty">Aucune conversation precedente.</p>';
                return;
            }

            const summary = document.createElement('div');
            summary.className = 'support-history-summary';
            summary.textContent = 'Vous avez ' + count + ' conversation' + (count > 1 ? 's' : '');
            historyBox.appendChild(summary);

            threadHistory.forEach(function (item, index) {
                const row = document.createElement('div');
                row.className = 'support-history-item';

                const number = index + 1;
                const dateLabel = formatDate(item.firstSeen);
                const openButton = document.createElement('button');
                openButton.type = 'button';
                openButton.className = 'history-open';

                const title = document.createElement('span');
                title.className = 'history-title';
                title.textContent = 'Conversation ' + number;

                const subtitle = document.createElement('span');
                subtitle.className = 'history-sub';
                subtitle.textContent = dateLabel ? ('Creee le ' + dateLabel) : 'Conversation precedente';

                openButton.appendChild(title);
                openButton.appendChild(subtitle);
                openButton.addEventListener('click', function () {
                    pageSupportOpenThread(item.id);
                });

                const deleteButton = document.createElement('button');
                deleteButton.type = 'button';
                deleteButton.className = 'history-delete';
                deleteButton.textContent = 'Supprimer';
                deleteButton.addEventListener('click', function () {
                    pageSupportDeleteThread(item.id);
                });

                row.appendChild(openButton);
                row.appendChild(deleteButton);
                historyBox.appendChild(row);
            });
        }

        window.pageSupportStart = async function () {
            const subject = document.getElementById('page-support-subject').value.trim() || 'Reclamation professeur';
            const message = document.getElementById('page-support-message').value.trim();

            if (!message) {
                feedback.textContent = 'Veuillez saisir votre message avant l\'envoi.';
                return;
            }

            feedback.textContent = 'Envoi en cours...';

            try {
                const response = await fetch(API_SEND, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        user_id: USER_ID,
                        user_type: USER_TYPE,
                        user_name: USER_NAME,
                        subject: subject,
                        message: message
                    })
                });

                const data = await response.json();
                if (!data.success) {
                    throw new Error('Erreur lors de l\'envoi');
                }

                threadId = data.thread_id;
                rememberThread(threadId);
                localStorage.setItem(STORE_KEY, threadId);
                document.getElementById('page-support-message').value = '';
                showThread();
                await pageSupportLoad();
                startPolling();
            } catch (error) {
                feedback.textContent = 'Impossible d\'envoyer le message pour le moment.';
            }
        };

        window.pageSupportLoad = async function () {
            if (!threadId) {
                return;
            }

            try {
                const response = await fetch(API_GET + '?thread_id=' + encodeURIComponent(threadId));
                const items = await response.json();
                messagesBox.innerHTML = '';

                items.forEach(function (item) {
                    const block = document.createElement('div');
                    block.className = 'page-msg ' + (item.sender === 'admin' ? 'admin' : 'user');
                    block.innerHTML = '<div class="page-msg-text">' + escapeHtml(String(item.message || '')).replace(/\n/g, '<br>') + '</div>'
                        + '<div class="page-msg-time">' + escapeHtml(String(item.created_at || '')) + '</div>';
                    messagesBox.appendChild(block);
                });

                messagesBox.scrollTop = messagesBox.scrollHeight;
            } catch (error) {
                messagesBox.innerHTML = '<div class="page-msg system">Le chargement des messages a echoue.</div>';
            }
        };

        window.pageSupportSend = async function () {
            const input = document.getElementById('page-support-text');
            const message = input.value.trim();

            if (!message || !threadId) {
                return;
            }

            input.value = '';

            try {
                await fetch(API_SEND, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        user_id: USER_ID,
                        user_type: USER_TYPE,
                        user_name: USER_NAME,
                        subject: 'Reclamation professeur',
                        message: message,
                        thread_id: threadId
                    })
                });
                await pageSupportLoad();
            } catch (error) {
                input.value = message;
            }
        };

        window.pageSupportToggleHistory = function () {
            const isOpen = historyBox.style.display === 'block';
            if (isOpen) {
                historyBox.style.display = 'none';
                return;
            }

            renderHistoryList();
            historyBox.style.display = 'block';
        };

        window.pageSupportOpenThread = async function (id) {
            threadId = String(id || '').trim();
            if (!threadId) {
                return;
            }

            rememberThread(threadId);
            localStorage.setItem(STORE_KEY, threadId);
            historyBox.style.display = 'none';
            showThread();
            await pageSupportLoad();
            startPolling();
        };

        window.pageSupportDeleteThread = function (id) {
            const value = String(id || '').trim();
            if (!value) {
                return;
            }

            const accepted = window.confirm('Supprimer cette conversation de votre liste ?');
            if (!accepted) {
                return;
            }

            threadHistory = threadHistory.filter(function (item) {
                return item.id !== value;
            });

            saveHistory();
            renderHistoryList();

            if (threadId === value) {
                threadId = null;
                localStorage.removeItem(STORE_KEY);
                if (pollTimer) {
                    clearInterval(pollTimer);
                    pollTimer = null;
                }

                messagesBox.innerHTML = '';
                document.getElementById('page-support-text').value = '';
                showStart();
                feedback.textContent = 'Conversation supprimee de la liste.';
            }
        };

        window.pageSupportNew = function () {
            if (threadId) {
                rememberThread(threadId);
            }

            threadId = null;
            localStorage.removeItem(STORE_KEY);
            if (pollTimer) {
                clearInterval(pollTimer);
                pollTimer = null;
            }

            messagesBox.innerHTML = '';
            document.getElementById('page-support-text').value = '';
            historyBox.style.display = 'none';
            feedback.textContent = 'Vous pouvez maintenant envoyer une nouvelle reclamation.';
            showStart();
            document.getElementById('page-support-message').focus();
        };

        function startPolling() {
            if (pollTimer) {
                clearInterval(pollTimer);
            }

            pollTimer = setInterval(function () {
                if (threadId) {
                    pageSupportLoad();
                }
            }, 5000);
        }

        if (threadId) {
            rememberThread(threadId);
            showThread();
            pageSupportLoad();
            startPolling();
        }

        renderHistoryList();
    })();
    </script>
</body>
</html>


