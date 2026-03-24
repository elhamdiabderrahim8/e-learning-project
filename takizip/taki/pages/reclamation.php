<?php

declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$error = get_flash('error');
$success = get_flash('success');
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reclamation - Enjah</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../media/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../media/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../media/favicon_io/favicon-16x16.png">
    <link rel="shortcut icon" href="../media/favicon_io/favicon.ico">
    <link rel="manifest" href="../media/favicon_io/site.webmanifest">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo"><img src="../media/logo.jpg" alt="Logo Enjah"><span>Enjah</span></div>
            <nav>
                <ul>
                    <li><a href="cours.php">Mes Cours</a></li>
                    <li><a href="tache_a_fair.php">Mes Taches</a></li>
                    <li><a href="offres.php">Choisir une offre</a></li>
                    <li><a href="calendrier.php">Calendrier</a></li>
                    <li><a href="certificats.php">Certificats</a></li>
                    <li class="active"><a href="reclamation.php">Reclamation</a></li>
                    <li><a href="profil.php">Mon Profil</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <h1>Envoyer une reclamation</h1>
                <p>Un probleme ? Notre equipe vous repondra sous 24h.</p>
            </header>

            <?php if ($error): ?>
                <p style="color: #b42318;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p style="color: #027a48;"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <div class="login-box" style="max-width: 600px; margin: 0 auto;">
                <form class="modal-form" action="../backend/actions/create_reclamation.php" method="post" enctype="multipart/form-data">
                    <div class="input-group">
                        <label for="subject">Sujet de la reclamation</label>
                        <select id="subject" name="subject" style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--grey-border);" required>
                            <option value="Probleme d acces au cours">Probleme d acces au cours</option>
                            <option value="Erreur de paiement / Facturation">Erreur de paiement / Facturation</option>
                            <option value="Bug technique sur la plateforme">Bug technique sur la plateforme</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="message">Description detaillee</label>
                        <textarea id="message" name="message" rows="6" placeholder="Expliquez votre probleme ici..." style="width: 100%; padding: 1rem; border-radius: 10px; border: 1px solid var(--grey-border); outline: none; font-family: inherit; resize: vertical;" required></textarea>
                    </div>

                    <div class="input-group">
                        <label for="attachment">Piece jointe (facultatif)</label>
                        <input type="file" id="attachment" name="attachment" style="border: none; padding: 0;">
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; text-align: center;">Envoyer la reclamation</button>
                </form>
            </div>
        </main>
    </div>
    </body>
</html>


