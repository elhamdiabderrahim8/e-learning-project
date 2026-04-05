<?php

declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();
$cin_prof = (int) (user_id() ?? 0);

$stmt = $pdo->prepare(
    "SELECT id, nom_cours, prix, categorie, image_type, image_data
     FROM cours
     WHERE id_professeur = :cin
     ORDER BY id DESC"
);
$stmt->execute(['cin' => $cin_prof]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Cours - Enjah</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="dashboard-container">
        <?php $active = 'cours'; require __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <h1>Mes Cours</h1>
                <p>Gérez vos cours, ajoutez des leçons, et suivez les inscriptions.</p>
            </header>

            <section class="courses-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
                <?php if (count($courses) === 0): ?>
                    <div class="card" style="padding:18px; grid-column: 1 / -1;">
                        <p style="margin:0; color:#64748b; font-weight:700;">Aucun cours publié pour le moment.</p>
                        <p style="margin:10px 0 0; color:#64748b;">
                            Ajoutez un cours depuis <a href="../offres.php">Vos cours</a>.
                        </p>
                    </div>
                <?php else: ?>
                    <?php foreach ($courses as $course): ?>
                        <?php
                            $imageSrc = null;
                            if (!empty($course['image_data']) && !empty($course['image_type'])) {
                                $imageSrc = 'data:' . $course['image_type'] . ';base64,' . base64_encode($course['image_data']);
                            }
                            $isPremium = ((string) ($course['categorie'] ?? '')) === 'Premium';
                        ?>
                        <article class="course-card" style="overflow:hidden;">
                            <div style="height:160px; position:relative; background: linear-gradient(135deg, #e0f2fe, #e0e7ff); <?php echo $imageSrc ? "background-image:url('{$imageSrc}'); background-size:cover; background-position:center;" : ''; ?>">
                                <span class="badge <?php echo $isPremium ? 'badge-premium' : 'badge-free'; ?>" style="position:absolute; top:10px; right:10px;">
                                    <?php echo $isPremium ? 'Premium' : 'Free'; ?>
                                </span>
                            </div>

                            <div style="padding: 14px;">
                                <h3 style="margin:0 0 6px;"><?php echo htmlspecialchars((string) $course['nom_cours'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                <div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
                                    <div style="font-weight:900; color:#16a34a;">
                                        <?php echo $isPremium ? (number_format((float) $course['prix'], 2) . ' DT') : 'Gratuit'; ?>
                                    </div>
                                    <div style="color:#64748b; font-weight:800; font-size:0.9rem;">
                                        #<?php echo (int) $course['id']; ?>
                                    </div>
                                </div>

                                <a href="../ouvrir_session_cours.php?id=<?php echo (int) $course['id']; ?>"
                                   class="btn-primary"
                                   style="display:block; text-align:center; text-decoration:none; margin-top:12px;">
                                    Ajouter / gérer les leçons
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>

