<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../professeur/config/connexion.php';

$conn->query("ALTER TABLE inscription ADD COLUMN IF NOT EXISTS paiement_valide TINYINT(1) NOT NULL DEFAULT 0");
$conn->query("ALTER TABLE inscription ADD COLUMN IF NOT EXISTS card_holder VARCHAR(150) NULL");
$conn->query("ALTER TABLE inscription ADD COLUMN IF NOT EXISTS card_last4 CHAR(4) NULL");
$conn->query("ALTER TABLE inscription ADD COLUMN IF NOT EXISTS payment_status_note VARCHAR(255) NULL");

$sql = "SELECT i.id_inscription, i.id_etudiant, i.id_cours, i.date_achat, i.methode_paiement, i.paiement_valide,
           i.card_holder, i.card_last4, i.payment_status_note,
               e.nom AS nom_etudiant, e.prenom AS prenom_etudiant,
               c.nom_cours, p.nom AS nom_prof, p.prenom AS prenom_prof
        FROM inscription i
        LEFT JOIN etudiant e ON e.CIN=i.id_etudiant
        LEFT JOIN cours c ON c.id=i.id_cours
        LEFT JOIN professeur p ON p.CIN=c.id_professeur
    WHERE i.methode_paiement = 'Carte Bancaire'
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
                <tr><th>ID</th><th>Étudiant</th><th>Cours</th><th>Professeur</th><th>Date</th><th>Données carte</th><th>Statut</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $status = (int) ($row['paiement_valide'] ?? 0);
                    $statusText = $status === 1 ? 'Validé' : ($status === -1 ? 'Refusé' : 'En attente');
                    $statusColor = $status === 1 ? '#16a34a' : ($status === -1 ? '#dc2626' : '#b45309');
                ?>
                <tr>
                    <td><?=htmlspecialchars($row['id_inscription'])?></td>
                    <td><?=htmlspecialchars($row['nom_etudiant'].' '.$row['prenom_etudiant'])?> (<?=htmlspecialchars($row['id_etudiant'])?>)</td>
                    <td><?=htmlspecialchars($row['nom_cours'])?></td>
                    <td><?=htmlspecialchars($row['nom_prof'].' '.$row['prenom_prof'])?></td>
                    <td><?=htmlspecialchars($row['date_achat'])?></td>
                    <td>
                        <?=htmlspecialchars((string) ($row['card_holder'] ?? 'N/A'))?><br>
                        <small>**** **** **** <?=htmlspecialchars((string) ($row['card_last4'] ?? ''))?></small>
                    </td>
                    <td style="font-weight:700;color:<?=$statusColor?>;">
                        <?=$statusText?>
                        <?php if (!empty($row['payment_status_note'])): ?><br><small><?=htmlspecialchars($row['payment_status_note'])?></small><?php endif; ?>
                    </td>
                    <td>
                        <?php if ($status === 0): ?>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <form method="post" action="actions/approve_payment.php" style="margin:0;">
                                <input type="hidden" name="id_inscription" value="<?=htmlspecialchars($row['id_inscription'])?>">
                                <button type="submit" style="padding:6px 12px;background:#4d68e1;color:#fff;border:0;border-radius:6px;cursor:pointer;">Approuver</button>
                            </form>
                            <form method="post" action="actions/reject_payment.php" style="margin:0;" onsubmit="return confirm('Refuser ce paiement ?');">
                                <input type="hidden" name="id_inscription" value="<?=htmlspecialchars($row['id_inscription'])?>">
                                <button type="submit" style="padding:6px 12px;background:#ef4444;color:#fff;border:0;border-radius:6px;cursor:pointer;">Refuser</button>
                            </form>
                        </div>
                        <?php else: ?>
                            <span style="color:#38a169;font-weight:600;">✓ Traité</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" style="text-align:center;padding:16px;">Aucune demande de paiement premium.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
