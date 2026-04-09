<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../professeur/config/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_cin'])) {
    $cin = (int)$_POST['delete_cin'];
    $stmt = $conn->prepare("DELETE FROM professeur WHERE CIN=?");
    $stmt->bind_param("i", $cin);
    $stmt->execute();
    $stmt->close();
    $success = "Professeur supprimé avec succès.";
}

$sql = "SELECT CIN, nom, prenom, name, type FROM professeur ORDER BY nom, prenom";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Professeurs - Admin</title>
    <link rel="stylesheet" href="../professeur/nouvel.css">
    <style>
        table { width:100%; border-collapse:collapse; font-family:'Montserrat',sans-serif; }
        th, td { padding:10px 12px; border:1px solid #e2e8f0; text-align:left; }
        th { background:#f1f5f9; font-weight:600; color:#4a5568; }
        tr:hover { background:#f8fafc; }
        .btn-delete { background:#ef4444; color:#fff; border:none; padding:6px 14px;
            border-radius:6px; cursor:pointer; font-size:.85rem; }
        .btn-delete:hover { background:#dc2626; }
        .success { background:#dcfce7; color:#166534; padding:10px 14px; border-radius:8px; margin-bottom:14px; }
        .search-bar { margin-bottom:14px; }
        .search-bar input { padding:8px 12px; border:1px solid #cbd5e0; border-radius:8px; width:280px; font-size:.95rem; }
    </style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="logo"><img src="../professeur/enjah.png" alt="logo" style="height:28px;"><span class="brand-name">Admin</span></div>
        <nav><ul>
            <li><a href="index.php">Tableau de bord</a></li>
            <li><a href="students.php">Étudiants</a></li>
            <li class="active"><a href="professors.php">Professeurs</a></li>
            <li><a href="payments.php">Paiements</a></li>
            <li><a href="chat.php">Support Chat</a></li>
            <li><a href="logout.php" style="color:#ef4444;">Déconnexion</a></li>
        </ul></nav>
    </aside>
    <main class="main-content">
        <header class="header"><h1>Liste des professeurs</h1></header>
        <?php if (!empty($success)): ?><div class="success"><?=htmlspecialchars($success)?></div><?php endif; ?>
        <div class="search-bar"><input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Rechercher..."></div>
        <table id="profsTable">
            <thead>
                <tr><th>CIN</th><th>Nom</th><th>Prénom</th><th>Photo</th><th>Type</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?=htmlspecialchars($row['CIN'])?></td>
                    <td><?=htmlspecialchars($row['nom'])?></td>
                    <td><?=htmlspecialchars($row['prenom'])?></td>
                    <td><?=$row['name'] ? htmlspecialchars($row['name']) : '—'?></td>
                    <td><?=$row['type'] ? htmlspecialchars($row['type']) : '—'?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Supprimer ce professeur ?');" style="display:inline;">
                            <input type="hidden" name="delete_cin" value="<?=(int)$row['CIN']?>">
                            <button type="submit" class="btn-delete">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;padding:16px;">Aucun professeur trouvé.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </main>
</div>
<script>
function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#profsTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
</body>
</html>
