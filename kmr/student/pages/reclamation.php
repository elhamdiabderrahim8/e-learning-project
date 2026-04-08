<?php

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$error = get_flash('error');
$success = get_flash('success');
$isEnglish = current_language() === 'en';
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
        <?php $active = 'reclamation'; require __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <h1><?php echo $isEnglish ? 'Send a support request' : 'Envoyer une reclamation'; ?></h1>
                <p><?php echo $isEnglish ? 'Having an issue? Our team will reply within 24h.' : 'Un probleme ? Notre equipe vous repondra sous 24h.'; ?></p>
            </header>

            <?php if ($error): ?>
                <div class="reclamation-alert reclamation-alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="reclamation-alert reclamation-alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <section class="reclamation-shell">
                <div class="reclamation-intro card">
                    <h2><?php echo $isEnglish ? 'Student Support' : 'Support Etudiant'; ?></h2>
                    <p><?php echo $isEnglish ? 'Fill out this form and attach files if needed. Our team will review your request quickly.' : 'Remplissez ce formulaire et joignez un fichier si necessaire. Notre equipe analysera votre demande rapidement.'; ?></p>
                    <ul class="reclamation-points">
                        <li><?php echo $isEnglish ? 'Reply within 24 business hours' : 'Reponse sous 24h ouvrables'; ?></li>
                        <li><?php echo $isEnglish ? 'Personalized follow-up' : 'Suivi personnalise de votre demande'; ?></li>
                        <li><?php echo $isEnglish ? 'Your information stays confidential' : 'Confidentialite de vos informations'; ?></li>
                    </ul>
                </div>

                <div class="reclamation-form-card card">
                    <form class="reclamation-form" action="../backend/actions/create_reclamation.php" method="post" enctype="multipart/form-data">
                        <div class="input-group">
                            <label for="subject"><?php echo $isEnglish ? 'Request subject' : 'Sujet de la reclamation'; ?></label>
                            <select id="subject" name="subject" required>
                                <option value="Probleme d acces au cours"><?php echo $isEnglish ? 'Course access issue' : 'Probleme d acces au cours'; ?></option>
                                <option value="Erreur de paiement / Facturation"><?php echo $isEnglish ? 'Payment / billing error' : 'Erreur de paiement / Facturation'; ?></option>
                                <option value="Bug technique sur la plateforme"><?php echo $isEnglish ? 'Platform technical bug' : 'Bug technique sur la plateforme'; ?></option>
                                <option value="Autre"><?php echo $isEnglish ? 'Other' : 'Autre'; ?></option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="message"><?php echo $isEnglish ? 'Detailed description' : 'Description detaillee'; ?></label>
                            <textarea id="message" name="message" rows="7" placeholder="<?php echo $isEnglish ? 'Explain your issue clearly...' : 'Expliquez votre probleme de maniere claire...'; ?>" required></textarea>
                            <p class="input-help"><?php echo $isEnglish ? 'Add the steps that triggered the issue to speed up resolution.' : 'Ajoutez les etapes qui ont provoque le probleme pour accelerer la resolution.'; ?></p>
                        </div>

                        <div class="input-group">
                            <label for="attachments"><?php echo $isEnglish ? 'Attachments (optional)' : 'Pieces jointes (facultatif)'; ?></label>
                            <input type="file" id="attachments" name="attachments[]" multiple>
                            <p class="input-help"><?php echo $isEnglish ? 'Recommended formats: PDF, PNG, JPG. Max size: 5 MB per file.' : 'Formats recommandes: PDF, PNG, JPG. Taille maximale: 5 Mo par fichier.'; ?></p>
                        </div>

                        <input type="submit" class="btn-primary reclamation-submit" value="<?php echo $isEnglish ? 'Send request' : 'Envoyer la reclamation'; ?>">
                    </form>
                </div>
            </section>
        </main>
    </div>
    </body>
</html>


