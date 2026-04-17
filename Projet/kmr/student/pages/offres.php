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

$pdo->exec("ALTER TABLE inscription ADD COLUMN IF NOT EXISTS paiement_valide TINYINT(1) NOT NULL DEFAULT 0");
$pdo->exec("ALTER TABLE inscription ADD COLUMN IF NOT EXISTS payment_status_note VARCHAR(255) NULL");

// Offres = cours non encore inscrits (Free + Premium).
$sql = "SELECT c.id AS id_cours, c.nom_cours, c.prix, c.categorie,
               p.nom AS nom_prof, p.prenom AS prenom_prof
        FROM cours c
        INNER JOIN professeur p ON c.id_professeur = p.CIN
        LEFT JOIN inscription i
          ON i.id_cours = c.id AND i.id_etudiant = :cin
                WHERE i.id_inscription IS NULL
                     OR (i.methode_paiement = 'Carte Bancaire' AND i.paiement_valide IN (0, -1))
        ORDER BY c.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['cin' => $cin_etudiant]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statusStmt = $pdo->prepare(
    "SELECT id_cours, paiement_valide, COALESCE(payment_status_note, '') AS payment_status_note
     FROM inscription
     WHERE id_etudiant = :cin AND methode_paiement = 'Carte Bancaire'"
);
$statusStmt->execute(['cin' => $cin_etudiant]);
$paymentStatuses = [];
foreach ($statusStmt->fetchAll(PDO::FETCH_ASSOC) as $st) {
    $paymentStatuses[(int) $st['id_cours']] = [
        'paiement_valide' => (int) ($st['paiement_valide'] ?? 0),
        'note' => (string) ($st['payment_status_note'] ?? ''),
    ];
}

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
                <?php if (isset($_GET['payment']) && $_GET['payment'] === 'pending'): ?>
                    <div style="margin-top:12px;padding:10px 12px;border-radius:10px;background:#fff7ed;color:#9a3412;font-weight:700;">
                        Paiement envoyé. En attente de validation par l'admin.
                    </div>
                <?php elseif (isset($_GET['payment']) && $_GET['payment'] === 'invalid'): ?>
                    <div style="margin-top:12px;padding:10px 12px;border-radius:10px;background:#fef2f2;color:#b91c1c;font-weight:700;">
                        Informations carte invalides. Veuillez vérifier vos données.
                    </div>
                <?php elseif (isset($_GET['payment']) && $_GET['payment'] === 'error'): ?>
                    <div style="margin-top:12px;padding:10px 12px;border-radius:10px;background:#fef2f2;color:#b91c1c;font-weight:700;">
                        Une erreur est survenue lors de l'envoi du paiement.
                    </div>
                <?php endif; ?>
            </header>

            <div class="courses-grid" id="courses-grid">
                <?php
                if (count($courses) > 0) {
                    foreach ($courses as $row) {
                        $courseId = (int) $row['id_cours'];
                        $image_src = '../backend/actions/course_image.php?id=' . $courseId;
                        $isPremium = ($row['categorie'] ?? '') === 'Premium';
                        $paymentInfo = $paymentStatuses[$courseId] ?? null;
                        $paymentState = $paymentInfo['paiement_valide'] ?? null;
                        $paymentNote = $paymentInfo['note'] ?? '';
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
                                    <?php if ($paymentState === 0): ?>
                                        <div style="margin-bottom:8px;padding:8px 10px;border-radius:8px;background:#fff7ed;color:#9a3412;font-weight:700;font-size:.88rem;">
                                            En attente de validation admin
                                        </div>
                                        <button type="button" class="btn-primary" style="width:100%;opacity:.7;cursor:not-allowed;" disabled>
                                            En attente
                                        </button>
                                    <?php else: ?>
                                        <?php if ($paymentState === -1): ?>
                                            <div style="margin-bottom:8px;padding:8px 10px;border-radius:8px;background:#fef2f2;color:#b91c1c;font-weight:700;font-size:.88rem;">
                                                Vous avez un problème dans votre carte. <?php echo $paymentNote !== '' ? htmlspecialchars($paymentNote, ENT_QUOTES, 'UTF-8') : ''; ?>
                                            </div>
                                        <?php endif; ?>
                                        <button type="button" class="btn-primary" style="width:100%;" onclick="openCardModal(<?php echo $courseId; ?>)">
                                            <?php echo $paymentState === -1 ? 'Réessayer paiement' : 'Payer'; ?>
                                        </button>
                                    <?php endif; ?>
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

    <div id="card-modal" class="modal-overlay" style="display:none;">
        <div class="modal-content" style="max-width:460px;">
            <h2 style="margin-top:0;">Paiement premium</h2>
            <p style="margin:6px 0 14px;color:#64748b;font-weight:700;">Vos informations seront envoyées à l'admin pour validation.</p>
            <form method="POST" action="../backend/actions/traitter_payement(a).php">
                <input type="hidden" name="course_id" id="payment_course_id" value="">

                <label>Nom sur la carte</label>
                <input type="text" name="card_holder" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;margin:6px 0 12px;">

                <label>Numéro de carte</label>
                <input type="text" name="card_number" inputmode="numeric" minlength="12" maxlength="19" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;margin:6px 0 12px;">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label>Expiration (MM/AA)</label>
                        <input type="text" name="card_expiry" minlength="4" maxlength="5" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;margin-top:6px;">
                    </div>
                    <div>
                        <label>CVC</label>
                        <input type="text" name="card_cvc" inputmode="numeric" minlength="3" maxlength="4" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;margin-top:6px;">
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width:100%;margin-top:14px;">Envoyer pour validation</button>
                <button type="button" onclick="closeCardModal()" style="width:100%;margin-top:8px;background:none;border:none;color:#64748b;cursor:pointer;">Annuler</button>
            </form>
        </div>
    </div>

    <script>
    function openCardModal(courseId) {
        document.getElementById('payment_course_id').value = courseId;
        document.getElementById('card-modal').style.display = 'flex';
    }

    function closeCardModal() {
        document.getElementById('card-modal').style.display = 'none';
    }
    </script>
</body>
</html>
