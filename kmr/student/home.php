<?php
declare(strict_types=1);

require_once __DIR__ . '/backend/includes/bootstrap.php';

$isEnglish = current_language() === 'en';
$pageLang = $isEnglish ? 'en' : 'fr';
$pageTitle = $isEnglish ? "Enjah - Learning Platform" : "Enjah - Plateforme d'apprentissage";
$pageDescription = $isEnglish
    ? 'Enjah: clear courses, visible progress, organized tasks, and responsive support. Learn better and move faster.'
    : 'Enjah : cours clairs, progression visible, tâches organisées, support réactif. Apprenez mieux et avancez plus vite.';
?>
<!DOCTYPE html>
<html lang="<?php echo $pageLang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="media/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="media/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="media/favicon_io/favicon-16x16.png">
    <link rel="shortcut icon" href="media/favicon_io/favicon.ico">
    <link rel="manifest" href="media/favicon_io/site.webmanifest">
    <link rel="stylesheet" href="landing.css?v=20260405-2">
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <a class="brand" href="index.php" aria-label="<?php echo $isEnglish ? 'Enjah home' : 'Accueil Enjah'; ?>">
                <img src="media/logo.jpg" alt="Logo Enjah">
                <span>Enjah</span>
            </a>

            <nav class="nav-links" aria-label="<?php echo $isEnglish ? 'Main navigation' : 'Navigation principale'; ?>">
                <a href="#produit"><?php echo $isEnglish ? 'Product' : 'Produit'; ?></a>
                <a href="#parcours"><?php echo $isEnglish ? 'Tracks' : 'Parcours'; ?></a>
                <a href="#tarifs"><?php echo $isEnglish ? 'Pricing' : 'Tarifs'; ?></a>
                <a href="#faq">FAQ</a>
            </nav>

            <div class="top-actions">
                <form class="lang-switch" action="index.php" method="get" aria-label="<?php echo $isEnglish ? 'Language setting' : 'Langue'; ?>">
                    <label class="sr-only" for="landing-language"><?php echo $isEnglish ? 'Language' : 'Langue'; ?></label>
                    <select id="landing-language" name="lang" onchange="this.form.submit()">
                        <option value="fr" <?php echo $pageLang === 'fr' ? 'selected' : ''; ?>><?php echo $isEnglish ? 'French' : 'Français'; ?></option>
                        <option value="en" <?php echo $pageLang === 'en' ? 'selected' : ''; ?>><?php echo $isEnglish ? 'English' : 'Anglais'; ?></option>
                    </select>
                </form>
                <a href="pages/login.php" class="btn btn-ghost"><?php echo $isEnglish ? 'Login' : 'Connexion'; ?></a>
                <a href="pages/registre.php" class="btn btn-primary"><?php echo $isEnglish ? 'Sign up' : 'Inscription'; ?></a>
                <a href="../../professeur/index.php" class="btn btn-soft"><?php echo $isEnglish ? 'Teacher area' : 'Espace Professeur'; ?></a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero" id="top">
            <div class="container hero-grid">
                <div class="hero-left">
                    <div class="hero-badges" aria-label="<?php echo $isEnglish ? 'Highlights' : 'Points forts'; ?>">
                        <a class="pill pill-brand" href="#produit"><?php echo $isEnglish ? 'E-learning platform' : 'Plateforme e-learning'; ?></a>
                        <a class="pill" href="#methode"><?php echo $isEnglish ? 'Progress & tasks' : 'Progression &amp; tâches'; ?></a>
                        <a class="pill" href="#faq"><?php echo $isEnglish ? 'Responsive support' : 'Support réactif'; ?></a>
                    </div>

                    <h1><?php echo $isEnglish ? 'Learn better, keep the pace,' : 'Apprends mieux, garde le rythme,'; ?> <span class="grad"><?php echo $isEnglish ? 'and truly progress.' : 'progresse pour de vrai'; ?></span></h1>
                    <p class="hero-subtitle">
                        <?php echo $isEnglish
                            ? 'Enjah brings your courses, progress, and tasks into one place. Less friction, more consistency, and clear guidance at every step.'
                            : 'Enjah réunit tes cours, ta progression et tes tâches dans un seul espace. Moins de friction, plus de régularité, et un suivi clair à chaque étape.'; ?>
                    </p>

                    <div class="hero-actions">
                        <a href="pages/registre.php" class="btn btn-primary"><?php echo $isEnglish ? 'Create my account' : 'Créer mon compte'; ?></a>
                        <a href="#produit" class="btn btn-ghost"><?php echo $isEnglish ? 'Discover the platform' : 'Découvrir la plateforme'; ?></a>
                    </div>

                    <div class="trustbar" aria-label="<?php echo $isEnglish ? 'What you get' : 'Ce que vous obtenez'; ?>">
                        <div class="trust-item">
                            <strong><?php echo $isEnglish ? 'Tracking' : 'Suivi'; ?></strong>
                            <span><?php echo $isEnglish ? 'course progress' : 'progression par cours'; ?></span>
                        </div>
                        <div class="trust-item">
                            <strong><?php echo $isEnglish ? 'Organization' : 'Organisation'; ?></strong>
                            <span><?php echo $isEnglish ? 'simple task board' : 'tableau de tâches simple'; ?></span>
                        </div>
                        <div class="trust-item">
                            <strong><?php echo $isEnglish ? 'Premium' : 'Premium'; ?></strong>
                            <span><?php echo $isEnglish ? 'offers and extended access' : 'offres et accès étendu'; ?></span>
                        </div>
                    </div>
                </div>

                <div class="hero-right" aria-label="<?php echo $isEnglish ? 'Platform preview' : 'Aperçu de la plateforme'; ?>">
                    <figure class="hero-shot">
                        <img src="../media/photo.png" alt="<?php echo $isEnglish ? 'Enjah platform preview' : 'Aperçu de la plateforme Enjah'; ?>">
                    </figure>
                </div>
            </div>
        </section>

        <section class="section section-alt" id="produit">
            <div class="container">
                <div class="section-head">
                    <div>
                        <h2><?php echo $isEnglish ? 'Everything you need to learn with confidence' : 'Tout ce qu’il faut pour apprendre sereinement'; ?></h2>
                        <p class="lead"><?php echo $isEnglish ? 'A clear design, simple steps, and useful daily tools.' : 'Un design clair, des étapes simples, et des outils utiles au quotidien.'; ?></p>
                    </div>
                    <a href="pages/login.php" class="btn btn-soft"><?php echo $isEnglish ? 'Access my space' : 'Accéder à mon espace'; ?></a>
                </div>

                <div class="feature-grid">
                    <article class="feature feature-accent">
                        <div class="feature-top">
                            <span class="feature-icon" aria-hidden="true">📚</span>
                            <h3><?php echo $isEnglish ? 'Clear courses' : 'Cours clairs'; ?></h3>
                        </div>
                    </article>

                    <article class="feature">
                        <div class="feature-top">
                            <span class="feature-icon" aria-hidden="true">📈</span>
                            <h3><?php echo $isEnglish ? 'Visible progress' : 'Progression visible'; ?></h3>
                        </div>
                    </article>

                    <article class="feature">
                        <div class="feature-top">
                            <span class="feature-icon" aria-hidden="true">✅</span>
                            <h3><?php echo $isEnglish ? 'Organized tasks' : 'Tâches organisées'; ?></h3>
                        </div>
                        <p><?php echo $isEnglish ? 'A simple board to plan, prioritize, and finish what matters each week.' : 'Un tableau simple pour planifier, prioriser et terminer ce qui compte chaque semaine.'; ?></p>
                    </article>

                    <article class="feature">
                        <div class="feature-top">
                            <span class="feature-icon" aria-hidden="true">💬</span>
                            <h3><?php echo $isEnglish ? 'Support & requests' : 'Support &amp; réclamation'; ?></h3>
                        </div>
                        <p><?php echo $isEnglish ? 'Need help? Send a request and follow your conversation without leaving the platform.' : 'Besoin d’aide ? Envoyez une demande et suivez votre échange, sans quitter la plateforme.'; ?></p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="parcours">
            <div class="container">
                <h2><?php echo $isEnglish ? 'Popular tracks' : 'Parcours populaires'; ?></h2>
                <p class="lead"><?php echo $isEnglish ? 'Tracks designed to help you progress without getting scattered.' : 'Des parcours pensés pour progresser sans vous disperser.'; ?></p>
                <div class="track-grid">
                    <article class="track">
                        <div class="track-head">
                            <h3><?php echo $isEnglish ? 'Web Development' : 'Développement Web'; ?></h3>
                            <span class="track-badge"><?php echo $isEnglish ? 'Projects' : 'Projets'; ?></span>
                        </div>
                        <p><?php echo $isEnglish ? 'HTML/CSS, PHP basics, guided practice, and mini-projects to learn by doing.' : 'HTML/CSS, bases PHP, pratiques guidées et mini-projets pour apprendre en faisant.'; ?></p>
                        <div class="track-meta">
                            <span class="chip"><?php echo $isEnglish ? 'Beginner → Intermediate' : 'Débutant → Intermédiaire'; ?></span>
                            <span class="chip"><?php echo $isEnglish ? 'Progress tracking' : 'Suivi progression'; ?></span>
                        </div>
                    </article>
                    <article class="track">
                        <div class="track-head">
                            <h3><?php echo $isEnglish ? 'Design & UI' : 'Design &amp; UI'; ?></h3>
                            <span class="track-badge soft"><?php echo $isEnglish ? 'Interface' : 'Interface'; ?></span>
                        </div>
                        <p><?php echo $isEnglish ? 'Visual hierarchy, components, consistency, and best practices for modern interfaces.' : 'Hiérarchie visuelle, composants, cohérence et bonnes pratiques pour des interfaces modernes.'; ?></p>
                        <div class="track-meta">
                            <span class="chip"><?php echo $isEnglish ? 'Design system' : 'Design system'; ?></span>
                            <span class="chip"><?php echo $isEnglish ? 'Exercises' : 'Exercices'; ?></span>
                        </div>
                    </article>
                    <article class="track">
                        <div class="track-head">
                            <h3><?php echo $isEnglish ? 'Methods & Organization' : 'Méthodes &amp; Organisation'; ?></h3>
                            <span class="track-badge ok"><?php echo $isEnglish ? 'Routine' : 'Routine'; ?></span>
                        </div>
                        <p><?php echo $isEnglish ? 'Simple goals, time management, and weekly tasks to stay on track.' : 'Objectifs simples, gestion du temps et tâches hebdomadaires pour garder le rythme.'; ?></p>
                        <div class="track-meta">
                            <span class="chip"><?php echo $isEnglish ? 'Productivity' : 'Productivité'; ?></span>
                            <span class="chip"><?php echo $isEnglish ? 'Habits' : 'Habitudes'; ?></span>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="methode">
            <div class="container">
                <h2><?php echo $isEnglish ? 'Enjah method' : 'Méthode Enjah'; ?></h2>
                <p class="lead"><?php echo $isEnglish ? 'A simple routine to follow, even with a busy schedule.' : 'Une routine simple à suivre, même avec un planning chargé.'; ?></p>

                <div class="timeline">
                    <div class="timeline-step">
                        <div class="timeline-badge">1</div>
                        <div class="timeline-body">
                            <strong><?php echo $isEnglish ? 'Choose a course' : 'Choisissez un cours'; ?></strong>
                            <p><?php echo $isEnglish ? 'Free or premium tracks based on your goals, schedule, and level.' : 'Parcours gratuits ou premium selon vos objectifs, vos horaires et votre niveau.'; ?></p>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-badge">2</div>
                        <div class="timeline-body">
                            <strong><?php echo $isEnglish ? 'Move forward in small steps' : 'Avancez en petites étapes'; ?></strong>
                            <p><?php echo $isEnglish ? 'Short lessons, progress tracking, and a clear view of what remains.' : 'Des leçons courtes, un suivi de progression, et un point clair sur ce qu’il reste à faire.'; ?></p>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-badge">3</div>
                        <div class="timeline-body">
                            <strong><?php echo $isEnglish ? 'Organize your tasks' : 'Organisez vos tâches'; ?></strong>
                            <p><?php echo $isEnglish ? 'Plan the week, prioritize, and finish without losing the thread.' : 'Planifiez la semaine, priorisez, et terminez sans perdre le fil.'; ?></p>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-badge">4</div>
                        <div class="timeline-body">
                            <strong><?php echo $isEnglish ? 'Get help quickly' : 'Recevez de l’aide rapidement'; ?></strong>
                            <p><?php echo $isEnglish ? 'Support and requests: you are never blocked alone.' : 'Support &amp; réclamation : vous n’êtes jamais bloqué seul.'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="avis">
            <div class="container">
                <h2><?php echo $isEnglish ? 'Learner feedback' : "Retours d'apprenants"; ?></h2>
                <p class="lead"><?php echo $isEnglish ? 'A few reviews to help you get a feel for it.' : 'Quelques avis pour se faire une idée.'; ?></p>
                <div class="testimonials">
                    <article class="quote">
                        <p><?php echo $isEnglish ? '“Simple navigation: I find everything quickly. The courses are well organized.”' : '“Navigation simple : je retrouve tout rapidement. Les cours sont bien organisés.”'; ?></p>
                        <span><?php echo $isEnglish ? 'Student' : 'Étudiant'; ?></span>
                    </article>
                    <article class="quote">
                        <p><?php echo $isEnglish ? '“I can see the progress and pick up easily. It motivates me to keep going.”' : '“Je vois la progression et je reprends facilement. Ça motive à continuer.”'; ?></p>
                        <span><?php echo $isEnglish ? 'Premium learner' : 'Apprenant premium'; ?></span>
                    </article>
                    <article class="quote">
                        <p><?php echo $isEnglish ? '“The task board is useful to stay consistent every week.”' : '“Le tableau des tâches est pratique pour rester régulier chaque semaine.”'; ?></p>
                        <span><?php echo $isEnglish ? 'Learner' : 'Apprenant'; ?></span>
                    </article>
                </div>
            </div>
        </section>

        <section class="section section-alt" id="tarifs">
            <div class="container">
                <div class="section-head">
                    <div>
                        <h2><?php echo $isEnglish ? 'Simple pricing' : 'Tarifs simples'; ?></h2>
                        <p class="lead"><?php echo $isEnglish ? 'Start free, then move premium when you are ready.' : 'Commencez gratuitement, puis passez en premium quand vous êtes prêt.'; ?></p>
                    </div>
                </div>

                <div class="pricing">
                    <article class="price-card">
                        <div class="price-top">
                            <h3><?php echo $isEnglish ? 'Free' : 'Gratuit'; ?></h3>
                            <div class="price">0<span>DT</span></div>
                        </div>
                        <ul class="price-list">
                            <li><span class="check" aria-hidden="true">✓</span><span><?php echo $isEnglish ? 'Access to free courses' : 'Accès aux cours gratuits'; ?></span></li>
                            <li><span class="check" aria-hidden="true">✓</span><span><?php echo $isEnglish ? 'Progress tracking' : 'Suivi de progression'; ?></span></li>
                            <li><span class="check" aria-hidden="true">✓</span><span><?php echo $isEnglish ? 'Support and requests' : 'Support &amp; réclamation'; ?></span></li>
                        </ul>
                        <a href="pages/registre.php" class="btn btn-soft btn-block"><?php echo $isEnglish ? 'Get started' : 'Démarrer'; ?></a>
                    </article>

                    <article class="price-card price-highlight">
                        <div class="price-ribbon"><?php echo $isEnglish ? 'Most chosen' : 'Le plus choisi'; ?></div>
                        <div class="price-top">
                            <h3><?php echo $isEnglish ? 'Premium' : 'Premium'; ?></h3>
                            <div class="price"><?php echo $isEnglish ? 'A la carte' : 'À la carte'; ?><span>DT</span></div>
                        </div>
                        <ul class="price-list">
                            <li><span class="check" aria-hidden="true">✓</span><span><?php echo $isEnglish ? 'Access to premium courses' : 'Accès aux cours premium'; ?></span></li>
                            <li><span class="check" aria-hidden="true">✓</span><span><?php echo $isEnglish ? 'Extended content and exercises' : 'Contenu et exercices étendus'; ?></span></li>
                            <li><span class="check" aria-hidden="true">✓</span><span><?php echo $isEnglish ? 'Priority on requests' : 'Priorité sur les demandes'; ?></span></li>
                        </ul>
                        <a href="pages/offres.php" class="btn btn-primary btn-block"><?php echo $isEnglish ? 'See offers' : 'Voir les offres'; ?></a>
                    </article>

                    <article class="price-card">
                        <div class="price-top">
                            <h3><?php echo $isEnglish ? 'Teacher' : 'Professeur'; ?></h3>
                            <div class="price"><?php echo $isEnglish ? 'Pro area' : 'Espace pro'; ?></div>
                        </div>
                        <ul class="price-list">
                            <li><span class="check" aria-hidden="true">✓</span><span><?php echo $isEnglish ? 'Manage your courses' : 'Gérer vos cours'; ?></span></li>
                            <li><span class="check" aria-hidden="true">✓</span><span><?php echo $isEnglish ? 'Track learners' : 'Suivre vos apprenants'; ?></span></li>
                            <li><span class="check" aria-hidden="true">✓</span><span><?php echo $isEnglish ? 'Simplified administration' : 'Administration simplifiée'; ?></span></li>
                        </ul>
                        <a href="../../professeur/login.php" class="btn btn-soft btn-block"><?php echo $isEnglish ? 'Access' : 'Accéder'; ?></a>
                    </article>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="cta">
                    <div>
                        <h2><?php echo $isEnglish ? 'Ready to get started?' : 'Prêt à commencer ?'; ?></h2>
                        <p><?php echo $isEnglish ? 'Quick registration, then you can start learning immediately.' : 'Inscription rapide, puis vous pouvez apprendre immédiatement.'; ?></p>
                    </div>
                    <div class="hero-actions">
                        <a href="pages/registre.php" class="btn btn-primary"><?php echo $isEnglish ? 'Start with Enjah' : 'Démarrer avec Enjah'; ?></a>
                        <a href="pages/login.php" class="btn btn-ghost"><?php echo $isEnglish ? 'Log in' : 'Se connecter'; ?></a>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="faq">
            <div class="container">
                <h2>FAQ</h2>
                <p class="lead"><?php echo $isEnglish ? 'Answers to the most common questions.' : 'Les réponses aux questions les plus fréquentes.'; ?></p>
                <div class="faq">
                    <details class="faq-item">
                        <summary><?php echo $isEnglish ? 'How does progress work?' : 'Comment fonctionne la progression ?'; ?></summary>
                        <p><?php echo $isEnglish ? 'When you finish a lesson, it is marked done and the course progress updates.' : 'Quand vous terminez une leçon, elle est marquée comme faite et la progression du cours se met à jour.'; ?></p>
                    </details>
                    <details class="faq-item">
                        <summary><?php echo $isEnglish ? 'Can I start for free?' : 'Puis-je commencer gratuitement ?'; ?></summary>
                        <p><?php echo $isEnglish ? 'Yes. Create an account, access free courses, and activate premium only if you need it.' : 'Oui. Créez un compte, accédez aux cours gratuits et activez le premium uniquement si vous en avez besoin.'; ?></p>
                    </details>
                    <details class="faq-item">
                        <summary><?php echo $isEnglish ? 'What if I have a problem?' : 'Et si j’ai un problème ?'; ?></summary>
                        <p><?php echo $isEnglish ? 'You can send a request from your student space. Our support will answer as soon as possible.' : 'Vous pouvez envoyer une réclamation depuis votre espace étudiant. Notre support vous répondra au plus vite.'; ?></p>
                    </details>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <?php echo $isEnglish ? '© 2026 Enjah · Learning platform · All rights reserved.' : '© 2026 Enjah · Plateforme d’apprentissage · Tous droits réservés.'; ?>
        </div>
    </footer>

    <script>
        (function () {
            var badgeLinks = Array.prototype.slice.call(document.querySelectorAll('.hero-badges a[href^="#"]'));
            if (!badgeLinks.length) return;

            var byId = new Map();
            badgeLinks.forEach(function (link) {
                var id = (link.getAttribute('href') || '').slice(1);
                var target = id ? document.getElementById(id) : null;
                if (target) byId.set(id, { link: link, target: target });
            });

            var setActive = function (id) {
                badgeLinks.forEach(function (link) { link.classList.remove('active'); });
                var item = byId.get(id);
                if (item) item.link.classList.add('active');
            };

            badgeLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    var id = (link.getAttribute('href') || '').slice(1);
                    if (id) setActive(id);
                });
            });

            if (!('IntersectionObserver' in window)) return;
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting && entry.target && entry.target.id) {
                        setActive(entry.target.id);
                    }
                });
            }, { root: null, threshold: 0.35 });

            byId.forEach(function (item) { io.observe(item.target); });
            setActive(badgeLinks[0].getAttribute('href').slice(1));
        })();
    </script>
    <?php
    $chatUserId = $_SESSION['CIN'] ?? null;
    $chatNom = $_SESSION['nom'] ?? '';
    $chatPrenom = $_SESSION['prenom'] ?? '';
    $chatWidgetPath = __DIR__ . '/../../admin/chat_widget.php';

    if ($chatUserId !== null && file_exists($chatWidgetPath)) {
        $chat_user_id = $chatUserId;
        $chat_user_type = 'student';
        $chat_user_name = trim($chatNom . ' ' . $chatPrenom);
        require_once $chatWidgetPath;
    }
    ?>
</body>
</html>
