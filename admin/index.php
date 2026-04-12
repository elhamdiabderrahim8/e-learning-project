<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../professeur/config/connexion.php';

$nb_etudiants = $conn->query("SELECT COUNT(*) as n FROM etudiant")->fetch_assoc()['n'];
$nb_profs     = $conn->query("SELECT COUNT(*) as n FROM professeur")->fetch_assoc()['n'];
$nb_cours     = $conn->query("SELECT COUNT(*) as n FROM cours")->fetch_assoc()['n'];
$res = $conn->query("SELECT COUNT(*) as n FROM support_messages WHERE admin_read=0");
$nb_messages  = $res ? $res->fetch_assoc()['n'] : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Enjah</title>
        <div class="logo">
            <a href="../index.php" style="display:flex;align-items:center;text-decoration:none;color:inherit;">
            <img src="../professeur/enjah.png" alt="logo" style="height:28px;">
        .overview { display:flex; gap:16px; margin-bottom:25px; flex-wrap:wrap; }
            </a>
        .overview-card { flex:1; min-width:180px; background:#fff; border-radius:12px; padding:20px;
            box-shadow:var(--shadow); border:1px solid #e2e8f0; text-align:center; }
            <li><a href="../index.php">Hub projet</a></li>
        .overview-card .num { font-size:2rem; font-weight:700; color:#4f46e5; }
        .overview-card .label { color:#718096; font-size:.9rem; margin-top:4px; }
        .badge { background:#ef4444; color:#fff; border-radius:999px; padding:2px 8px; font-size:.75rem; margin-left:6px; }
        .card-link { flex:1; min-width:200px; padding:16px; background:#fff; border-radius:12px;
            border:1px solid #e2e8f0; text-decoration:none; color:#2d3748;
            box-shadow:var(--shadow); display:block; transition:.2s; }
        .card-link:hover { border-color:#4f46e5; }
    </style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="logo">
            <img src="../professeur/enjah.png" alt="logo" style="height:28px;">
            <span class="brand-name">Admin</span>
        </div>
        <nav><ul>
            <li class="active"><a href="index.php">Tableau de bord</a></li>
            <li><a href="students.php">Étudiants</a></li>
            <li><a href="professors.php">Professeurs</a></li>
            <li><a href="payments.php">Paiements</a></li>
            <li><a href="chat.php">Support Chat <?php if($nb_messages>0): ?><span class="badge"><?=$nb_messages?></span><?php endif; ?></a></li>
            <li><a href="logout.php" style="color:#ef4444;">Déconnexion</a></li>
        </ul></nav>
    </aside>
    <main class="main-content">
        <header class="header">
            <h1>Tableau de bord</h1>
            <p>Connecté en tant que <strong><?=htmlspecialchars($_SESSION['admin_email'])?></strong></p>
        </header>
        <div class="overview">
            <div class="overview-card"><div class="num"><?=$nb_etudiants?></div><div class="label">👩‍🎓 Étudiants</div></div>
            <div class="overview-card"><div class="num"><?=$nb_profs?></div><div class="label">👨‍🏫 Professeurs</div></div>
            <div class="overview-card"><div class="num"><?=$nb_cours?></div><div class="label">📚 Cours</div></div>
            <div class="overview-card"><div class="num"><?=$nb_messages?></div><div class="label">💬 Non lus</div></div>
        </div>
        <div style="display:flex;gap:16px;flex-wrap:wrap;">
            <a href="../index.php" class="card-link"><strong>🏠 Hub projet</strong><br><small style="color:#718096;">Retourner au point d'entrée principal</small></a>
            <a href="students.php" class="card-link"><strong>👩‍🎓 Gérer les étudiants</strong><br><small style="color:#718096;">Voir, supprimer des étudiants</small></a>
            <a href="professors.php" class="card-link"><strong>👨‍🏫 Gérer les professeurs</strong><br><small style="color:#718096;">Voir, supprimer des professeurs</small></a>
            <a href="chat.php" class="card-link"><strong>💬 Support Chat</strong><br><small style="color:#718096;">Répondre aux messages</small></a>
        </div>
    </main>
</div>
</body>
</html>
