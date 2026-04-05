<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'elearning');

if (!isset($_GET['id'])) {
    die("ID de leçon manquant.");
}

$id_lecon = intval($_GET['id']);
$res = $conn->query("SELECT * FROM lecon WHERE id_lecon = $id_lecon");
$lecon = $res->fetch_assoc();

if (!$lecon) {
    die("Leçon introuvable.");
}

$chemin = "uploads/" . $lecon['nom_fichier'];
$type = $lecon['type_fichier'];

// Détermination de la classe dynamique AVANT le HTML
$mode_class = "mode-empty";
if (strpos($type, 'video') !== false) {
    $mode_class = "mode-video";
} elseif (strpos($type, 'pdf') !== false) {
    $mode_class = "mode-pdf";
}
elseif (strpos($type, 'audio') !== false || strpos($type, 'mpeg') !== false) {
    $mode_class = "mode-audio"; // Nouvelle classe pour le MP3
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($lecon['titre']); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="visualisation.css">
    <style>
 /* --- Variables de Thème Enjah --- */
:root {
    --primary-blue: #4d68e1;      /* Bleu Royal */
    --slate-dark: #1e293b;        /* Bleu Ardoise */
    --white: #ffffff;
    --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.15);
}

/* --- Mise en page du conteneur --- */
.viewer-container {
    max-width: 1100px;
    margin: 20px auto;
    background: var(--white);
    border-radius: 24px; /* Arrondi signature */
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

/* --- ZONE MÉDIA COMMUNE --- */
.media-box {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    min-height: 500px !important;
    width: 100% !important;
    transition: all 0.3s ease;
}

/* --- 1. MODE VIDÉO (Cinéma) --- */
.media-box.mode-video {
    background: #0f172a !important; /* Ardoise sombre */
    padding: 0 !important;
}

.media-box.mode-video video {
    width: 100% !important;
    max-height: 80vh !important;
}

/* --- 2. MODE PDF (Épuré) --- */
.media-box.mode-pdf {
    background: #f1f5f9 !important; /* Gris clair */
    padding: 40px !important;
}

.pdf-viewer {
    width: 95% !important; /* Correction du bug de petite taille */
    height: 80vh !important;
    border-radius: 12px;
    background: white;
    border: 8px solid white !important; /* Bordure épaisse élégante */
    box-shadow: var(--shadow-lg) !important;
}

/* --- 3. MODE AUDIO (MP3) --- */
.media-box.mode-audio {
    background: linear-gradient(135deg, #1e293b, #4d68e1) !important; /* Dégradé Enjah */
    flex-direction: column !important;
    padding: 60px !important;
}

.audio-player-wrapper {
    width: 100% !important;
    max-width: 500px !important; /* Fix pour éviter le petit point noir */
    text-align: center;
}

.audio-icon {
    font-size: 80px;
    color: white;
    margin-bottom: 20px;
    animation: pulse 2s infinite ease-in-out;
}

.mode-audio audio {
    width: 100% !important; /* Force la largeur du bouton */
    height: 50px;
    filter: invert(1) brightness(1.5); /* Rend le lecteur blanc sur fond bleu */
    border-radius: 30px;
}

/* --- 4. MODE VIDE / ERREUR --- */
.media-box.mode-empty {
    background: #fff5f5 !important;
    padding: 50px !important;
}

.empty-state {
    text-align: center;
    border: 2px dashed #feb2b2; /* Cadre pointillé rouge */
    padding: 40px;
    border-radius: 20px;
    color: #c53030;
}

/* --- ANIMATIONS --- */
@keyframes pulse {
    0% { transform: scale(1); opacity: 0.8; }
    50% { transform: scale(1.1); opacity: 1; }
    100% { transform: scale(1); opacity: 0.8; }
}

/* --- BOUTONS ET INFOS --- */
.media-info {
    padding: 30px 40px;
}

.btn-download {
    display: inline-block;
    background: var(--primary-blue) !important;
    color: white !important;
    padding: 12px 25px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: bold;
    margin-top: 15px;
    box-shadow: 0 4px 15px rgba(77, 104, 225, 0.3);
}
.media-box.mode-video {
    background: #0f172a !important; /* Ardoise sombre Enjah */
    min-height: 500px !important;
    display: flex !important;
    justify-content: center !important; /* Centrage horizontal */
    align-items: center !important;     /* Centrage vertical */
    padding: 20px !important;
}

/* 2. LE LECTEUR VIDÉO LUI-MÊME (The Fix) */
.mode-video video {
    width: 90% !important;  /* Force la largeur de la vidéo */
    max-width: 900px !important; /* Mais pas plus grande que cette taille pour la qualité */
    height: auto !important; /* Respecte le ratio de la vidéo */
    max-height: 80vh !important; /* Évite qu'elle ne prenne toute la hauteur de l'écran */
    
    /* Transition pour l'interactivité */
    transition: all 0.3s ease !important;
    cursor: pointer !important;
}

/* 3. Interaction au survol du lecteur */
.media-box.mode-video:hover video {
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5) !important; /* Ombre de relief au survol */
}

    </style>

</head>
<body style="background-color: #f4f7f6;">

<div class="viewer-container">
    <div class="back-nav">
        <a href="javascript:history.back()" class="back-link">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour aux leçons
        </a>
    </div>

    <div class="media-box <?php echo $mode_class; ?>">
        <?php if ($mode_class === "mode-video"): ?>
            <video controls autoplay>
                <source src="<?php echo $chemin; ?>" type="<?php echo $type; ?>">
                Votre navigateur ne supporte pas la vidéo.
            </video>

        <?php elseif ($mode_class === "mode-pdf"): ?>
            <embed src="<?php echo $chemin; ?>#toolbar=0" type="application/pdf" class="pdf-viewer" />
            <?php elseif ($mode_class === "mode-audio"): ?>
    <div class="audio-player-wrapper">
        <div class="audio-icon">♫</div>
        <audio controls>
            <source src="<?php echo $chemin; ?>" type="<?php echo $type; ?>">
            Votre navigateur ne supporte pas la lecture audio.
        </audio>
    </div>

        <?php else: ?>
            <div class="empty-state">
                <div class="icon-warning">⚠️</div>
                <p>Aperçu indisponible pour ce format.</p>
                <a href="<?php echo $chemin; ?>" download class="btn-dl">Télécharger le fichier</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="media-info">
        <h2><?php echo htmlspecialchars($lecon['titre']); ?></h2>
        <p><?php echo htmlspecialchars($lecon['description']); ?></p>
        
        <?php if ($mode_class === "mode-pdf" || strpos($type, 'octet-stream') !== false): ?>
            <a href="<?php echo $chemin; ?>" download class="btn-download">
                Télécharger le document
            </a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>