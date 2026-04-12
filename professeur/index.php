<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enjah Professeur - Plateforme Enseignant</title>
    <meta name="description" content="Enjah Professeur: creez vos cours, structurez vos lecons, et pilotez vos resultats avec une experience moderne et claire.">
    <link rel="stylesheet" href="landing.css?v=20260405-3">
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <a class="brand" href="index.php" aria-label="Accueil professeur">
                <img src="enjah.png" alt="Logo Enjah">
                <span>Enjah Professeur</span>
            </a>

            <nav class="nav-links" aria-label="Navigation professeur">
                <a href="#produit">Produit</a>
                <a href="#methode">Methode</a>
                <a href="#avis">Avis</a>
                <a href="#impact">Impact</a>
            </nav>

            <div class="top-actions">
                <a href="../index.php" class="btn btn-soft">Hub projet</a>
                <a href="login.php" class="btn btn-ghost">Connexion</a>
                <a href="registre.php" class="btn btn-primary">Creer un compte</a>
                <a href="../kmr/student/index.php" class="btn btn-soft">Espace Etudiant</a>
                <a href="../admin/login.php" class="btn btn-soft">Espace Admin</a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero" id="top">
            <div class="container hero-grid">
                <div class="hero-left">
                    <div class="hero-badges" aria-label="Points forts professeur">
                        <a class="pill pill-brand" href="#produit">Creation de cours</a>
                        <a class="pill" href="#methode">Lecons structurees</a>
                        <a class="pill" href="#impact">Pilotage d'impact</a>
                    </div>

                    <p class="hero-kicker">Plateforme Enseignant</p>
                    <h1>Lancez des cours professionnels et transformez votre expertise en impact durable.</h1>
                    <p class="hero-subtitle">
                        Enjah Professeur centralise vos contenus, vos lecons, et votre progression pedagogique dans un espace
                        visuel, fluide et oriente resultats.
                    </p>
                    <div class="hero-actions">
                        <a href="registre.php" class="btn btn-primary">Creer mon espace enseignant</a>
                        <a href="login.php" class="btn btn-ghost">J'ai deja un compte</a>
                    </div>

                    <div class="trustbar" aria-label="Resume plateforme">
                        <div class="trust-item">
                            <strong>Edition</strong>
                            <span>cours premium et gratuits</span>
                        </div>
                        <div class="trust-item">
                            <strong>Lecons</strong>
                            <span>organisation claire par etapes</span>
                        </div>
                        <div class="trust-item">
                            <strong>Pilotage</strong>
                            <span>vision globale de votre activite</span>
                        </div>
                    </div>
                </div>

                <div class="hero-right" aria-label="Apercu professeur">
                    <figure class="hero-shot">
                        <img src="../kmr/media/photo.png" alt="Apercu de la plateforme Enjah Professeur">
                    </figure>
                </div>
            </div>
        </section>

        <section class="section section-alt" id="produit">
            <div class="container">
                <div class="section-head">
                    <div>
                        <h2>Tout ce qu'il faut pour enseigner avec precision</h2>
                        <p class="lead">Une interface claire pour publier, structurer et faire evoluer votre catalogue de cours.</p>
                    </div>
                    <a href="login.php" class="btn btn-soft">Acceder a mon espace</a>
                </div>

                <div class="feature-grid">
                    <article class="feature feature-accent">
                        <div class="feature-top">
                            <span class="feature-icon" aria-hidden="true">CO</span>
                            <h3>Creation de cours</h3>
                        </div>
                        <p>Nom, categorie, prix et image: publiez une offre lisible et professionnelle en quelques minutes.</p>
                    </article>

                    <article class="feature">
                        <div class="feature-top">
                            <span class="feature-icon" aria-hidden="true">LE</span>
                            <h3>Gestion des lecons</h3>
                        </div>
                        <p>Ajoutez vos lecons par sequence pour guider les etudiants sans confusion.</p>
                    </article>

                    <article class="feature">
                        <div class="feature-top">
                            <span class="feature-icon" aria-hidden="true">SU</span>
                            <h3>Suivi pedagogique</h3>
                        </div>
                        <p>Gardez une vue continue sur vos cours et ajustez votre contenu en fonction des besoins.</p>
                    </article>

                    <article class="feature">
                        <div class="feature-top">
                            <span class="feature-icon" aria-hidden="true">ID</span>
                            <h3>Identite professionnelle</h3>
                        </div>
                        <p>Mettez a jour vos informations pour renforcer la confiance de vos apprenants.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="methode">
            <div class="container">
                <h2>Methode Enjah Professeur</h2>
                <p class="lead">Un cycle simple pour produire des cours solides, visibles et utiles.</p>

                <div class="timeline">
                    <div class="timeline-step">
                        <div class="timeline-badge">1</div>
                        <div class="timeline-body">
                            <strong>Definir l'offre de cours</strong>
                            <p>Cadrez l'objectif, le format et la promesse pedagogique.</p>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-badge">2</div>
                        <div class="timeline-body">
                            <strong>Structurer les lecons</strong>
                            <p>Organisez la progression en modules simples et actionnables.</p>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-badge">3</div>
                        <div class="timeline-body">
                            <strong>Publier et iterer</strong>
                            <p>Ameliorez en continu la qualite de votre parcours selon les retours.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="avis">
            <div class="container">
                <h2>Retours de professeurs</h2>
                <p class="lead">Une plateforme concue pour gagner du temps et augmenter la qualite d'enseignement.</p>
                <div class="testimonials">
                    <article class="quote">
                        <p>"J'ai lance mon cours en peu de temps. L'interface est propre et facile a prendre en main."</p>
                        <span>Professeur en developpement web</span>
                    </article>
                    <article class="quote">
                        <p>"Le suivi de mes contenus est plus simple, je peux me concentrer sur la pedagogie."</p>
                        <span>Formateur independant</span>
                    </article>
                    <article class="quote">
                        <p>"Mes etudiants trouvent les lecons plus claires et la progression est beaucoup plus fluide."</p>
                        <span>Coach digital</span>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="impact">
            <div class="container cta">
                <div>
                    <h2>Pret a lancer votre prochain parcours d'excellence ?</h2>
                    <p>Rejoignez Enjah Professeur et donnez une nouvelle dimension a votre impact pedagogique.</p>
                </div>
                <div class="hero-actions">
                    <a href="registre.php" class="btn btn-primary">Creer mon espace professeur</a>
                    <a href="login.php" class="btn btn-ghost">Se connecter</a>
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
