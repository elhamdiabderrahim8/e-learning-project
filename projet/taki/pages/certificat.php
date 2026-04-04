<?php
session_start();
require_once('../backend/config/database.php');
$pdo = db();

// Sécurité : Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['CIN'])) {
    header("Location: login.php");
    exit();
}

$cin_etudiant = $_SESSION['CIN'];

// Requête SQL optimisée
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
$certificats = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Certifications | Énjah</title>
    <link rel="stylesheet" href="style_global.css"> 
    <link rel="stylesheet" href="style_certificats.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
</head>
<body>

<div class="dashboard-container">
    <?php $active = 'certificats'; require __DIR__ . '/partials/sidebar.php'; ?>

    <main class="main-content">
        <header class="page-header">
            <h1>Mes Certifications Académiques</h1>
            <p>Retrouvez ici tous vos diplômes obtenus sur la plateforme <strong>Énjah</strong>.</p>
        </header>

        <?php if (count($certificats) > 0): ?>
            <div class="certificats-grid">
                <?php foreach ($certificats as $cert): ?>
                    <div class="certificat-card">
                        <div class="card-icon">🎓</div>
                        <div class="card-body">
                            <h2 class="course-name"><?php echo htmlspecialchars($cert['nom_cours']); ?></h2>
                            <p class="prof-info">Certifié par : <strong>Prof. <?php echo htmlspecialchars($cert['prenom_prof'] . " " . $cert['nom_prof']); ?></strong></p>
                            
                            <div class="card-footer">
                                <div class="date-obtention">
                                    <span>Obtenu le :</span>
                                    <strong><?php echo date('d/m/Y', strtotime($cert['date_obtention'])); ?></strong>
                                </div>
                                <div class="verify-code">
                                    Code : <code><?php echo $cert['code_verification']; ?></code>
                                </div>
                            </div>

                            <a href="../../professeur/generer_certificat.php?code=<?php echo $cert['code_verification']; ?>" target="_blank" class="btn-download">
                                Visualiser & Imprimer
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>Vous n'avez pas encore de certificats validés. Terminez vos cours à 100% pour les obtenir !</p>
            </div>
        <?php endif; ?>
    </main>
</div>

</body>
</html>
