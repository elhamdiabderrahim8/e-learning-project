<?php

declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();
$cin_prof = user_id();

// Table is created lazily by the student free-enroll action.
$existsStmt = $pdo->query("SHOW TABLES LIKE 'inscription_demandes'");
$tableExists = (bool) $existsStmt->fetchColumn();

$rows = [];
if ($tableExists) {
    $stmt = $pdo->prepare(
        "SELECT id, id_etudiant, id_cours, student_nom, student_prenom, student_email, course_name, message, created_at, updated_at
         FROM inscription_demandes
         WHERE id_professeur = :cin_prof
         ORDER BY updated_at DESC"
    );
    $stmt->execute(['cin_prof' => $cin_prof]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demandes d'inscription - Enjah</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="dashboard-container">
        <?php $active = 'demandes'; require __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <h1>Demandes d'inscription</h1>
                <p>Demandes envoyées par les étudiants (cours gratuits).</p>
            </header>

            <section class="card" style="padding: 18px;">
                <?php if (!$tableExists): ?>
                    <p style="margin:0; color:#64748b; font-weight:700;">Aucune demande pour le moment.</p>
                <?php elseif (count($rows) === 0): ?>
                    <p style="margin:0; color:#64748b; font-weight:700;">Aucune demande pour le moment.</p>
                <?php else: ?>
                    <div style="overflow:auto;">
                        <table style="width:100%; border-collapse: collapse; min-width: 920px;">
                            <thead>
                                <tr style="text-align:left; color:#334155;">
                                    <th style="padding: 10px 12px; border-bottom: 1px solid #dbe4ef;">Étudiant</th>
                                    <th style="padding: 10px 12px; border-bottom: 1px solid #dbe4ef;">Email</th>
                                    <th style="padding: 10px 12px; border-bottom: 1px solid #dbe4ef;">CIN</th>
                                    <th style="padding: 10px 12px; border-bottom: 1px solid #dbe4ef;">Cours</th>
                                    <th style="padding: 10px 12px; border-bottom: 1px solid #dbe4ef;">Message</th>
                                    <th style="padding: 10px 12px; border-bottom: 1px solid #dbe4ef;">Dernière maj</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f7; font-weight:800;">
                                            <?php echo htmlspecialchars((string) $row['student_prenom'] . ' ' . (string) $row['student_nom'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f7;">
                                            <?php echo htmlspecialchars((string) $row['student_email'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f7;">
                                            <?php echo htmlspecialchars((string) $row['id_etudiant'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f7;">
                                            <?php echo htmlspecialchars((string) $row['course_name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f7; max-width: 380px;">
                                            <?php echo htmlspecialchars((string) ($row['message'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f7;">
                                            <?php echo htmlspecialchars((string) $row['updated_at'], ENT_QUOTES, 'UTF-8'); ?>
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

