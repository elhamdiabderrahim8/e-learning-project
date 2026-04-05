<?php

declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();
$cin_prof = user_id();

$sql = "SELECT
            e.CIN AS cin_etudiant,
            e.nom AS nom_etudiant,
            e.prenom AS prenom_etudiant,
            e.email AS email_etudiant,
            c.id AS id_cours,
            c.nom_cours,
            i.date_achat,
            i.methode_paiement
        FROM inscription i
        INNER JOIN etudiant e ON e.CIN = i.id_etudiant
        INNER JOIN cours c ON c.id = i.id_cours
        WHERE c.id_professeur = :cin_prof
        ORDER BY i.date_achat DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['cin_prof' => $cin_prof]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Étudiants - Enjah</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="dashboard-container">
        <?php $active = 'etudiants'; require __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <h1>Mes Étudiants</h1>
                <p>Liste des inscriptions à vos cours (nom, prénom, email).</p>
            </header>

            <section class="card" style="padding: 18px;">
                <?php if (count($rows) === 0): ?>
                    <p style="margin:0; color:#64748b; font-weight:700;">Aucune inscription pour le moment.</p>
                <?php else: ?>
                    <div style="overflow:auto;">
                        <table style="width:100%; border-collapse: collapse; min-width: 840px;">
                            <thead>
                                <tr style="text-align:left; color:#334155;">
                                    <th style="padding: 10px 12px; border-bottom: 1px solid #dbe4ef;">Étudiant</th>
                                    <th style="padding: 10px 12px; border-bottom: 1px solid #dbe4ef;">Email</th>
                                    <th style="padding: 10px 12px; border-bottom: 1px solid #dbe4ef;">CIN</th>
                                    <th style="padding: 10px 12px; border-bottom: 1px solid #dbe4ef;">Cours</th>
                                    <th style="padding: 10px 12px; border-bottom: 1px solid #dbe4ef;">Date</th>
                                    <th style="padding: 10px 12px; border-bottom: 1px solid #dbe4ef;">Méthode</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f7; font-weight:800;">
                                            <?php echo htmlspecialchars((string) $row['prenom_etudiant'] . ' ' . (string) $row['nom_etudiant'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f7;">
                                            <?php echo htmlspecialchars((string) $row['email_etudiant'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f7;">
                                            <?php echo htmlspecialchars((string) $row['cin_etudiant'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f7;">
                                            <?php echo htmlspecialchars((string) $row['nom_cours'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f7;">
                                            <?php echo htmlspecialchars((string) $row['date_achat'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f7;">
                                            <?php echo htmlspecialchars((string) $row['methode_paiement'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>

