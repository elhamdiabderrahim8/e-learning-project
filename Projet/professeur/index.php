<?php
declare(strict_types=1);

session_start();

$requestedLang = (string) ($_GET['lang'] ?? '');
if ($requestedLang === 'en' || $requestedLang === 'fr') {
    $_SESSION['preferred_language'] = $requestedLang;
    header('Location: index.php');
    exit;
}

$pageLang = (string) ($_SESSION['preferred_language'] ?? 'fr');
if ($pageLang !== 'fr' && $pageLang !== 'en') {
    $pageLang = 'fr';
}

$isEnglish = $pageLang === 'en';

$t = static function (string $fr, string $en) use ($isEnglish): string {
    return $isEnglish ? $en : $fr;
};
?>
<!DOCTYPE html>
<html lang="<?php echo $pageLang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t('Enjah Professeur - Plateforme Enseignant', 'Enjah Teacher - Teaching Platform'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($t('Enjah Professeur: creez vos cours, structurez vos lecons, et pilotez vos resultats avec une experience moderne et claire.', 'Enjah Teacher: create your courses, structure your lessons, and manage your results with a clear modern experience.'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="landing.css?v=20260405-3">
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <a class="brand" href="index.php" aria-label="<?php echo $t('Accueil professeur', 'Teacher home'); ?>">
                <img src="enjah.png" alt="Logo Enjah">
                <span><?php echo $t('Professeur', 'Teacher'); ?></span>
            </a>

            <nav class="nav-links" aria-label="<?php echo $t('Navigation professeur', 'Teacher navigation'); ?>">
                <a href="#produit"><?php echo $t('Produit', 'Product'); ?></a>
                <a href="#methode"><?php echo $t('Methode', 'Method'); ?></a>
                <a href="#avis"><?php echo $t('Avis', 'Reviews'); ?></a>
                <a href="#impact"><?php echo $t('Impact', 'Impact'); ?></a>
            </nav>

            <div class="top-actions">
                <form class="lang-switch" action="index.php" method="get" aria-label="<?php echo $t('Langue', 'Language'); ?>">
                    <label class="sr-only" for="landing-language"><?php echo $t('Langue', 'Language'); ?></label>
                    <select id="landing-language" name="lang" onchange="this.form.submit()">
                        <option value="fr" <?php echo $pageLang === 'fr' ? 'selected' : ''; ?>><?php echo $isEnglish ? 'French' : 'Français'; ?></option>
                        <option value="en" <?php echo $pageLang === 'en' ? 'selected' : ''; ?>><?php echo $isEnglish ? 'English' : 'Anglais'; ?></option>
                    </select>
                </form>
                <a href="login.html" class="btn btn-ghost"><?php echo $t('Connexion', 'Login'); ?></a>
                <a href="registre.php" class="btn btn-primary"><?php echo $t('Creer un compte', 'Create an account'); ?></a>
                <a href="../kmr/student/index.php" class="btn btn-soft"><?php echo $t('Espace Etudiant', 'Student Area'); ?></a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero" id="top">
            <div class="container hero-grid">
                <div class="hero-left">
                    <div class="hero-badges" aria-label="<?php echo $t('Points forts professeur', 'Teacher highlights'); ?>">
                        <a class="pill pill-brand" href="#produit"><?php echo $t('Creation de cours', 'Course creation'); ?></a>
                        <a class="pill" href="#methode"><?php echo $t('Lecons structurees', 'Structured lessons'); ?></a>
                        <a class="pill" href="#impact"><?php echo $t("Pilotage d'impact", 'Impact management'); ?></a>
                    </div>

                    <p class="hero-kicker"><?php echo $t('Plateforme Enseignant', 'Teaching Platform'); ?></p>
                    <h1><?php echo $t('Lancez des cours professionnels et transformez votre expertise en impact durable.', 'Launch professional courses and turn your expertise into lasting impact.'); ?></h1>
                    <p class="hero-subtitle">
                        <?php echo $t('Enjah Professeur centralise vos contenus, vos lecons, et votre progression pedagogique dans un espace visuel, fluide et oriente resultats.', 'Enjah Teacher brings your content, lessons, and teaching progress together in a visual, fluid, results-oriented space.'); ?>
                    </p>
                    <div class="hero-actions">
                        <a href="registre.php" class="btn btn-primary"><?php echo $t('Creer mon espace enseignant', 'Create my teacher space'); ?></a>
                        <a href="login.php" class="btn btn-ghost"><?php echo $t("J'ai deja un compte", 'I already have an account'); ?></a>
                    </div>

                    <div class="trustbar" aria-label="<?php echo $t('Resume plateforme', 'Platform summary'); ?>">
                        <div class="trust-item">
                            <strong><?php echo $t('Edition', 'Publishing'); ?></strong>
                            <span><?php echo $t('cours premium et gratuits', 'premium and free courses'); ?></span>
                        </div>
                        <div class="trust-item">
                            <strong><?php echo $t('Lecons', 'Lessons'); ?></strong>
                            <span><?php echo $t('organisation claire par etapes', 'clear step-by-step organization'); ?></span>
                        </div>
                        <div class="trust-item">
                            <strong><?php echo $t('Pilotage', 'Management'); ?></strong>
                            <span><?php echo $t('vision globale de votre activite', 'global view of your activity'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="hero-right" aria-label="<?php echo $t('Apercu professeur', 'Teacher preview'); ?>">
                    <figure class="hero-shot">
                        <img src="../kmr/media/photo.png" alt="<?php echo $t('Apercu de la plateforme Enjah Professeur', 'Enjah Teacher platform preview'); ?>">
                    </figure>
                </div>
            </div>
        </section>

        <section class="section section-alt" id="produit">
            <div class="container">
                <div class="section-head">
                    <div>
                        <h2><?php echo $t("Tout ce qu'il faut pour enseigner avec precision", 'Everything you need to teach with clarity'); ?></h2>
                        <p class="lead"><?php echo $t('Une interface claire pour publier, structurer et faire evoluer votre catalogue de cours.', 'A clear interface to publish, structure, and grow your course catalog.'); ?></p>
                    </div>
                    <a href="login.php" class="btn btn-soft"><?php echo $t('Acceder a mon espace', 'Access my space'); ?></a>
                </div>

                <div class="feature-grid">
                    <article class="feature feature-accent">
                        <div class="feature-top">
                            <span class="feature-icon" aria-hidden="true">CO</span>
                            <h3><?php echo $t('Creation de cours', 'Course creation'); ?></h3>
                        </div>
                        <p><?php echo $t('Nom, categorie, prix et image: publiez une offre lisible et professionnelle en quelques minutes.', 'Name, category, price, and image: publish a clear professional offer in a few minutes.'); ?></p>
                    </article>

                    <article class="feature">
                        <div class="feature-top">
                            <span class="feature-icon" aria-hidden="true">LE</span>
                            <h3><?php echo $t('Gestion des lecons', 'Lesson management'); ?></h3>
                        </div>
                        <p><?php echo $t('Ajoutez vos lecons par sequence pour guider les etudiants sans confusion.', 'Add lessons by sequence to guide learners without confusion.'); ?></p>
                    </article>

                    <article class="feature">
                        <div class="feature-top">
                            <span class="feature-icon" aria-hidden="true">SU</span>
                            <h3><?php echo $t('Suivi pedagogique', 'Teaching follow-up'); ?></h3>
                        </div>
                        <p><?php echo $t('Gardez une vue continue sur vos cours et ajustez votre contenu en fonction des besoins.', 'Keep a continuous view of your courses and adjust your content based on needs.'); ?></p>
                    </article>

                    <article class="feature">
                        <div class="feature-top">
                            <span class="feature-icon" aria-hidden="true">ID</span>
                            <h3><?php echo $t('Identite professionnelle', 'Professional identity'); ?></h3>
                        </div>
                        <p><?php echo $t('Mettez a jour vos informations pour renforcer la confiance de vos apprenants.', 'Update your information to strengthen learner trust.'); ?></p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="methode">
            <div class="container">
                <h2><?php echo $t('Methode Enjah Professeur', 'Enjah Teacher method'); ?></h2>
                <p class="lead"><?php echo $t('Un cycle simple pour produire des cours solides, visibles et utiles.', 'A simple cycle to produce solid, visible, useful courses.'); ?></p>

                <div class="timeline">
                    <div class="timeline-step">
                        <div class="timeline-badge">1</div>
                        <div class="timeline-body">
                            <strong><?php echo $t("Definir l'offre de cours", 'Define the course offer'); ?></strong>
                            <p><?php echo $t("Cadrez l'objectif, le format et la promesse pedagogique.", 'Clarify the goal, format, and teaching promise.'); ?></p>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-badge">2</div>
                        <div class="timeline-body">
                            <strong><?php echo $t('Structurer les lecons', 'Structure the lessons'); ?></strong>
                            <p><?php echo $t('Organisez la progression en modules simples et actionnables.', 'Organize progress into simple, actionable modules.'); ?></p>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-badge">3</div>
                        <div class="timeline-body">
                            <strong><?php echo $t('Publier et iterer', 'Publish and iterate'); ?></strong>
                            <p><?php echo $t('Ameliorez en continu la qualite de votre parcours selon les retours.', 'Continuously improve the quality of your path based on feedback.'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="avis">
            <div class="container">
                <h2><?php echo $t('Retours de professeurs', 'Teacher feedback'); ?></h2>
                <p class="lead"><?php echo $t("Une plateforme concue pour gagner du temps et augmenter la qualite d'enseignement.", 'A platform designed to save time and improve teaching quality.'); ?></p>
                <div class="testimonials">
                    <article class="quote">
                        <p><?php echo $t('"J\'ai lance mon cours en peu de temps. L\'interface est propre et facile a prendre en main."', '"I launched my course quickly. The interface is clean and easy to use."'); ?></p>
                        <span><?php echo $t('Professeur en developpement web', 'Web development teacher'); ?></span>
                    </article>
                    <article class="quote">
                        <p><?php echo $t('"Le suivi de mes contenus est plus simple, je peux me concentrer sur la pedagogie."', '"Tracking my content is simpler, so I can focus on teaching."'); ?></p>
                        <span><?php echo $t('Formateur independant', 'Independent trainer'); ?></span>
                    </article>
                    <article class="quote">
                        <p><?php echo $t('"Mes etudiants trouvent les lecons plus claires et la progression est beaucoup plus fluide."', '"My students find the lessons clearer and the progression much smoother."'); ?></p>
                        <span><?php echo $t('Coach digital', 'Digital coach'); ?></span>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="impact">
            <div class="container cta">
                <div>
                    <h2><?php echo $t("Pret a lancer votre prochain parcours d'excellence ?", 'Ready to launch your next high-impact learning path?'); ?></h2>
                    <p><?php echo $t('Rejoignez Enjah Professeur et donnez une nouvelle dimension a votre impact pedagogique.', 'Join Enjah Teacher and give your educational impact a new dimension.'); ?></p>
                </div>
                <div class="hero-actions">
                    <a href="registre.php" class="btn btn-primary"><?php echo $t('Creer mon espace professeur', 'Create my teacher space'); ?></a>
                    <a href="login.php" class="btn btn-ghost"><?php echo $t('Se connecter', 'Log in'); ?></a>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            © 2026 Enjah Professeur · Tous droits reserves.
        </div>
    </footer>
</body>
</html>
