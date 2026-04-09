<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();
$cin_etudiant = (int)$_SESSION['CIN'];

/**
 * REQUÊTE SQL SÉCURISÉE (PDO)
 * On récupère la progression et le statut du certificat depuis la table inscription
 */
$sql = "SELECT DISTINCT c.*, p.nom, p.prenom, i.statut_certificat
        FROM cours c
        INNER JOIN professeur p ON c.id_professeur = p.CIN
        INNER JOIN inscription i ON c.id = i.id_cours AND i.id_etudiant = :cin
        ORDER BY c.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['cin' => $cin_etudiant]);
$mes_cours = $stmt->fetchAll(PDO::FETCH_ASSOC); 

$isEnglish = current_language() === 'en';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Cours - Enjah</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        /* Styles spécifiques pour la barre de progression si non présents dans tes CSS */
        .progression-wrapper {
            margin: 15px 0;
        }
        .progression-bar-bg {
            width: 100%;
            background: #e2e8f0;
            height: 8px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #cbd5e1;
        }
        .progression-fill {
            height: 100%;
            background: linear-gradient(90deg, #4d68e1, #20c997);
            transition: width 0.8s ease-in-out;
        }
        .status-badge {
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
            margin-top: 5px;
            padding: 2px 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php $active = 'cours'; require __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <header style="padding: 20px; border-bottom: 1px solid #eee;">
                <h1><?php echo $isEnglish ? 'My Academic Courses' : 'Mes Cours Académiques'; ?></h1>
                <p><?php echo $isEnglish ? 'Welcome, you are enrolled in' : 'Bienvenue, vous avez'; ?> <strong><?php echo count($mes_cours); ?></strong> <?php echo $isEnglish ? 'courses.' : 'cours inscrits.'; ?></p>
            </header>

            <div class="courses-grid" id="courses-grid">
                <?php
                if (count($mes_cours) > 0) {
                    foreach ($mes_cours as $row) {
                        $id_c       = $row['id'];
                        $titre      = htmlspecialchars($row['nom_cours']);
                        $prix       = number_format((float)$row['prix'], 2);
                        $cat        = $row['categorie'];
                        $prof       = htmlspecialchars($row['nom'] . " " . $row['prenom']);
                        $image_src  = 'data:' . $row['image_type'] . ';base64,' . base64_encode($row['image_data']);
                        
                        // Variables de progression
                       // --- CALCUL DYNAMIQUE DE LA PROGRESSION ---
// 1. Compter le total de leçons actuel pour ce cours
$stmt_t = $pdo->prepare("SELECT COUNT(*) FROM lecon WHERE id_cours = ?");
$stmt_t->execute([$id_c]);
$total_docs = (int)$stmt_t->fetchColumn();

// 2. Compter les leçons uniques terminées par l'étudiant
$stmt_f = $pdo->prepare("SELECT COUNT(DISTINCT id_lecon) FROM suivi_lecons WHERE id_etudiant = ? AND id_cours = ?");
$stmt_f->execute([$cin_etudiant, $id_c]);
$fait_docs = (int)$stmt_f->fetchColumn();

// 3. Calcul du pourcentage final
$prog = ($total_docs > 0) ? (int)round(($fait_docs / $total_docs) * 100) : 0;
                        $statut     = $row['statut_certificat'] ?? 'aucun';
                        $badge_class = ($cat === 'Premium') ? 'badge-premium' : 'badge-free';
                ?>
                        <div class="course-card" id="cours-<?php echo $id_c; ?>">
                            <div class="course-image" 
                                 style="background-image: url('<?php echo $image_src; ?>'); background-size: cover; background-position: center;">
                                <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars((string) $cat, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            
                            <div class="course-body">
                                <h3><?php echo $titre; ?></h3>
                                <p><?php echo $isEnglish ? 'By' : 'Par'; ?> <strong><?php echo $prof; ?></strong></p>
                                <div class="price-tag"><?php echo $prix; ?> DT</div>

                                <div class="progression-wrapper">
                                    <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                                        <span><?php echo $isEnglish ? 'Progress' : 'Progression'; ?></span>
                                        <strong><?php echo $prog; ?>%</strong>
                                    </div>
                                    <div class="progression-bar-bg">
                                        <div class="progression-fill" style="width: <?php echo $prog; ?>%;"></div>
                                    </div>

                                    <?php if($prog >= 100): ?>
                                        <div class="status-badge" style="background: #ecfdf5; color: #059669;">
                                            <?php echo ($statut === 'valide') ? ($isEnglish ? '✅ Certificate ready' : '✅ Certificat prêt') : ($isEnglish ? '⏳ Pending validation' : '⏳ En attente de validation'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <a href="lesson(a).php?id=<?php echo $id_c; ?>" class="btn-primary" style="display:block; text-align:center; text-decoration:none; margin-top:10px;">
                                    <?php echo $isEnglish ? 'View lessons' : 'Consulter leçons'; ?>
                                </a>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p style='grid-column: 1/-1; text-align: center;'>" . ($isEnglish ? 'No courses available.' : 'Aucun cours disponible.') . "</p>";
                }
                ?>
            </div>
        </main>
    </div>
</body>
</html>
