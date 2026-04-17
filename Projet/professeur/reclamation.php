<?php
session_start();
require_once __DIR__ . '/../kmr/student/backend/config/database.php';

if (!isset($_SESSION['CIN'])) {
    header('Location: login.html');
    exit();
}

$pdo = db();
$cin = (string) $_SESSION['CIN'];
$profileName = trim((string) (($_SESSION['nom'] ?? '') . ' ' . ($_SESSION['prenom'] ?? '')));
$profileSrc = 'profil.avif';

try {
    $stmtProf = $pdo->prepare('SELECT nom, prenom, data, type FROM professeur WHERE CIN = :cin LIMIT 1');
    $stmtProf->execute(['cin' => $cin]);
    $prof = $stmtProf->fetch(PDO::FETCH_ASSOC) ?: [];

    if ($profileName === '') {
        $profileName = trim((string) (($prof['nom'] ?? '') . ' ' . ($prof['prenom'] ?? '')));
    }

    if (!empty($prof['data']) && !empty($prof['type'])) {
        $profileSrc = 'data:' . $prof['type'] . ';base64,' . base64_encode($prof['data']);
    }
} catch (Throwable $e) {
    // Keep fallback profile values if loading the avatar fails.
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reclamation - ENJAH</title>
    <link rel="stylesheet" href="nouvel.css">
    <link rel="stylesheet" href="reclamation.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="topbar">
            <div class="container topbar-inner">
                <a class="brand" href="index.php" aria-label="Accueil professeur">
                    <img src="enjah.png" alt="Logo Enjah">
                    <span>Professeur</span>
                </a>

                <nav class="nav-links" aria-label="Navigation professeur">
                    <a href="offres.php">Vos Cours</a>
                    <a href="valider_certificats.php">Certificats</a>
                    <a href="reclamation.php" class="is-active">Reclamation</a>
                    <a href="infos.php">Mes Infos</a>
                </nav>

                <div class="top-actions">
                    <details class="profile-menu">
                        <summary class="profile-trigger" aria-label="Ouvrir le menu profil" title="Mon Profil">
                            <img src="<?php echo htmlspecialchars($profileSrc, ENT_QUOTES, 'UTF-8'); ?>" class="nav-avatar" alt="Profil">
                        </summary>
                        <div class="profile-dropdown" role="menu" aria-label="Menu profil">
                            <div class="profile-dropdown-header">
                                <div class="profile-dropdown-name"><?php echo htmlspecialchars($profileName !== '' ? $profileName : 'Professeur', ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="profile-dropdown-sub">Professeur</div>
                            </div>
                            <a href="infos.php" role="menuitem">Voir profil</a>
                            <a href="infos.php" role="menuitem">Mes infos</a>
                            <a href="logout.php" class="danger" role="menuitem">Se deconnecter</a>
                        </div>
                    </details>
                </div>
            </div>
        </header>

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
                    <div class="support-meta">
                        <span class="meta-badge">Admin support</span>
                        <span class="meta-badge meta-soft">Espace professeur</span>
                    </div>
                </aside>

                <section class="support-panel-page">
                    <div class="support-start-card" id="page-support-start">
                        <label for="page-support-subject">Sujet</label>
                        <input type="text" id="page-support-subject" value="Reclamation professeur" placeholder="Sujet de votre message">

                        <label for="page-support-message">Votre message</label>
                        <textarea id="page-support-message" rows="7" placeholder="Expliquez votre probleme, la page concernee et ce dont vous avez besoin..."></textarea>

                        <button type="button" class="btn-primary support-submit" onclick="pageSupportStart()">Envoyer a l'admin</button>
                        <p class="support-hint" id="page-support-feedback">Le support reviendra dans ce fil des qu'une reponse sera disponible.</p>
                    </div>

                    <div class="support-thread-card" id="page-support-thread" style="display:none;">
                        <div class="thread-head">
                            <div>
                                <h2>Conversation avec l'admin</h2>
                                <p>Vous pouvez poursuivre les echanges ici.</p>
                            </div>
                            <button type="button" class="thread-refresh" onclick="pageSupportLoad()">Actualiser</button>
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
        const API_SEND = '../admin/api/send_message.php';
        const API_GET = '../admin/api/get_messages.php';
        const USER_ID = <?php echo json_encode($cin); ?>;
        const USER_TYPE = 'professeur';
        const USER_NAME = <?php echo json_encode($profileName !== '' ? $profileName : 'Professeur'); ?>;
        const STORE_KEY = 'prof_support_thread_' + USER_ID;
        let threadId = localStorage.getItem(STORE_KEY) || null;
        let pollTimer = null;

        const startCard = document.getElementById('page-support-start');
        const threadCard = document.getElementById('page-support-thread');
        const feedback = document.getElementById('page-support-feedback');
        const messagesBox = document.getElementById('page-support-messages');

        function showThread() {
            startCard.style.display = 'none';
            threadCard.style.display = 'block';
        }

        function escapeHtml(value) {
            return value
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
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
            showThread();
            pageSupportLoad();
            startPolling();
        }
    })();
    </script>
</body>
</html>
