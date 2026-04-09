<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();
$cin_etudiant = (int) $_SESSION['CIN'];
$isEnglish = current_language() === 'en';

$sql = "SELECT 
            cert.code_verification, 
            cert.date_obtention, 
            c.nom_cours, 
            p.nom AS nom_prof, 
            p.prenom AS prenom_prof
        FROM certificaton cert
        JOIN cours c ON cert.id_cours = c.id
        JOIN professeur p ON cert.id_professeur = p.CIN
        WHERE cert.id_etudiant = :cin
        ORDER BY cert.date_obtention DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['cin' => $cin_etudiant]);
$certificats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEnglish ? 'My Certificates - Enjah' : 'Mes Certifications - Enjah'; ?></title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_certificats.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <?php $active = 'certificat'; require __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="page-header">
                <h1><?php echo $isEnglish ? 'My Academic Certificates' : 'Mes Certifications Académiques'; ?></h1>
                <p><?php echo $isEnglish ? 'Find here all certificates validated on the platform.' : 'Retrouvez ici tous vos diplômes obtenus sur la plateforme Enjah.'; ?></p>
            </header>

            <?php if (count($certificats) > 0): ?>
                <div class="certificats-grid">
                    <?php foreach ($certificats as $cert): ?>
                        <article class="certificat-card">
                            <div class="card-icon" aria-hidden="true">&#127891;</div>
                            <div class="card-body">
                                <h2 class="course-name"><?php echo htmlspecialchars((string) $cert['nom_cours'], ENT_QUOTES, 'UTF-8'); ?></h2>
                                <p class="prof-info"><?php echo $isEnglish ? 'Certified by:' : 'Certifié par :'; ?> <strong>Prof. <?php echo htmlspecialchars($cert['prenom_prof'] . ' ' . $cert['nom_prof'], ENT_QUOTES, 'UTF-8'); ?></strong></p>

                                <div class="card-footer">
                                    <div class="date-obtention">
                                        <span><?php echo $isEnglish ? 'Obtained on:' : 'Obtenu le :'; ?></span>
                                        <strong><?php echo date('d/m/Y', strtotime((string) $cert['date_obtention'])); ?></strong>
                                    </div>
                                    <div class="verify-code">
                                        <span><?php echo $isEnglish ? 'Code:' : 'Code :'; ?></span>
                                        <code><?php echo htmlspecialchars((string) $cert['code_verification'], ENT_QUOTES, 'UTF-8'); ?></code>
                                    </div>
                                </div>

                                <a href="../../../professeur/generer_certificat.php?code=<?php echo rawurlencode((string) $cert['code_verification']); ?>" target="_blank" rel="noopener noreferrer" class="btn-download">
                                    <?php echo $isEnglish ? 'View and Print' : 'Visualiser & Imprimer'; ?>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p><?php echo $isEnglish ? 'No validated certificates yet. Complete your courses to unlock them.' : 'Vous n\'avez pas encore de certificats validés. Terminez vos cours à 100% pour les obtenir !'; ?></p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
