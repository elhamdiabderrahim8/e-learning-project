<?php
require_once __DIR__ . '/../../../professeur/config/connexion.php';

$sql = "SELECT c.*, p.nom, p.prenom
        FROM cours c
        INNER JOIN professeur p ON c.id_professeur = p.CIN
        WHERE LOWER(c.categorie) = 'free'
        ORDER BY c.id DESC";
$result = $conn->query($sql);
$freeCourses = [];

if ($result instanceof mysqli_result) {
    while ($row = $result->fetch_assoc()) {
        $freeCourses[] = $row;
    }
}

$totalCourses = count($freeCourses);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mode invite - Enjah</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_invite.css">
</head>
<body class="guest-page">
    <div class="dashboard-container guest-shell">
        <header class="topbar guest-topbar" aria-label="Navigation invite">
            <div class="container topbar-inner guest-topbar-inner">
                <a class="brand" href="../index.php" aria-label="Retour a l'accueil etudiant">
                    <img src="../media/logo.jpg" alt="Logo Enjah">
                    <span>Invite</span>
                </a>

                <nav class="nav-links guest-nav" aria-label="Actions invite">
                    <a href="login.php">Connexion</a>
                    <a href="registre.php">Inscription</a>
                    <a href="../index.php">Accueil</a>
                </nav>
            </div>
        </header>

        <main class="main-content guest-main">
            <section class="header guest-hero">
                <div class="guest-hero-copy">
                    <span class="header-kicker">Decouverte libre</span>
                    <h1>Explorez les cours gratuits en mode invite</h1>
                    <p>Cette page vous permet de parcourir les cours free disponibles sur Enjah. Vous pouvez consulter les lecons gratuites, puis creer un compte plus tard pour suivre votre progression dans un espace complet.</p>

                    <div class="guest-actions-row">
                        <a href="registre.php" class="btn-primary guest-action-primary">S'inscrire gratuitement</a>
                        <a href="login.php" class="guest-action-secondary">Se connecter</a>
                    </div>
                </div>

                <aside class="guest-summary-card">
                    <div class="guest-summary-item">
                        <strong><?php echo $totalCourses; ?></strong>
                        <span>cours gratuits</span>
                    </div>
                    <div class="guest-summary-item">
                        <strong>Free</strong>
                        <span>contenu visible sans compte</span>
                    </div>
                    <div class="guest-summary-item">
                        <strong>Enjah</strong>
                        <span>apprentissage clair et moderne</span>
                    </div>
                </aside>
            </section>

            <section class="guest-info-grid">
                <article class="guest-info-card">
                    <h2>Ce que vous pouvez faire</h2>
                    <p>Parcourir les cours gratuits, identifier les enseignants et ouvrir les contenus accessibles en mode invite.</p>
                </article>
                <article class="guest-info-card">
                    <h2>Pourquoi creer un compte</h2>
                    <p>Suivre votre progression, retrouver vos cours, gerer vos taches et acceder a vos certificats depuis votre tableau de bord.</p>
                </article>
                <article class="guest-info-card">
                    <h2>Quand vous etes pret</h2>
                    <p>Inscrivez-vous gratuitement ou connectez-vous pour passer de la simple decouverte a un vrai parcours d'apprentissage.</p>
                </article>
            </section>

            <section class="header guest-catalog-header">
                <div>
                    <span class="header-kicker">Catalogue gratuit</span>
                    <h2>Cours disponibles maintenant</h2>
                    <p>Chaque carte presente un cours free consultable depuis ce mode invite.</p>
                </div>
            </section>

            <section class="courses-grid guest-courses-grid" id="courses-grid">
                <?php if ($totalCourses > 0): ?>
                    <?php foreach ($freeCourses as $row): ?>
                        <?php
                        $imageSrc = 'data:' . $row['image_type'] . ';base64,' . base64_encode($row['image_data']);
                        $teacher = trim((string) ($row['nom'] . ' ' . $row['prenom']));
                        ?>
                        <article class="course-card guest-course-card" id="cours-<?php echo (int) $row['id']; ?>">
                            <div class="course-image" style="background-image: url('<?php echo $imageSrc; ?>'); background-size: cover; background-position: center;">
                                <span class="badge badge-free">Free</span>
                            </div>

                            <div class="course-body guest-course-body">
                                <div class="guest-course-head">
                                    <h3><?php echo htmlspecialchars((string) $row['nom_cours'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <span class="guest-course-tag">Mode invite</span>
                                </div>

                                <p class="guest-course-teacher">Par <strong><?php echo htmlspecialchars($teacher, ENT_QUOTES, 'UTF-8'); ?></strong></p>
                                <div class="price-tag">Gratuit</div>
                                <p class="guest-course-text">Decouvrez le contenu du cours et accedez aux lecons gratuites proposees sur la plateforme.</p>

                                <div class="guest-course-actions">
                                    <a href="lesson(a)_inv.php?id=<?php echo (int) $row['id']; ?>" class="btn-primary">Consulter les lecons</a>
                                    <a href="registre.php" class="guest-inline-link">Creer un compte pour suivre votre progression</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="guest-empty-state">
                        <h3>Aucun cours gratuit pour le moment</h3>
                        <p>Le catalogue gratuit est vide pour l'instant. Vous pouvez revenir plus tard ou creer un compte pour entrer directement sur la plateforme.</p>
                        <a href="registre.php" class="btn-primary guest-empty-cta">Creer un compte</a>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
<?php
$conn->close();
?>
