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
               c.image_type, c.image_data,
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
                        $image_src = 'data:' . $row['image_type'] . ';base64,' . base64_encode($row['image_data']);
                        $isPremium = ($row['categorie'] ?? '') === 'Premium';
                ?>
                        <div class="course-card">
                             <div class="course-image" style="background-image: url('<?php echo $image_src; ?>'); position: relative;">
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
                                    <button type="button" class="btn-primary" onclick="ouvrirModalPaiement(<?php echo (int) $row['id_cours']; ?>)" style="width:100%;">
                                        Payer
                                    </button>
                                <?php else: ?>
                                    <form action="../backend/actions/enroll_free_course.php" method="post">
                                        <input type="hidden" name="course_id" value="<?php echo (int) $row['id_cours']; ?>">
                                        <button type="button" class="btn-primary" onclick="ouvrirModalInscriptionFree(<?php echo (int) $row['id_cours']; ?>, '<?php echo htmlspecialchars((string) $row['nom_cours'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars((string) ($row['nom_prof'] . ' ' . $row['prenom_prof']), ENT_QUOTES, 'UTF-8'); ?>')" style="width:100%;">
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

    <div id="modal-paiement-global" class="modal-overlay">
        <div class="modal-content">
            <h2 style="margin-top:0;"><?php echo $isEnglish ? 'Secure payment' : 'Paiement sécurisé'; ?></h2>
            <form action="../backend/actions/traitter_payement(a).php" method="POST">
                <input type="hidden" id="input_id_cours_final" name="course_id">

                <label><?php echo $isEnglish ? 'Name on card' : 'Nom sur la carte'; ?></label>
                <input type="text" placeholder="M. JEAN DUPONT">
                <label><?php echo $isEnglish ? 'Card number' : 'Numéro de carte'; ?></label>
                <input type="text" placeholder="4532 •••• •••• ••••">
                <div class="input-row">
                    <input type="text" placeholder="MM/AA">
                    <input type="text" placeholder="CVC">
                </div>

                <button type="submit" style="width:100%; background:#20c997; color:white; border:none; padding:12px; margin-top:20px; cursor:pointer; border-radius:5px; font-weight:bold;">
                    <?php echo $isEnglish ? 'Confirm purchase' : 'Confirmer l\'achat'; ?>
                </button>
                <button type="button" onclick="fermerModal()" style="width:100%; background:none; border:none; color:#666; margin-top:10px; cursor:pointer;"><?php echo $isEnglish ? 'Cancel' : 'Annuler'; ?></button>
            </form>
        </div>
    </div>

    <div id="modal-free-enroll" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <h2 style="margin-top:0;"><?php echo $isEnglish ? 'Free course enrollment' : 'Inscription au cours gratuit'; ?></h2>
            <p style="margin-top:6px; color:#64748b; font-weight:700;">
                <?php echo $isEnglish ? 'This request will be visible to the teacher (name, email).' : 'Cette demande sera visible par le professeur (nom, prénom, email).'; ?>
            </p>

            <form action="../backend/actions/enroll_free_course.php" method="post">
                <input type="hidden" id="free_course_id" name="course_id" value="">
                <input type="hidden" id="free_course_name" name="course_name" value="">
                <input type="hidden" id="free_prof_name" name="prof_name" value="">

                <div class="input-row">
                    <div class="input-group">
                        <label><?php echo $isEnglish ? 'First name' : 'Prénom'; ?></label>
                        <input type="text" value="<?php echo htmlspecialchars((string) ($student['prenom'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" readonly>
                    </div>
                    <div class="input-group">
                        <label><?php echo $isEnglish ? 'Last name' : 'Nom'; ?></label>
                        <input type="text" value="<?php echo htmlspecialchars((string) ($student['nom'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" readonly>
                    </div>
                </div>

                <div class="input-group">
                    <label>Email</label>
                    <input type="text" value="<?php echo htmlspecialchars((string) ($student['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" readonly>
                </div>

                <div class="input-group">
                    <label for="free_message"><?php echo $isEnglish ? 'Message (optional)' : 'Message (optionnel)'; ?></label>
                    <textarea id="free_message" name="message" rows="4" placeholder="<?php echo $isEnglish ? 'Why are you interested in this course?' : 'Pourquoi ce cours vous intéresse ?'; ?>"></textarea>
                </div>

                <button type="submit" class="btn-primary" style="width:100%;"><?php echo $isEnglish ? 'Send & enroll' : 'Envoyer & s\'inscrire'; ?></button>
                <button type="button" onclick="fermerModalFree()" style="width:100%; background:none; border:none; color:#666; margin-top:10px; cursor:pointer;"><?php echo $isEnglish ? 'Cancel' : 'Annuler'; ?></button>
            </form>
        </div>
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

    <script>
    // Cette fonction s'exécute dès que la page est chargée
    window.addEventListener('load', function() {
        // 1. On vérifie si l'URL contient #step-success
        // 2. OU si le paramètre paiement=success est présent
        const urlParams = new URLSearchParams(window.location.search);
        
        if (window.location.hash === '#step-success' || urlParams.get('paiement') === 'success') {
            const modalSuccess = document.getElementById('step-success');
            if (modalSuccess) {
                modalSuccess.style.display = 'flex'; // On affiche le modal
            }
        }
    });

    function ouvrirModalPaiement(id) {
        document.getElementById('input_id_cours_final').value = id;
        document.getElementById('modal-paiement-global').style.display = 'flex';
    }

    function fermerModal() {
        document.getElementById('modal-paiement-global').style.display = 'none';
        // On cache aussi le modal de succès si on clique sur fermer
        document.getElementById('step-success').style.display = 'none';
    }

    function ouvrirModalInscriptionFree(id, courseName, profName) {
        document.getElementById('free_course_id').value = id;
        document.getElementById('free_course_name').value = courseName || '';
        document.getElementById('free_prof_name').value = profName || '';
        document.getElementById('modal-free-enroll').style.display = 'flex';
    }

    function fermerModalFree() {
        document.getElementById('modal-free-enroll').style.display = 'none';
        var message = document.getElementById('free_message');
        if (message) message.value = '';
    }
</script>
</body>
</html>
