<?php
session_start();
require_once __DIR__ . '/backend/config/database.php';
$pdo = db();

$code = $_GET['code'] ?? '';

if (empty($code)) {
    die("Code de certificat manquant.");
}

$sql = "SELECT 
            e.nom AS nom_etudiant, e.prenom AS prenom_etudiant,
            p.nom AS nom_prof, p.prenom AS prenom_prof,
            c.nom_cours,
            cert.date_obtention,
            cert.code_verification
        FROM certificaton cert
        JOIN etudiant e ON cert.id_etudiant = e.CIN
        JOIN professeur p ON cert.id_professeur = p.CIN
        JOIN cours c ON cert.id_cours = c.id
        WHERE cert.code_verification = :code";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['code' => $code]);
    $data = $stmt->fetch();

    if (!$data) {
        die("Certificat introuvable.");
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

$date_fr = date('d/m/Y', strtotime($data['date_obtention']));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat - <?php echo htmlspecialchars($data['code_verification']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="certificat/certificat.css">
    <link rel="stylesheet" href="certificat/style_certificat.css">
    
    <style>
        /* Styles pour l'écran */
        .btn-print {
            position: fixed; top: 20px; right: 20px;
            background: #b8860b; color: white;
            padding: 12px 25px; border: none; border-radius: 30px;
            cursor: pointer; font-weight: bold; z-index: 1000;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: transform 0.2s;
        }
        .btn-print:hover { transform: scale(1.05); background: #946c09; }

        /* CONFIGURATION IMPRESSION */
        @media print {
            @page {
                size: A4 landscape; /* Force le mode paysage */
                margin: 0; /* Supprime les marges par défaut du navigateur */
            }
            body {
                margin: 0;
                -webkit-print-color-adjust: exact !important; /* Force Chrome/Safari à imprimer les couleurs/images */
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .certificate-container {
                width: 100vw;
                height: 100vh;
                margin: 0;
                border: none;
                box-shadow: none;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <button class="btn-print no-print" onclick="window.print()">
        🖨️ Imprimer / Sauvegarder en PDF
    </button>

    <div class="certificate-container">
        <div class="certificate-background"></div>

        <div class="header">
            <img src="enjah.png" alt="Logo Enjah" class="logo-enjah">
            <h1 class="platform-name">E-LEARNING PLATFORM</h1>
        </div>

        <div class="content">
            <h2 class="main-title">CERTIFICAT DE RÉUSSITE</h2>
            <p class="award-text">Décerné avec fierté à :</p>
            
            <p class="student-name"><?php echo htmlspecialchars($data['prenom_etudiant'] . " " . $data['nom_etudiant']); ?></p>
            
            <p class="accomplishment-text">Pour avoir terminé avec succès le cours :</p>
            
            <p class="course-name"><?php echo htmlspecialchars(strtoupper($data['nom_cours'])); ?></p>
            
            <p class="course-details">Publié par le Professeur : <span class="prof-name"><?php echo htmlspecialchars($data['prenom_prof'] . " " . $data['nom_prof']); ?></span></p>
        </div>

        <div class="footer">
            <div class="signature-block">
                <p class="date">Fait le : <span class="date-value"><?php echo $date_fr; ?></span></p>
                <p class="signature-title">Référence du Diplôme</p>
                <p style="font-family: monospace; font-size: 12px; font-weight: bold; color: #b8860b; margin-top: 5px;">
                    ID: <?php echo htmlspecialchars($data['code_verification']); ?>
                </p>
                <div class="signature-line"></div>
            </div>

            <div class="signature-block" style="position: relative;">
                <div style="position: absolute; top: -90px; left: 50%; transform: translateX(-50%); width: 130px; z-index: 10;">
                    <img src="certificat/tampon.php?nom=<?php echo urlencode($data['prenom_prof'] . ' ' . $data['nom_prof']); ?>" 
                         alt="Tampon Officiel" 
                         style="width: 100%; height: auto; display: block; filter: multiply(1.2);">
                </div>
                
                <p class="signature-title" style="margin-top: 60px;">Signature & Tampon du Professeur</p>
                <div class="signature-line" style="border-top: 2px solid #b8860b;"></div>
            </div>
        </div>

        <div class="gold-border"></div>
    </div>

</body>
</html>