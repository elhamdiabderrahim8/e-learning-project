<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../professeur/config/connexion.php';

$conn->query("ALTER TABLE inscription ADD COLUMN IF NOT EXISTS paiement_valide TINYINT(1) NOT NULL DEFAULT 0");

$sql = "SELECT i.id_inscription, i.id_etudiant, i.id_cours, i.date_achat, i.methode_paiement, i.paiement_valide,
               e.nom AS nom_etudiant, e.prenom AS prenom_etudiant,
               c.nom_cours, p.nom AS nom_prof, p.prenom AS prenom_prof
        FROM inscription i
        LEFT JOIN etudiant e ON e.CIN=i.id_etudiant
        LEFT JOIN cours c ON c.id=i.id_cours
        LEFT JOIN professeur p ON p.CIN=c.id_professeur
        ORDER BY i.date_achat DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiements - Admin</title>
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
            <li class="active"><a href="payments.php">Paiements</a></li>
            <li><a href="chat.php">Support Chat</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul></nav>
    </aside>
    <main class="main-content">
        <header class="header"><h1>Paiements</h1></header>
        <table>
            <thead>
                <tr><th>ID</th><th>Étudiant</th><th>Cours</th><th>Professeur</th><th>Date</th><th>Méthode</th><th>Statut</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?=htmlspecialchars($row['id_inscription'])?></td>
                    <td><?=htmlspecialchars($row['nom_etudiant'].' '.$row['prenom_etudiant'])?> (<?=htmlspecialchars($row['id_etudiant'])?>)</td>
                    <td><?=htmlspecialchars($row['nom_cours'])?></td>
                    <td><?=htmlspecialchars($row['nom_prof'].' '.$row['prenom_prof'])?></td>
                    <td><?=htmlspecialchars($row['date_achat'])?></td>
                    <td><?=htmlspecialchars($row['methode_paiement'])?></td>
                    <td style="font-weight:700;color:<?=$row['paiement_valide']==1?'#16a34a':'#b45309'?>;">
                        <?=$row['paiement_valide']==1?'Validé':'En attente'?>
                    </td>
                    <td>
                        <?php if ($row['paiement_valide'] != 1): ?>
                        <form method="post" action="actions/approve_payment.php" style="margin:0;">
                            <input type="hidden" name="id_inscription" value="<?=htmlspecialchars($row['id_inscription'])?>">
                            <button type="submit" style="padding:6px 12px;background:#4d68e1;color:#fff;border:0;border-radius:6px;cursor:pointer;">Approuver</button>
                        </form>
                        <?php else: ?>
                            <span style="color:#38a169;font-weight:600;">✓ Validé</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" style="text-align:center;padding:16px;">Aucun paiement trouvé.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
