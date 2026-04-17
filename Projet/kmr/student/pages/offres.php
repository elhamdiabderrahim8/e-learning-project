<?php
declare(strict_types=1);

// 1. Chargement de l'environnement élève
require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();
$cin_etudiant = (int) ($_SESSION['CIN'] ?? 0);

// Student details for enrollment form (sent to professor).
$stmtStudent = $pdo->prepare('SELECT nom, prenom, email FROM etudiant WHERE CIN = :cin LIMIT 1');
$stmtStudent->execute(['cin' => $cin_etudiant]);
$student = $stmtStudent->fetch(PDO::FETCH_ASSOC) ?: ['nom' => '', 'prenom' => '', 'email' => ''];

// Offres = cours non encore inscrits (Free + Premium).
$sql = "SELECT c.id AS id_cours, c.nom_cours, c.prix, c.categorie,
               p.nom AS nom_prof, p.prenom AS prenom_prof
        FROM cours c
        INNER JOIN professeur p ON c.id_professeur = p.CIN
        LEFT JOIN inscription i
          ON i.id_cours = c.id AND i.id_etudiant = :cin
        WHERE i.id_etudiant IS NULL
        ORDER BY c.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['cin' => $cin_etudiant]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$isEnglish = current_language() === 'en';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Choisir une offre - Enjah</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="dashboard-container">
        <?php $active = 'offres'; require __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <h1><?php echo $isEnglish ? 'Special Offers' : 'Offres Spéciales'; ?></h1>
                <p><?php echo $isEnglish ? 'Choose a course. Courses already enrolled are hidden here.' : 'Choisissez un cours. Les cours déjà inscrits ne s\'affichent plus ici.'; ?></p>
            </header>

            <div class="courses-grid" id="courses-grid">
                <?php
                if (count($courses) > 0) {
                    foreach ($courses as $row) {
                        $courseId = (int) $row['id_cours'];
                        $image_src = '../backend/actions/course_image.php?id=' . $courseId;
                        $isPremium = ($row['categorie'] ?? '') === 'Premium';
                ?>
                        <div class="course-card">
                             <div class="course-image" style="position: relative;">
                                <img class="course-image-media" src="<?php echo htmlspecialchars($image_src, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($row['nom_cours'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" decoding="async">
                                <?php if ($isPremium): ?>
                                    <span class="badge badge-premium" style="position: absolute; top: 14px; right: 14px;">Premium</span>
                                <?php else: ?>
                                    <span class="badge badge-free" style="position: absolute; top: 14px; right: 14px;">Free</span>
                                <?php endif; ?>
                            </div>
                            <div class="course-body">
                                <h3><?php echo htmlspecialchars($row['nom_cours']); ?></h3>
                                <p><?php echo $isEnglish ? 'By' : 'Par'; ?> <?php echo htmlspecialchars($row['nom_prof'] . " " . $row['prenom_prof']); ?></p>
                                
                                <div class="price-tag">
                                    <?php echo $isPremium ? (number_format((float) $row['prix'], 2) . " DT") : ($isEnglish ? 'Free' : 'Gratuit'); ?>
                                </div>

                                <?php if ($isPremium): ?>
                                    <form action="../backend/actions/traitter_payement(a).php" method="POST">
                                        <input type="hidden" name="course_id" value="<?php echo $courseId; ?>">
                                        <button type="submit" class="btn-primary" style="width:100%;">
                                            Payer
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form action="../backend/actions/enroll_free_course.php" method="post">
                                        <input type="hidden" name="course_id" value="<?php echo (int) $row['id_cours']; ?>">
                                        <button type="submit" class="btn-primary" style="width:100%;">
                                            <?php echo $isEnglish ? 'Enroll for free' : 'S\'inscrire gratuitement'; ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p>" . ($isEnglish ? 'No courses available for now.' : 'Aucun cours disponible pour le moment.') . "</p>";
                }
                ?>
            </div>
        </main>
    </div>

    <div id="step-success" class="modal-overlay" style="display: none;">
        <div class="modal-content" style="text-align:center;">
            <div style="font-size: 4rem; color: #20c997;">&#10003;</div>
            <h2><?php echo $isEnglish ? 'Payment confirmed!' : 'Paiement validé !'; ?></h2>
            <p style="margin: 15px 0; color: #666;"><?php echo $isEnglish ? 'Your course has been added to your dashboard.' : 'Votre cours a été ajouté à votre espace personnel.'; ?></p>
            <a href="cours.php" style="display:block; text-decoration:none; background:#007bff; color:white; padding:12px; border-radius:5px; font-weight:bold;"><?php echo $isEnglish ? 'Go to my courses' : 'Accéder à mes cours'; ?></a>
            <a href="offres.php" style="display:block; margin-top:10px; color:#666; font-size:0.9em;"><?php echo $isEnglish ? 'Close' : 'Fermer'; ?></a>
        </div>
    </div>

</body>
</html>
